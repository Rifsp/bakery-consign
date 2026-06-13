<?php

namespace App\Controllers;

use App\Models\LaporanModel;
use App\Models\ProdukModel;
use App\Models\TokoModel;
use App\Models\UserModel;
use App\Models\SupplierModel;

class Laporan extends BaseController
{
    protected $laporanModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
    }

    // --- ADMIN ONLY ---

    public function labaRugi()
    {
        $this->checkAdmin();
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');
        $salesId = $this->request->getGet('sales_id') ? (int) $this->request->getGet('sales_id') : null;
        $produkId = $this->request->getGet('produk_id') ? (int) $this->request->getGet('produk_id') : null;

        $data = [
            'title' => 'Laporan Laba Rugi',
            'records' => $this->laporanModel->labaRugi($tglDari, $tglSampai, $salesId, $produkId),
            'summary' => $this->laporanModel->labaRugiSummary($tglDari, $tglSampai, $salesId),
            'tglDari' => $tglDari,
            'tglSampai' => $tglSampai,
            'salesList' => (new UserModel())->where('role', 'sales')->findAll(),
            'produkList' => (new ProdukModel())->where('is_aktif', true)->findAll(),
            'filterSales' => $salesId,
            'filterProduk' => $produkId,
        ];
        return view('laporan/laba_rugi', $data);
    }

    public function penjualan()
    {
        $this->checkAdmin();
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');
        $salesId = $this->request->getGet('sales_id') ? (int) $this->request->getGet('sales_id') : null;
        $tokoId = $this->request->getGet('toko_id') ? (int) $this->request->getGet('toko_id') : null;

        $data = [
            'title' => 'Rekap Penjualan',
            'records' => $this->laporanModel->penjualan($tglDari, $tglSampai, $salesId, $tokoId),
            'tglDari' => $tglDari,
            'tglSampai' => $tglSampai,
            'salesList' => (new UserModel())->where('role', 'sales')->findAll(),
            'tokoList' => (new TokoModel())->where('is_aktif', true)->findAll(),
            'filterSales' => $salesId,
            'filterToko' => $tokoId,
        ];
        return view('laporan/penjualan', $data);
    }

    public function pembelian()
    {
        $this->checkAdmin();
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');
        $supplierId = $this->request->getGet('supplier_id') ? (int) $this->request->getGet('supplier_id') : null;

        $data = [
            'title' => 'Laporan Pembelian',
            'records' => $this->laporanModel->pembelian($tglDari, $tglSampai, $supplierId),
            'tglDari' => $tglDari,
            'tglSampai' => $tglSampai,
            'supplierList' => (new SupplierModel())->findAll(),
            'filterSupplier' => $supplierId,
        ];
        return view('laporan/pembelian', $data);
    }

    public function stokGudang()
    {
        $this->checkAdmin();
        $data = [
            'title' => 'Stok Gudang',
            'records' => $this->laporanModel->stokGudang(),
        ];
        return view('laporan/stok_gudang', $data);
    }

    public function stokSales()
    {
        $this->checkAdmin();
        $salesId = $this->request->getGet('sales_id') ? (int) $this->request->getGet('sales_id') : null;
        $data = [
            'title' => 'Stok Sales',
            'records' => $this->laporanModel->stokSales($salesId),
            'salesList' => (new UserModel())->where('role', 'sales')->findAll(),
            'filterSales' => $salesId,
        ];
        return view('laporan/stok_sales', $data);
    }

    public function stokToko()
    {
        $this->checkAdmin();
        $salesId = $this->request->getGet('sales_id') ? (int) $this->request->getGet('sales_id') : null;
        $data = [
            'title' => 'Stok Toko',
            'records' => $this->laporanModel->stokToko($salesId),
            'salesList' => (new UserModel())->where('role', 'sales')->findAll(),
            'filterSales' => $salesId,
        ];
        return view('laporan/stok_toko', $data);
    }

    public function expired()
    {
        $this->checkAdmin();
        $tokoId = $this->request->getGet('toko_id') ? (int) $this->request->getGet('toko_id') : null;
        $hampirExpired = (bool) $this->request->getGet('hampir_expired');

        $data = [
            'title' => 'Monitor Expired',
            'records' => $this->laporanModel->expired($tokoId, $hampirExpired),
            'tokoList' => (new TokoModel())->where('is_aktif', true)->findAll(),
            'filterToko' => $tokoId,
            'hampirExpired' => $hampirExpired,
        ];
        return view('laporan/expired', $data);
    }

    // --- SHARED ---

    public function penjualanSaya()
    {
        $this->checkAccess();
        $salesId = (int) session()->get('id');
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');

        $data = [
            'title' => 'Penjualan Saya',
            'records' => $this->laporanModel->penjualan($tglDari, $tglSampai, $salesId),
            'tglDari' => $tglDari,
            'tglSampai' => $tglSampai,
            'salesList' => [],
            'tokoList' => [],
            'filterSales' => null,
            'filterToko' => null,
        ];
        return view('laporan/penjualan_saya', $data);
    }

    // --- SALES ONLY ---

    public function feeSales()
    {
        $this->checkAdmin();
        $salesId = $this->request->getGet('sales_id') ? (int) $this->request->getGet('sales_id') : null;
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');

        $records = $this->laporanModel->feeSales($tglDari, $tglSampai, $salesId);

        $data = [
            'title' => 'Fee Penjualan Sales',
            'records' => $records,
            'tglDari' => $tglDari,
            'tglSampai' => $tglSampai,
            'salesList' => (new \App\Models\UserModel())->where('role', 'sales')->findAll(),
            'filterSales' => $salesId,
        ];
        return view('laporan/fee_sales', $data);
    }

    public function stokSalesSaya()
    {
        $this->checkSales();
        $salesId = (int) session()->get('id');
        $data = [
            'title' => 'Stok Sales Saya',
            'records' => $this->laporanModel->stokSales($salesId),
        ];
        return view('laporan/stok_sales_saya', $data);
    }

    public function stokTokoSaya()
    {
        $this->checkSales();
        $salesId = (int) session()->get('id');
        $data = [
            'title' => 'Stok Toko Area Saya',
            'records' => $this->laporanModel->stokToko($salesId),
        ];
        return view('laporan/stok_toko_saya', $data);
    }

    public function mutasi()
    {
        $role = session()->get('role');
        if ($role !== 'admin' && $role !== 'sales') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $isSales = $role === 'sales';
        $jenis = $isSales ? 'sales' : $this->request->getGet('jenis') ?: 'gudang';
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');
        $salesId = $isSales ? (int) session()->get('id') : ($this->request->getGet('sales_id') ? (int) $this->request->getGet('sales_id') : null);

        if ($jenis === 'sales') {
            $records = $this->laporanModel->mutasiSalesRingkasan($tglDari, $tglSampai, $salesId);
        } else {
            $jenis = 'gudang';
            $records = $this->laporanModel->mutasiGudangRingkasan($tglDari, $tglSampai);
        }

        $data = [
            'title' => 'Mutasi Stok',
            'records' => $records,
            'jenis' => $jenis,
            'tglDari' => $tglDari,
            'tglSampai' => $tglSampai,
            'salesList' => (new UserModel())->where('role', 'sales')->findAll(),
            'filterSales' => $salesId,
            'isSales' => $isSales,
        ];
        return view('laporan/mutasi', $data);
    }

    public function mutasiDetailJson()
    {
        $role = session()->get('role');
        if ($role !== 'admin' && $role !== 'sales') {
            return $this->response->setJSON([]);
        }

        $isSales = $role === 'sales';
        $jenis = $isSales ? 'sales' : $this->request->getGet('jenis') ?: 'gudang';
        $produkId = (int) $this->request->getGet('produk_id');
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');
        $salesId = $isSales ? (int) session()->get('id') : ($this->request->getGet('sales_id') ? (int) $this->request->getGet('sales_id') : null);

        if (!$produkId) {
            return $this->response->setJSON([]);
        }

        if ($jenis === 'sales') {
            $records = $this->laporanModel->mutasiSalesDetail($produkId, $tglDari, $tglSampai, $salesId);
        } else {
            $records = $this->laporanModel->mutasiGudangDetail($produkId, $tglDari, $tglSampai);
        }

        return $this->response->setJSON($records);
    }

    public function stokTokoDetail($tokoId)
    {
        $role = session()->get('role');
        if ($role === 'sales') {
            $salesId = (int) session()->get('id');
            $records = $this->laporanModel->stokTokoDetail((int) $tokoId, $salesId);
        } else {
            $records = $this->laporanModel->stokTokoDetail((int) $tokoId);
        }
        $namaToko = $records[0]['nama_toko'] ?? 'Toko';
        $data = [
            'title' => 'Detail Stok: ' . $namaToko,
            'records' => $records,
            'namaToko' => $namaToko,
            'isSales' => $role === 'sales',
        ];
        return view('laporan/stok_toko_detail', $data);
    }

    // --- HELPERS ---

    protected function checkAccess(): void
    {
        $role = session()->get('role');
        if (!in_array($role, ['admin', 'sales'], true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    protected function checkAdmin(): void
    {
        if (session()->get('role') !== 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    protected function checkSales(): void
    {
        if (session()->get('role') !== 'sales') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    // --- EXPORT ---

    public function exportCsv($jenis)
    {
        $role = session()->get('role');
        $this->db = \Config\Database::connect();

        $filename = $jenis . '_' . date('Ymd') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');

        switch ($jenis) {
            case 'laba_rugi':
                $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
                $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');
                fputcsv($output, ['Tanggal', 'Toko', 'Sales', 'Produk', 'Terjual', 'Harga', 'Fee', 'HPP', 'Subtotal', 'Laba']);
                foreach ($this->laporanModel->labaRugi($tglDari, $tglSampai) as $r) {
                    fputcsv($output, [$r['tanggal'], $r['nama_toko'], $r['nama_sales'], $r['nama_produk'], $r['jumlah_terjual'], $r['harga_satuan'], $r['fee_satuan'], $r['hpp_satuan'], $r['subtotal_harga'], $r['laba_bersih']]);
                }
                break;

            case 'fee_sales':
                $salesId = (int) session()->get('id');
                $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
                $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');
                fputcsv($output, ['Tanggal', 'Toko', 'Produk', 'Terjual', 'Fee Satuan', 'Total Fee']);
                foreach ($this->laporanModel->feeSales($salesId, $tglDari, $tglSampai) as $r) {
                    fputcsv($output, [$r['tanggal'], $r['nama_toko'], $r['nama_produk'], $r['jumlah_terjual'], $r['fee_satuan'], $r['total_fee']]);
                }
                break;

            case 'stok_gudang':
                fputcsv($output, ['Kode', 'Produk', 'Satuan', 'HPP', 'Stok', 'Update']);
                foreach ($this->laporanModel->stokGudang() as $r) {
                    fputcsv($output, [$r['kode_produk'], $r['nama_produk'], $r['satuan'], $r['hpp'], $r['stok_tersedia'], $r['updated_at']]);
                }
                break;

            case 'expired':
                fputcsv($output, ['Toko', 'Produk', 'Jml Titip', 'Stok Toko', 'Tgl Expired', 'Status']);
                foreach ($this->laporanModel->expired() as $r) {
                    fputcsv($output, [$r['nama_toko'], $r['nama_produk'], $r['jumlah_titip'], $r['stok_tersedia'], $r['tgl_expired'], $r['sudah_expired'] ? 'Expired' : 'Akan Expired']);
                }
                break;
        }

        fclose($output);
        exit;
    }
}
