<?php

namespace App\Controllers;

use App\Models\ReturModel;
use App\Models\ReturDetailModel;
use App\Models\StokTokoModel;
use App\Models\StokSalesModel;
use App\Models\MutasiSalesModel;
use App\Models\StokExpiredTokoModel;

class Retur extends BaseController
{
    protected $returModel;
    protected $detailModel;

    public function __construct()
    {
        $this->returModel = new ReturModel();
        $this->detailModel = new ReturDetailModel();
    }

    protected function checkAccess(): void
    {
        $role = session()->get('role');
        if ($role !== 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    protected function buildFilterQuery()
    {
        $search = trim($this->request->getGet('search') ?? '');
        $status = $this->request->getGet('status');
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');

        $this->returModel->select('
                retur.*,
                toko.nama as toko_nama,
                users.nama as sales_nama
            ')
            ->join('toko', 'toko.id = retur.toko_id', 'left')
            ->join('users', 'users.id = retur.sales_id', 'left');

        if ($status && $status !== '') {
            $this->returModel->where('retur.status', $status);
        }
        $this->returModel->where('retur.tanggal >=', $tglDari);
        $this->returModel->where('retur.tanggal <=', $tglSampai);

        if ($search !== '') {
            $this->returModel->groupStart()
                ->like('retur.nomor_retur', $search, 'both', null, true)
                ->orLike('toko.nama', $search, 'both', null, true)
                ->groupEnd();
        }

        $this->returModel->orderBy('retur.id', 'DESC');
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
            'disetujui' => ['label' => 'Disetujui', 'class' => 'bg-success'],
            'ditolak' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
        ];

        $data = [
            'title' => 'Retur',
            'records' => $this->returModel->findAll(),
            'status' => $status,
            'search' => $search,
            'tglDari' => $tglDari,
            'tglSampai' => $tglSampai,
            'statusLabels' => $statusLabels,
        ];

        return view('retur/index', $data);
    }

    public function fetch()
    {
        $this->checkAccess();
        $this->buildFilterQuery();

        $records = $this->returModel->findAll();

        $statusLabels = [
            'pending' => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
            'disetujui' => ['label' => 'Disetujui', 'class' => 'bg-success'],
            'ditolak' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
        ];

        $tableHtml = view('retur/_table_data', [
            'records' => $records,
            'statusLabels' => $statusLabels,
        ]);

        return $this->response->setJSON([
            'table'      => $tableHtml,
        ]);
    }

    public function detail($id)
    {
        $this->checkAccess();

        $header = $this->returModel->select('retur.*, toko.nama as toko_nama, users.nama as sales_nama')
            ->join('toko', 'toko.id = retur.toko_id', 'left')
            ->join('users', 'users.id = retur.sales_id', 'left')
            ->find($id);

        if (!$header) {
            return redirect()->to('/retur')->with('error', 'Data tidak ditemukan');
        }

        $details = $this->detailModel
            ->select('retur_detail.*, produk.nama as produk_nama, produk.kode_produk')
            ->join('produk', 'produk.id = retur_detail.produk_id', 'left')
            ->where('retur_detail.retur_id', $id)
            ->findAll();

        $kunjungan = null;
        if ($header['kunjungan_id']) {
            $kunjunganModel = new \App\Models\KunjunganModel();
            $kunjungan = $kunjunganModel->select('kunjungan.*, toko.nama as toko_nama')
                ->join('toko', 'toko.id = kunjungan.toko_id', 'left')
                ->find($header['kunjungan_id']);
        }

        $statusLabels = [
            'pending' => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
            'disetujui' => ['label' => 'Disetujui', 'class' => 'bg-success'],
            'ditolak' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
        ];

        $data = [
            'title' => 'Detail Retur: ' . $header['nomor_retur'],
            'header' => $header,
            'details' => $details,
            'kunjungan' => $kunjungan,
            'statusLabels' => $statusLabels,
        ];

        return view('retur/detail', $data);
    }

    public function approve($id)
    {
        $this->checkAccess();

        $header = $this->returModel->find($id);
        if (!$header) {
            return redirect()->to('/retur')->with('error', 'Data tidak ditemukan');
        }
        if ($header['status'] !== 'pending') {
            return redirect()->to('/retur')->with('error', 'Hanya retur pending yang bisa disetujui');
        }

        $details = $this->detailModel
            ->select('retur_detail.*, produk.kode_produk')
            ->join('produk', 'produk.id = retur_detail.produk_id', 'left')
            ->where('retur_id', $id)
            ->findAll();
        if (empty($details)) {
            return redirect()->to('/retur')->with('error', 'Detail retur tidak ditemukan');
        }

        $stokTokoModel = new StokTokoModel();

        foreach ($details as $detail) {
            $tokoId = (int) $header['toko_id'];
            $produkId = (int) $detail['produk_id'];
            $jumlahRetur = (int) $detail['jumlah_retur'];
            $stokToko = $stokTokoModel->getStok($tokoId, $produkId);
            if ($jumlahRetur > $stokToko) {
                $kode = $detail['kode_produk'] ?? "ID {$produkId}";
                return redirect()->to('/retur/detail/' . $id)->with('error', "Stok toko tidak cukup untuk {$kode}. Retur {$jumlahRetur}, stok tersedia: {$stokToko}");
            }
        }

        $stokSalesModel = new StokSalesModel();
        $mutasiSalesModel = new MutasiSalesModel();
        $stokExpiredModel = new StokExpiredTokoModel();

        $db = \Config\Database::connect();
        $db->transStart();

        $this->returModel->update($id, ['status' => 'disetujui']);

        foreach ($details as $detail) {
            $produkId = (int) $detail['produk_id'];
            $jumlahRetur = (int) $detail['jumlah_retur'];
            $kondisi = $detail['kondisi'] ?? 'baik';
            $tglExpired = $detail['tgl_expired'] ?? null;
            $tokoId = (int) $header['toko_id'];
            $salesId = (int) $header['sales_id'];

            $stokTokoModel->tambahStok($tokoId, $produkId, -$jumlahRetur);

            if ($kondisi === 'baik') {
                $stokSalesModel->tambahStok($salesId, $produkId, $jumlahRetur);

                $mutasiSalesModel->insert([
                    'sales_id' => $salesId,
                    'produk_id' => $produkId,
                    'jenis' => 'retur_dari_toko',
                    'jumlah' => $jumlahRetur,
                    'referensi_id' => $id,
                    'referensi_tabel' => 'retur',
                    'keterangan' => 'Retur baik: ' . $header['nomor_retur'],
                ]);
            }

            if ($tglExpired) {
                $stokExpiredModel
                    ->where('toko_id', $tokoId)
                    ->where('produk_id', $produkId)
                    ->where('tgl_expired', $tglExpired)
                    ->where('is_diretur', false)
                    ->set(['is_diretur' => true])
                    ->update();
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/retur/detail/' . $id)->with('error', 'Gagal menyetujui retur');
        }

        return redirect()->to('/retur/detail/' . $id)->with('success', 'Retur disetujui: ' . $header['nomor_retur']);
    }

    public function reject($id)
    {
        $this->checkAccess();

        $header = $this->returModel->find($id);
        if (!$header) {
            return redirect()->to('/retur')->with('error', 'Data tidak ditemukan');
        }
        if ($header['status'] !== 'pending') {
            return redirect()->to('/retur')->with('error', 'Hanya retur pending yang bisa ditolak');
        }

        $this->returModel->update($id, ['status' => 'ditolak']);

        return redirect()->to('/retur/detail/' . $id)->with('success', 'Retur ditolak: ' . $header['nomor_retur']);
    }
}
