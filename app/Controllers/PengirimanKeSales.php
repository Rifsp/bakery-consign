<?php

namespace App\Controllers;

use App\Models\PengirimanKeSalesModel;
use App\Models\PengirimanKeSalesDetailModel;
use App\Models\StokGudangModel;
use App\Models\MutasiGudangModel;
use App\Models\StokSalesModel;
use App\Models\MutasiSalesModel;
use App\Models\ProdukModel;
use App\Models\UserModel;

class PengirimanKeSales extends BaseController
{
    protected $pengirimanModel;
    protected $detailModel;

    public function __construct()
    {
        $this->pengirimanModel = new PengirimanKeSalesModel();
        $this->detailModel = new PengirimanKeSalesDetailModel();
        $this->helpers = array_merge($this->helpers, ['form']);
    }

    protected function buildFilterQuery()
    {
        $salesId = $this->request->getGet('sales_id');
        $search = trim($this->request->getGet('search') ?? '');
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');

        $this->pengirimanModel->select('
                pengiriman_ke_sales.*,
                users.nama as sales_nama,
                (SELECT COALESCE(SUM(jumlah),0) FROM pengiriman_ke_sales_detail WHERE pengiriman_ke_sales_id = pengiriman_ke_sales.id) as total_item
            ')
            ->join('users', 'users.id = pengiriman_ke_sales.sales_id', 'left');

        if ($salesId && $salesId !== '') {
            $this->pengirimanModel->where('pengiriman_ke_sales.sales_id', $salesId);
        }
        $this->pengirimanModel->where('pengiriman_ke_sales.tanggal_kirim >=', $tglDari);
        $this->pengirimanModel->where('pengiriman_ke_sales.tanggal_kirim <=', $tglSampai);

        if ($search !== '') {
            $this->pengirimanModel->groupStart()
                ->like('pengiriman_ke_sales.nomor_kirim', $search, 'both', null, true)
                ->orLike('users.nama', $search, 'both', null, true)
                ->groupEnd();
        }

        $this->pengirimanModel->orderBy('pengiriman_ke_sales.id', 'DESC');
    }

    public function index()
    {
        $this->checkAccess();

        $salesId = $this->request->getGet('sales_id');
        $search = $this->request->getGet('search');
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');

        $this->buildFilterQuery();

        $salesModel = new UserModel();
        $salesList = $salesModel->where('role', 'sales')->where('is_aktif', true)->findAll();

        $data = [
            'title' => 'Transfer ke Sales',
            'records' => $this->pengirimanModel->findAll(),
            'salesList' => $salesList,
            'salesId' => $salesId,
            'search' => $search,
            'tglDari' => $tglDari,
            'tglSampai' => $tglSampai,
        ];

        return view('pengiriman_ke_sales/index', $data);
    }

    public function fetch()
    {
        $this->checkAccess();

        $this->buildFilterQuery();

        $records = $this->pengirimanModel->findAll();

        $tableHtml = view('pengiriman_ke_sales/_table_data', ['records' => $records]);

        return $this->response->setJSON([
            'table'      => $tableHtml,
        ]);
    }

    public function create()
    {
        $this->checkAccess();

        $salesModel = new UserModel();
        $produkModel = new ProdukModel();

        $salesList = $salesModel->where('role', 'sales')->where('is_aktif', true)->findAll();
        $produkList = $produkModel->where('is_aktif', true)->findAll();

        $stokGudangModel = new StokGudangModel();
        $stokGudangAll = $stokGudangModel->select('stok_gudang.*, produk.nama as produk_nama, produk.kode_produk')
            ->join('produk', 'produk.id = stok_gudang.produk_id', 'left')
            ->where('stok_gudang.stok_tersedia >', 0)
            ->findAll();

        $data = [
            'title' => 'Buat Transfer ke Sales',
            'salesList' => $salesList,
            'produkList' => $produkList,
            'stokGudangAll' => $stokGudangAll,
        ];

        return view('pengiriman_ke_sales/create', $data);
    }

    public function store()
    {
        $this->checkAccess();

        $validationRules = [
            'sales_id' => 'required|is_natural_no_zero',
            'items' => 'required',
            'tanggal_kirim' => 'required|valid_date',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $salesId = (int) $this->request->getPost('sales_id');
        $tanggalKirim = $this->request->getPost('tanggal_kirim');
        $catatan = $this->request->getPost('catatan');
        $items = $this->request->getPost('items');

        if (!is_array($items) || empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Minimal 1 item produk');
        }

        $userId = session()->get('id');
        $stokGudangModel = new StokGudangModel();
        $stokSalesModel = new StokSalesModel();
        $mutasiGudangModel = new MutasiGudangModel();
        $mutasiSalesModel = new MutasiSalesModel();

        $detailData = [];
        foreach ($items as $item) {
            $produkId = (int) ($item['produk_id'] ?? 0);
            $jumlah = (int) ($item['jumlah'] ?? 0);
            $tglExpired = $item['tgl_expired'] ?? null;
            $hargaBeli = (float) ($item['harga_beli'] ?? 0);

            if (!$produkId || $jumlah <= 0) {
                continue;
            }

            $stokGudang = $stokGudangModel->where('produk_id', $produkId)->first();
            $stokTersedia = $stokGudang ? (int) $stokGudang['stok_tersedia'] : 0;

            if ($jumlah > $stokTersedia) {
                return redirect()->back()->withInput()->with('error', "Stok gudang tidak cukup untuk produk ID {$produkId}. Tersedia: {$stokTersedia}, diminta: {$jumlah}");
            }

            if (!$tglExpired) {
                return redirect()->back()->withInput()->with('error', 'Tanggal expired wajib diisi untuk setiap item');
            }

            $detailData[] = [
                'produk_id' => $produkId,
                'jumlah' => $jumlah,
                'tgl_expired' => $tglExpired,
                'harga_beli' => $hargaBeli,
            ];
        }

        if (empty($detailData)) {
            return redirect()->back()->withInput()->with('error', 'Item produk tidak valid');
        }

        $nomorKirim = $this->pengirimanModel->generateNomorKirim();

        $headerData = [
            'nomor_kirim' => $nomorKirim,
            'sales_id' => $salesId,
            'tanggal_kirim' => $tanggalKirim,
            'catatan' => $catatan,
            'dibuat_oleh' => $userId,
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $pengirimanId = $this->pengirimanModel->insert($headerData);

        foreach ($detailData as &$detail) {
            $detail['pengiriman_ke_sales_id'] = $pengirimanId;
        }
        unset($detail);

        $this->detailModel->insertBatch($detailData);

        foreach ($detailData as $detail) {
            $produkId = $detail['produk_id'];
            $jumlah = $detail['jumlah'];

            $stokGudang = $stokGudangModel->where('produk_id', $produkId)->first();
            $stokGudangModel->update($stokGudang['id'], [
                'stok_tersedia' => $stokGudang['stok_tersedia'] - $jumlah,
            ]);

            $mutasiGudangModel->insert([
                'produk_id' => $produkId,
                'jenis' => 'kirim_ke_sales',
                'jumlah' => $jumlah,
                'referensi_id' => $pengirimanId,
                'referensi_tabel' => 'pengiriman_ke_sales',
                'keterangan' => 'Transfer ke sales: ' . $nomorKirim,
                'dibuat_oleh' => $userId,
            ]);

            $stokSalesModel->tambahStok($salesId, $produkId, $jumlah);

            $mutasiSalesModel->insert([
                'sales_id' => $salesId,
                'produk_id' => $produkId,
                'jenis' => 'masuk_dari_gudang',
                'jumlah' => $jumlah,
                'referensi_id' => $pengirimanId,
                'referensi_tabel' => 'pengiriman_ke_sales',
                'keterangan' => 'Penerimaan dari gudang: ' . $nomorKirim,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transfer');
        }

        return redirect()->to('/pengiriman')->with('success', 'Transfer berhasil: ' . $nomorKirim);
    }

    public function detail($id)
    {
        $this->checkAccess();

        $header = $this->pengirimanModel->select('pengiriman_ke_sales.*, users.nama as sales_nama')
            ->join('users', 'users.id = pengiriman_ke_sales.sales_id', 'left')
            ->find($id);

        if (!$header) {
            return redirect()->to('/pengiriman')->with('error', 'Data tidak ditemukan');
        }

        $details = $this->detailModel->select('pengiriman_ke_sales_detail.*, produk.nama as produk_nama, produk.kode_produk')
            ->join('produk', 'produk.id = pengiriman_ke_sales_detail.produk_id', 'left')
            ->where('pengiriman_ke_sales_id', $id)
            ->findAll();

        $data = [
            'title' => 'Detail Transfer: ' . $header['nomor_kirim'],
            'header' => $header,
            'details' => $details,
        ];

        return view('pengiriman_ke_sales/detail', $data);
    }

    public function getStokSales()
    {
        $salesId = (int) $this->request->getGet('sales_id');
        $produkId = (int) $this->request->getGet('produk_id');

        $stokSalesModel = new StokSalesModel();
        $stok = $stokSalesModel->getStok($salesId, $produkId);

        return $this->response->setJSON(['stok' => $stok]);
    }

    public function destroy($id)
    {
        $this->checkAccess();

        $header = $this->pengirimanModel->find($id);
        if (!$header) {
            return redirect()->to('/pengiriman')->with('error', 'Data tidak ditemukan');
        }

        $this->pengirimanModel->delete($id);

        return redirect()->to('/pengiriman')->with('success', 'Transfer berhasil dihapus');
    }

    protected function checkAccess(): void
    {
        if (session()->get('role') !== 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}
