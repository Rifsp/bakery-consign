<?php

namespace App\Controllers;

use App\Models\PenitipanModel;
use App\Models\PenitipanDetailModel;
use App\Models\StokSalesModel;
use App\Models\MutasiSalesModel;
use App\Models\StokTokoModel;
use App\Models\StokExpiredTokoModel;
use App\Models\TokoModel;
use App\Models\ProdukModel;
use App\Models\HargaJualModel;

class Penitipan extends BaseController
{
    protected $penitipanModel;
    protected $detailModel;

    public function __construct()
    {
        $this->penitipanModel = new PenitipanModel();
        $this->detailModel = new PenitipanDetailModel();
        $this->helpers = array_merge($this->helpers, ['form']);
    }

    protected function checkAccess(): void
    {
        $role = session()->get('role');
        if (!in_array($role, ['admin', 'sales'], true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    protected function buildFilterQuery()
    {
        $role = session()->get('role');
        $salesId = (int) session()->get('id');
        $filterToko = $this->request->getGet('toko_id');
        $search = trim($this->request->getGet('search') ?? '');
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');

        $this->penitipanModel->select('
                penitipan.*,
                toko.nama as toko_nama,
                users.nama as sales_nama,
                (SELECT COALESCE(SUM(jumlah_titip),0) FROM penitipan_detail WHERE penitipan_id = penitipan.id) as total_item
            ')
            ->join('toko', 'toko.id = penitipan.toko_id', 'left')
            ->join('users', 'users.id = penitipan.sales_id', 'left');

        if ($role === 'sales') {
            $this->penitipanModel->where('penitipan.sales_id', $salesId);
        }
        if ($filterToko && $filterToko !== '') {
            $this->penitipanModel->where('penitipan.toko_id', $filterToko);
        }
        $this->penitipanModel->where('penitipan.tanggal_titip >=', $tglDari);
        $this->penitipanModel->where('penitipan.tanggal_titip <=', $tglSampai);

        if ($search !== '') {
            $this->penitipanModel->groupStart()
                ->like('penitipan.nomor_titip', $search, 'both', null, true)
                ->orLike('toko.nama', $search, 'both', null, true)
                ->groupEnd();
        }

        $this->penitipanModel->orderBy('penitipan.id', 'DESC');
    }

    public function index()
    {
        $this->checkAccess();

        $filterToko = $this->request->getGet('toko_id');
        $search = $this->request->getGet('search');
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');

        $this->buildFilterQuery();

        $tokoModel = new TokoModel();
        $role = session()->get('role');
        $salesId = (int) session()->get('id');

        if ($role === 'sales') {
            $tokoList = $tokoModel->where('sales_id', $salesId)->where('is_aktif', true)->findAll();
        } else {
            $tokoList = $tokoModel->where('is_aktif', true)->findAll();
        }

        $statusLabels = [
            'aktif' => ['label' => 'Aktif', 'class' => 'bg-success'],
            'selesai' => ['label' => 'Selesai', 'class' => 'bg-info text-dark'],
            'ditarik' => ['label' => 'Ditarik', 'class' => 'bg-secondary'],
        ];

        $data = [
            'title' => 'Penitipan',
            'records' => $this->penitipanModel->findAll(),
            'tokoList' => $tokoList,
            'filterToko' => $filterToko,
            'search' => $search,
            'tglDari' => $tglDari,
            'tglSampai' => $tglSampai,
            'statusLabels' => $statusLabels,
        ];

        return view('penitipan/index', $data);
    }

    public function fetch()
    {
        $this->checkAccess();
        $this->buildFilterQuery();

        $records = $this->penitipanModel->findAll();

        $statusLabels = [
            'aktif' => ['label' => 'Aktif', 'class' => 'bg-success'],
            'selesai' => ['label' => 'Selesai', 'class' => 'bg-info text-dark'],
            'ditarik' => ['label' => 'Ditarik', 'class' => 'bg-secondary'],
        ];

        $tableHtml = view('penitipan/_table_data', [
            'records' => $records,
            'statusLabels' => $statusLabels,
        ]);

        return $this->response->setJSON([
            'table'      => $tableHtml,
        ]);
    }

    public function create()
    {
        $this->checkAccess();

        $role = session()->get('role');
        $salesId = (int) session()->get('id');

        $tokoModel = new TokoModel();
        if ($role === 'sales') {
            $tokoList = $tokoModel->where('sales_id', $salesId)->where('is_aktif', true)->findAll();
        } else {
            $tokoList = $tokoModel->where('is_aktif', true)->findAll();
        }

        $produkModel = new ProdukModel();
        $produkList = $produkModel->where('is_aktif', true)->findAll();

        $stokSalesModel = new StokSalesModel();
        $stokSalesList = $stokSalesModel->where('sales_id', $salesId)->where('stok_tersedia >', 0)->findAll();

        $stokSalesMap = [];
        foreach ($stokSalesList as $s) {
            $stokSalesMap[$s['produk_id']] = (int) $s['stok_tersedia'];
        }

        $data = [
            'title' => 'Buat Penitipan',
            'tokoList' => $tokoList,
            'produkList' => $produkList,
            'stokSalesMap' => $stokSalesMap,
            'salesId' => $salesId,
        ];

        return view('penitipan/create', $data);
    }

    public function store()
    {
        $this->checkAccess();

        $rules = [
            'toko_id' => 'required|is_natural_no_zero',
            'tanggal_titip' => 'required|valid_date',
            'items' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role = session()->get('role');
        $salesId = (int) session()->get('id');
        $tokoId = (int) $this->request->getPost('toko_id');
        $tanggalTitip = $this->request->getPost('tanggal_titip');
        $catatan = $this->request->getPost('catatan');
        $items = $this->request->getPost('items');

        if (!is_array($items) || empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Minimal 1 item produk');
        }

        if ($role === 'sales') {
            $tokoModel = new TokoModel();
            $toko = $tokoModel->find($tokoId);
            if (!$toko || (int) $toko['sales_id'] !== $salesId) {
                return redirect()->back()->withInput()->with('error', 'Toko tidak valid');
            }
        }

        $stokSalesModel = new StokSalesModel();
        $mutasiSalesModel = new MutasiSalesModel();
        $stokTokoModel = new StokTokoModel();
        $stokExpiredModel = new StokExpiredTokoModel();
        $produkModel = new ProdukModel();
        $hargaJualModel = new HargaJualModel();

        $detailData = [];
        foreach ($items as $item) {
            $produkId = (int) ($item['produk_id'] ?? 0);
            $hargaJualId = (int) ($item['harga_jual_id'] ?? 0);
            $jumlahTitip = (int) ($item['jumlah_titip'] ?? 0);

            if (!$produkId || !$hargaJualId || $jumlahTitip <= 0) {
                continue;
            }

            $stokSales = $stokSalesModel->getStok($salesId, $produkId);
            if ($jumlahTitip > $stokSales) {
                return redirect()->back()->withInput()->with('error', "Stok sales tidak cukup untuk produk ID {$produkId}. Tersedia: {$stokSales}");
            }

            $hargaJual = $hargaJualModel->find($hargaJualId);
            if (!$hargaJual || (int) $hargaJual['produk_id'] !== $produkId) {
                return redirect()->back()->withInput()->with('error', 'Tier harga tidak valid');
            }

            $produk = $produkModel->find($produkId);
            $shelfLife = (int) ($produk['shelf_life_hari'] ?? 3);
            $tglExpired = date('Y-m-d', strtotime($tanggalTitip . ' + ' . $shelfLife . ' days'));

            $detailData[] = [
                'produk_id' => $produkId,
                'harga_jual_id' => $hargaJualId,
                'jumlah_titip' => $jumlahTitip,
                'tgl_expired' => $tglExpired,
                'harga_satuan' => $hargaJual['harga'],
                'fee_satuan' => $hargaJual['fee_sales'],
            ];
        }

        if (empty($detailData)) {
            return redirect()->back()->withInput()->with('error', 'Item produk tidak valid');
        }

        $nomorTitip = $this->penitipanModel->generateNomorTitip();

        $headerData = [
            'nomor_titip' => $nomorTitip,
            'toko_id' => $tokoId,
            'sales_id' => $salesId,
            'tanggal_titip' => $tanggalTitip,
            'status' => 'aktif',
            'catatan' => $catatan,
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $penitipanId = $this->penitipanModel->insert($headerData);

        foreach ($detailData as &$detail) {
            $detail['penitipan_id'] = $penitipanId;
        }
        unset($detail);

        $insertedIds = [];
        foreach ($detailData as $d) {
            $insertedIds[] = $this->detailModel->insert($d);
        }

        foreach ($detailData as $idx => $detail) {
            $produkId = $detail['produk_id'];
            $jumlahTitip = $detail['jumlah_titip'];
            $detailPk = $insertedIds[$idx];

            $stokSalesModel->tambahStok($salesId, $produkId, -$jumlahTitip);

            $mutasiSalesModel->insert([
                'sales_id' => $salesId,
                'produk_id' => $produkId,
                'jenis' => 'keluar_ke_toko',
                'jumlah' => $jumlahTitip,
                'referensi_id' => $penitipanId,
                'referensi_tabel' => 'penitipan',
                'keterangan' => 'Titip ke toko: ' . $nomorTitip,
            ]);

            $stokTokoModel->tambahStok($tokoId, $produkId, $jumlahTitip);

            $stokExpiredModel->insert([
                'toko_id' => $tokoId,
                'produk_id' => $produkId,
                'penitipan_detail_id' => $detailPk,
                'jumlah' => $jumlahTitip,
                'tgl_expired' => $detail['tgl_expired'],
                'is_diretur' => false,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan penitipan');
        }

        return redirect()->to('/penitipan')->with('success', 'Penitipan berhasil: ' . $nomorTitip);
    }

    public function getStokSalesJson()
    {
        $produkId = (int) $this->request->getGet('produk_id');
        $salesId = (int) session()->get('id');
        $stokSalesModel = new StokSalesModel();
        $stok = $stokSalesModel->getStok($salesId, $produkId);

        $hargaJualModel = new HargaJualModel();
        $tiers = $hargaJualModel->select('harga_jual.*, produk.nama as produk_nama')
            ->join('produk', 'produk.id = harga_jual.produk_id', 'left')
            ->where('harga_jual.produk_id', $produkId)
            ->where('harga_jual.is_aktif', true)
            ->findAll();

        return $this->response->setJSON([
            'stok' => $stok,
            'tiers' => $tiers,
        ]);
    }

    public function detail($id)
    {
        $this->checkAccess();

        $header = $this->penitipanModel->select('penitipan.*, toko.nama as toko_nama, users.nama as sales_nama')
            ->join('toko', 'toko.id = penitipan.toko_id', 'left')
            ->join('users', 'users.id = penitipan.sales_id', 'left')
            ->find($id);

        if (!$header) {
            return redirect()->to('/penitipan')->with('error', 'Data tidak ditemukan');
        }

        $details = $this->detailModel->select('
                penitipan_detail.*,
                produk.nama as produk_nama,
                produk.kode_produk,
                harga_jual.nama_harga as tier_nama
            ')
            ->join('produk', 'produk.id = penitipan_detail.produk_id', 'left')
            ->join('harga_jual', 'harga_jual.id = penitipan_detail.harga_jual_id', 'left')
            ->where('penitipan_id', $id)
            ->findAll();

        $statusLabels = [
            'aktif' => ['label' => 'Aktif', 'class' => 'bg-success'],
            'selesai' => ['label' => 'Selesai', 'class' => 'bg-info text-dark'],
            'ditarik' => ['label' => 'Ditarik', 'class' => 'bg-secondary'],
        ];

        $data = [
            'title' => 'Detail Penitipan: ' . $header['nomor_titip'],
            'header' => $header,
            'details' => $details,
            'statusLabels' => $statusLabels,
        ];

        return view('penitipan/detail', $data);
    }

    public function destroy($id)
    {
        $this->checkAccess();

        $role = session()->get('role');
        if ($role !== 'admin') {
            return redirect()->to('/penitipan')->with('error', 'Hanya admin yang dapat menarik penitipan');
        }

        $header = $this->penitipanModel->find($id);
        if (!$header) {
            return redirect()->to('/penitipan')->with('error', 'Data tidak ditemukan');
        }
        if ($header['status'] !== 'aktif') {
            return redirect()->to('/penitipan')->with('error', 'Hanya penitipan aktif yang bisa ditarik');
        }

        $details = $this->detailModel->where('penitipan_id', $id)->findAll();
        if (empty($details)) {
            return redirect()->to('/penitipan')->with('error', 'Detail penitipan tidak ditemukan');
        }

        $stokSalesModel = new StokSalesModel();
        $mutasiSalesModel = new MutasiSalesModel();
        $stokTokoModel = new StokTokoModel();
        $stokExpiredModel = new StokExpiredTokoModel();

        $db = \Config\Database::connect();
        $db->transStart();

        $this->penitipanModel->update($id, ['status' => 'ditarik']);

        foreach ($details as $detail) {
            $produkId = (int) $detail['produk_id'];
            $jumlahTitip = (int) $detail['jumlah_titip'];
            $salesId = (int) $header['sales_id'];
            $tokoId = (int) $header['toko_id'];

            $stokSalesModel->tambahStok($salesId, $produkId, $jumlahTitip);

            $mutasiSalesModel->insert([
                'sales_id' => $salesId,
                'produk_id' => $produkId,
                'jenis' => 'retur_dari_toko',
                'jumlah' => $jumlahTitip,
                'referensi_id' => $id,
                'referensi_tabel' => 'penitipan',
                'keterangan' => 'Tarik penitipan: ' . $header['nomor_titip'],
            ]);

            $stokTokoModel->tambahStok($tokoId, $produkId, -$jumlahTitip);

            $stokExpiredModel
                ->where('penitipan_detail_id', (int) $detail['id'])
                ->set(['is_diretur' => true])
                ->update();
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/penitipan')->with('error', 'Gagal menarik penitipan');
        }

        return redirect()->to('/penitipan')->with('success', 'Penitipan ditarik: ' . $header['nomor_titip']);
    }
}
