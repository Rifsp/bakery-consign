<?php

namespace App\Controllers;

use App\Models\KunjunganModel;
use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\ReturModel;
use App\Models\ReturDetailModel;
use App\Models\StokTokoModel;
use App\Models\StokSalesModel;
use App\Models\MutasiSalesModel;
use App\Models\StokExpiredTokoModel;
use App\Models\TokoModel;
use App\Models\ProdukModel;
use App\Models\HargaJualModel;

class Kunjungan extends BaseController
{
    protected $kunjunganModel;
    protected $penjualanModel;
    protected $penjualanDetailModel;
    protected $returModel;
    protected $returDetailModel;

    public function __construct()
    {
        $this->kunjunganModel = new KunjunganModel();
        $this->penjualanModel = new PenjualanModel();
        $this->penjualanDetailModel = new PenjualanDetailModel();
        $this->returModel = new ReturModel();
        $this->returDetailModel = new ReturDetailModel();
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
        $search = trim($this->request->getGet('search') ?? '');
        $status = $this->request->getGet('status');
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');

        $this->kunjunganModel->select('
                kunjungan.*,
                toko.nama as toko_nama,
                users.nama as sales_nama
            ')
            ->join('toko', 'toko.id = kunjungan.toko_id', 'left')
            ->join('users', 'users.id = kunjungan.sales_id', 'left');

        if ($role === 'sales') {
            $this->kunjunganModel->where('kunjungan.sales_id', $salesId);
        }
        if ($status && $status !== '') {
            $this->kunjunganModel->where('kunjungan.status', $status);
        }
        $this->kunjunganModel->where('kunjungan.tanggal >=', $tglDari);
        $this->kunjunganModel->where('kunjungan.tanggal <=', $tglSampai);

        if ($search !== '') {
            $this->kunjunganModel->groupStart()
                ->like('kunjungan.nomor_kunjungan', $search, 'both', null, true)
                ->orLike('toko.nama', $search, 'both', null, true)
                ->groupEnd();
        }

        $this->kunjunganModel->orderBy('kunjungan.id', 'DESC');
    }

    public function index()
    {
        $this->checkAccess();

        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');

        $this->buildFilterQuery();

        $statusLabels = [
            'pending' => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
            'selesai' => ['label' => 'Selesai', 'class' => 'bg-success'],
        ];

        $data = [
            'title' => 'Kunjungan',
            'records' => $this->kunjunganModel->findAll(),
            'status' => $status,
            'search' => $search,
            'tglDari' => $tglDari,
            'tglSampai' => $tglSampai,
            'statusLabels' => $statusLabels,
        ];

        return view('kunjungan/index', $data);
    }

    public function fetch()
    {
        $this->checkAccess();
        $this->buildFilterQuery();

        $records = $this->kunjunganModel->findAll();

        $statusLabels = [
            'pending' => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
            'selesai' => ['label' => 'Selesai', 'class' => 'bg-success'],
        ];

        $tableHtml = view('kunjungan/_table_data', [
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

        $data = [
            'title' => 'Buat Kunjungan',
            'tokoList' => $tokoList,
            'produkList' => $produkList,
        ];

        return view('kunjungan/create', $data);
    }

    public function store()
    {
        $this->checkAccess();

        $rules = [
            'toko_id' => 'required|is_natural_no_zero',
            'tanggal' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role = session()->get('role');
        $salesId = (int) session()->get('id');
        $tokoId = (int) $this->request->getPost('toko_id');
        $tanggal = $this->request->getPost('tanggal');
        $catatan = $this->request->getPost('catatan');
        $penjualanItems = $this->request->getPost('penjualan_items');
        $returItems = $this->request->getPost('retur_items');

        if ($role === 'sales') {
            $tokoModel = new TokoModel();
            $toko = $tokoModel->find($tokoId);
            if (!$toko || (int) $toko['sales_id'] !== $salesId) {
                return redirect()->back()->withInput()->with('error', 'Toko tidak valid');
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $nomorKunjungan = $this->kunjunganModel->generateNomorKunjungan();
            $kunjunganId = $this->kunjunganModel->insert([
                'nomor_kunjungan' => $nomorKunjungan,
                'toko_id' => $tokoId,
                'sales_id' => $salesId,
                'tanggal' => $tanggal,
                'status' => 'selesai',
                'catatan' => $catatan,
            ]);

            if (!is_array($penjualanItems)) {
                $penjualanItems = [];
            }
            if (!is_array($returItems)) {
                $returItems = [];
            }

            $hasPenjualan = !empty($penjualanItems);
            $hasRetur = !empty($returItems);

            if ($hasPenjualan) {
                $this->processPenjualan($kunjunganId, $tokoId, $salesId, $tanggal, $penjualanItems);
            }

            if ($hasRetur) {
                $this->processRetur($kunjunganId, $tokoId, $salesId, $tanggal, $returItems);
            }

            if (!$hasPenjualan && !$hasRetur) {
                $this->kunjunganModel->update($kunjunganId, ['status' => 'pending']);
            }
        } catch (\RuntimeException $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            $errMsg = $db->error();
            log_message('error', 'Kunjungan store failed: ' . json_encode($errMsg));
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan kunjungan: ' . ($errMsg['message'] ?? 'unknown error'));
        }

        return redirect()->to('/kunjungan')->with('success', 'Kunjungan berhasil: ' . $nomorKunjungan);
    }

    private function processPenjualan(int $kunjunganId, int $tokoId, int $salesId, string $tanggal, array $items): void
    {
        $validItems = [];
        foreach ($items as $item) {
            $produkId = (int) ($item['produk_id'] ?? 0);
            $jumlahTerjual = (int) ($item['jumlah_terjual'] ?? 0);
            $hargaSatuan = (float) ($item['harga_satuan'] ?? 0);
            $hargaJualId = (int) ($item['harga_jual_id'] ?? 0);

            if (!$produkId || $jumlahTerjual <= 0 || $hargaSatuan <= 0 || !$hargaJualId) {
                continue;
            }
            $validItems[] = $item;
        }

        if (empty($validItems)) {
            return;
        }

        $penjualanModel = new PenjualanModel();
        $detailModel = new PenjualanDetailModel();
        $stokTokoModel = new StokTokoModel();

        $nomorJual = $penjualanModel->generateNomorJual();

        $penjualanId = $penjualanModel->insert([
            'nomor_jual' => $nomorJual,
            'kunjungan_id' => $kunjunganId,
            'toko_id' => $tokoId,
            'sales_id' => $salesId,
            'tanggal' => $tanggal,
            'total_harga' => 0,
            'total_fee' => 0,
        ]);

        $runningHarga = 0;
        $runningFee = 0;

        foreach ($validItems as $item) {
            $produkId = (int) ($item['produk_id'] ?? 0);
            $jumlahTerjual = (int) ($item['jumlah_terjual'] ?? 0);
            $hargaSatuan = (float) ($item['harga_satuan'] ?? 0);
            $feeSatuan = (float) ($item['fee_satuan'] ?? 0);
            $hppSatuan = (float) ($item['hpp_satuan'] ?? 0);
            $hargaJualId = (int) ($item['harga_jual_id'] ?? 0);

            $stokToko = $stokTokoModel->getStok($tokoId, $produkId);
            if ($jumlahTerjual > $stokToko) {
                throw new \RuntimeException("Stok toko tidak cukup untuk produk ID {$produkId}. Tersedia: {$stokToko}");
            }

            $detailModel->insert([
                'penjualan_id' => $penjualanId,
                'produk_id' => $produkId,
                'harga_jual_id' => $hargaJualId,
                'jumlah_terjual' => $jumlahTerjual,
                'harga_satuan' => $hargaSatuan,
                'fee_satuan' => $feeSatuan,
                'hpp_satuan' => $hppSatuan,
            ]);

            $stokTokoModel->tambahStok($tokoId, $produkId, -$jumlahTerjual);

            $runningHarga += $hargaSatuan * $jumlahTerjual;
            $runningFee += $feeSatuan * $jumlahTerjual;
        }

        $penjualanModel->update($penjualanId, [
            'total_harga' => $runningHarga,
            'total_fee' => $runningFee,
        ]);
    }

    private function processRetur(int $kunjunganId, int $tokoId, int $salesId, string $tanggal, array $items): void
    {
        $validItems = [];
        foreach ($items as $item) {
            $produkId = (int) ($item['produk_id'] ?? 0);
            $jumlahRetur = (int) ($item['jumlah_retur'] ?? 0);

            if (!$produkId || $jumlahRetur <= 0) {
                continue;
            }
            $validItems[] = $item;
        }

        if (empty($validItems)) {
            return;
        }

        $returModel = new ReturModel();
        $detailModel = new ReturDetailModel();

        $nomorRetur = $returModel->generateNomorRetur();

        $returId = $returModel->insert([
            'nomor_retur' => $nomorRetur,
            'kunjungan_id' => $kunjunganId,
            'toko_id' => $tokoId,
            'sales_id' => $salesId,
            'tanggal' => $tanggal,
            'status' => 'pending',
            'alasan' => 'Retur dari toko',
        ]);

        foreach ($validItems as $item) {
            $produkId = (int) ($item['produk_id'] ?? 0);
            $jumlahRetur = (int) ($item['jumlah_retur'] ?? 0);
            $kondisi = $item['kondisi'] ?? 'baik';
            $tglExpired = $item['tgl_expired'] ?? null;
            if ($tglExpired === '') {
                $tglExpired = null;
            }
            $keterangan = $item['keterangan'] ?? '';

            $detailModel->insert([
                'retur_id' => $returId,
                'produk_id' => $produkId,
                'jumlah_retur' => $jumlahRetur,
                'kondisi' => $kondisi,
                'tgl_expired' => $tglExpired,
                'keterangan' => $keterangan,
            ]);
        }
    }

    public function getStokTokoJson()
    {
        $this->checkAccess();
        $tokoId = (int) $this->request->getGet('toko_id');
        $produkId = (int) $this->request->getGet('produk_id');

        $stokTokoModel = new StokTokoModel();
        $stok = $stokTokoModel->getStok($tokoId, $produkId);

        $db = \Config\Database::connect();
        $sql = "SELECT pd.harga_satuan, pd.fee_satuan, pr.hpp, pd.harga_jual_id, hj.nama_harga
                FROM penitipan_detail pd
                JOIN penitipan p ON p.id = pd.penitipan_id
                JOIN produk pr ON pr.id = pd.produk_id
                LEFT JOIN harga_jual hj ON hj.id = pd.harga_jual_id
                WHERE p.toko_id = ? AND pd.produk_id = ? AND p.status = 'aktif'
                ORDER BY p.id DESC LIMIT 1";
        $row = $db->query($sql, [$tokoId, $produkId])->getRowArray();

        if (!$row) {
            $sql2 = "SELECT hj.harga as harga_satuan, hj.fee_sales as fee_satuan, pr.hpp, hj.id as harga_jual_id, hj.nama_harga
                     FROM harga_jual hj
                     JOIN produk pr ON pr.id = hj.produk_id
                     WHERE hj.produk_id = ? AND hj.is_aktif = true
                     ORDER BY hj.id ASC LIMIT 1";
            $row = $db->query($sql2, [$produkId])->getRowArray();
        }

        return $this->response->setJSON([
            'stok' => $stok,
            'harga' => $row ? [
                'harga_satuan' => (float) ($row['harga_satuan'] ?? 0),
                'fee_satuan' => (float) ($row['fee_satuan'] ?? 0),
                'hpp' => (float) ($row['hpp'] ?? 0),
                'harga_jual_id' => (int) ($row['harga_jual_id'] ?? 0),
                'nama_harga' => $row['nama_harga'] ?? '-',
            ] : null,
        ]);
    }

    public function detail($id)
    {
        $this->checkAccess();

        $header = $this->kunjunganModel->select('kunjungan.*, toko.nama as toko_nama, users.nama as sales_nama')
            ->join('toko', 'toko.id = kunjungan.toko_id', 'left')
            ->join('users', 'users.id = kunjungan.sales_id', 'left')
            ->find($id);

        if (!$header) {
            return redirect()->to('/kunjungan')->with('error', 'Data tidak ditemukan');
        }

        $penjualan = $this->penjualanModel
            ->where('kunjungan_id', $id)
            ->first();

        $penjualanDetails = [];
        if ($penjualan) {
            $penjualanDetails = $this->penjualanDetailModel
                ->select('penjualan_detail.*, produk.nama as produk_nama, produk.kode_produk, harga_jual.nama_harga as tier_nama')
                ->join('produk', 'produk.id = penjualan_detail.produk_id', 'left')
                ->join('harga_jual', 'harga_jual.id = penjualan_detail.harga_jual_id', 'left')
                ->where('penjualan_detail.penjualan_id', $penjualan['id'])
                ->findAll();
        }

        $retur = $this->returModel
            ->where('kunjungan_id', $id)
            ->first();

        $returDetails = [];
        if ($retur) {
            $returDetails = $this->returDetailModel
                ->select('retur_detail.*, produk.nama as produk_nama, produk.kode_produk')
                ->join('produk', 'produk.id = retur_detail.produk_id', 'left')
                ->where('retur_detail.retur_id', $retur['id'])
                ->findAll();
        }

        $statusLabels = [
            'pending' => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
            'selesai' => ['label' => 'Selesai', 'class' => 'bg-success'],
        ];

        $data = [
            'title' => 'Detail Kunjungan: ' . $header['nomor_kunjungan'],
            'header' => $header,
            'penjualan' => $penjualan,
            'penjualanDetails' => $penjualanDetails,
            'retur' => $retur,
            'returDetails' => $returDetails,
            'statusLabels' => $statusLabels,
        ];

        return view('kunjungan/detail', $data);
    }
}
