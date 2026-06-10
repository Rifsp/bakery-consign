<?php

namespace App\Controllers;

use App\Models\PembelianModel;
use App\Models\PembelianDetailModel;
use App\Models\SupplierModel;
use App\Models\ProdukModel;
use App\Models\StokGudangModel;
use App\Models\MutasiGudangModel;

class Pembelian extends BaseController
{
    protected $pembelianModel;
    protected $detailModel;

    public function __construct()
    {
        $this->pembelianModel = new PembelianModel();
        $this->detailModel = new PembelianDetailModel();
        $this->helpers = array_merge($this->helpers, ['form']);
    }

    protected function buildFilterQuery()
    {
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');

        $this->pembelianModel->select('pembelian.*, suppliers.nama as supplier_nama')
            ->join('suppliers', 'suppliers.id = pembelian.supplier_id', 'left');

        if ($status && $status !== '') {
            $this->pembelianModel->where('pembelian.status', $status);
        }
        $this->pembelianModel->where('pembelian.tanggal_pesan >=', $tglDari);
        $this->pembelianModel->where('pembelian.tanggal_pesan <=', $tglSampai);

        $search = trim($search ?? '');
        if ($search !== '') {
            $this->pembelianModel->groupStart()
                ->like('pembelian.nomor_po', $search, 'both', null, true)
                ->orLike('suppliers.nama', $search, 'both', null, true)
                ->groupEnd();
        }

        $this->pembelianModel->orderBy('pembelian.id', 'DESC');
    }

    public function index()
    {
        $this->checkAccess();

        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');
        $tglDari = $this->request->getGet('tgl_dari') ?: date('Y-m-01');
        $tglSampai = $this->request->getGet('tgl_sampai') ?: date('Y-m-t');

        $this->buildFilterQuery();

        $data = [
            'title' => 'Data Pembelian',
            'records' => $this->pembelianModel->paginate(15),
            'pager' => $this->pembelianModel->pager,
            'status' => $status,
            'search' => $search,
            'tglDari' => $tglDari,
            'tglSampai' => $tglSampai,
        ];

        return view('pembelian/index', $data);
    }

    public function fetch()
    {
        $this->checkAccess();

        $this->buildFilterQuery();

        $records = $this->pembelianModel->paginate(15);
        $pager = $this->pembelianModel->pager;

        $tableHtml = view('pembelian/_table_data', [
            'records' => $records,
        ]);

        $paginationHtml = $pager->links('default', 'bootstrap_pagination');

        return $this->response->setJSON([
            'table'      => $tableHtml,
            'pagination' => '<div class="mt-3" id="paginationWrap">' . $paginationHtml . '</div>',
        ]);
    }

    public function create()
    {
        $this->checkAccess();

        $supplierModel = new SupplierModel();
        $produkModel = new ProdukModel();

        $data = [
            'title' => 'Buat Purchase Order',
            'suppliers' => $supplierModel->where('is_aktif', true)->findAll(),
            'produkList' => $produkModel->where('is_aktif', true)->findAll(),
        ];

        return view('pembelian/create', $data);
    }

    public function store()
    {
        $this->checkAccess();

        $validationRules = [
            'supplier_id' => 'required|is_natural_no_zero',
            'items' => 'required',
            'tanggal_pesan' => 'required|valid_date',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $supplierId = $this->request->getPost('supplier_id');
        $tanggalPesan = $this->request->getPost('tanggal_pesan');
        $catatan = $this->request->getPost('catatan');
        $items = $this->request->getPost('items');

        if (!is_array($items) || empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Minimal 1 item produk');
        }

        $userId = session()->get('id');
        $totalNilai = 0;

        $detailData = [];
        foreach ($items as $item) {
            if (empty($item['produk_id']) || empty($item['jumlah_pesan']) || empty($item['harga_beli'])) {
                continue;
            }
            $subtotal = (int) $item['jumlah_pesan'] * (float) $item['harga_beli'];
            $totalNilai += $subtotal;
            $detailData[] = [
                'produk_id' => $item['produk_id'],
                'jumlah_pesan' => (int) $item['jumlah_pesan'],
                'jumlah_terima' => 0,
                'harga_beli' => (float) $item['harga_beli'],
            ];
        }

        if (empty($detailData)) {
            return redirect()->back()->withInput()->with('error', 'Item produk tidak valid');
        }

        $nomorPO = $this->pembelianModel->generateNomorPO();

        $poData = [
            'nomor_po' => $nomorPO,
            'supplier_id' => $supplierId,
            'tanggal_pesan' => $tanggalPesan,
            'status' => 'pending',
            'catatan' => $catatan,
            'total_nilai' => $totalNilai,
            'dibuat_oleh' => $userId,
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $poId = $this->pembelianModel->insert($poData);

        foreach ($detailData as &$detail) {
            $detail['pembelian_id'] = $poId;
        }
        unset($detail);

        $this->detailModel->insertBatch($detailData);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan PO');
        }

        cache()->deleteMatching('pembelian_index_*');
        return redirect()->to('/pembelian/detail/' . $poId)->with('success', 'PO berhasil dibuat: ' . $nomorPO);
    }

    public function detail($id)
    {
        $this->checkAccess();

        $po = $this->pembelianModel->select('pembelian.*, suppliers.nama as supplier_nama')
            ->join('suppliers', 'suppliers.id = pembelian.supplier_id', 'left')
            ->find($id);

        if (!$po) {
            return redirect()->to('/pembelian')->with('error', 'PO tidak ditemukan');
        }

        $details = $this->detailModel->select('pembelian_detail.*, produk.nama as produk_nama, produk.kode_produk')
            ->join('produk', 'produk.id = pembelian_detail.produk_id', 'left')
            ->where('pembelian_id', $id)
            ->findAll();

        $data = [
            'title' => 'Detail PO: ' . $po['nomor_po'],
            'po' => $po,
            'details' => $details,
        ];

        return view('pembelian/detail', $data);
    }

    public function terimaBarang($id)
    {
        $this->checkAccess();

        $po = $this->pembelianModel->select('pembelian.*, suppliers.nama as supplier_nama')
            ->join('suppliers', 'suppliers.id = pembelian.supplier_id', 'left')
            ->find($id);
        if (!$po) {
            return redirect()->to('/pembelian')->with('error', 'PO tidak ditemukan');
        }

        if ($po['status'] === 'diterima') {
            return redirect()->to('/pembelian/detail/' . $id)->with('error', 'PO sudah diterima semua');
        }
        if ($po['status'] === 'dibatalkan') {
            return redirect()->to('/pembelian/detail/' . $id)->with('error', 'PO sudah dibatalkan');
        }

        $details = $this->detailModel->select('pembelian_detail.*, produk.nama as produk_nama, produk.kode_produk')
            ->join('produk', 'produk.id = pembelian_detail.produk_id', 'left')
            ->where('pembelian_id', $id)
            ->findAll();

        $data = [
            'title' => 'Terima Barang - ' . $po['nomor_po'],
            'po' => $po,
            'details' => $details,
        ];

        return view('pembelian/terima', $data);
    }

    public function prosesTerima($id)
    {
        $this->checkAccess();

        $po = $this->pembelianModel->find($id);
        if (!$po) {
            return redirect()->to('/pembelian')->with('error', 'PO tidak ditemukan');
        }

        if ($po['status'] === 'diterima') {
            return redirect()->to('/pembelian/detail/' . $id)->with('error', 'PO sudah diterima semua');
        }
        if ($po['status'] === 'dibatalkan') {
            return redirect()->to('/pembelian/detail/' . $id)->with('error', 'PO sudah dibatalkan');
        }

        $items = $this->request->getPost('items');
        if (!is_array($items) || empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada item yang diproses');
        }

        $userId = session()->get('id');
        $db = \Config\Database::connect();
        $db->transStart();

        $allFullyReceived = true;
        $anyReceived = false;

        foreach ($items as $detailId => $item) {
            $detail = $this->detailModel->find($detailId);
            if (!$detail || $detail['pembelian_id'] != $id) {
                continue;
            }

            $qtyTerimaBaru = (int) ($item['qty_terima'] ?? 0);
            if ($qtyTerimaBaru <= 0) {
                if ($detail['jumlah_terima'] < $detail['jumlah_pesan']) {
                    $allFullyReceived = false;
                }
                continue;
            }

            $tglExpired = $item['tgl_expired'] ?? null;
            if ($tglExpired === '') {
                $tglExpired = null;
            }

            $maxCanReceive = $detail['jumlah_pesan'] - $detail['jumlah_terima'];
            if ($qtyTerimaBaru > $maxCanReceive) {
                $qtyTerimaBaru = $maxCanReceive;
            }

            $newJumlahTerima = $detail['jumlah_terima'] + $qtyTerimaBaru;

            $this->detailModel->update($detailId, [
                'jumlah_terima' => $newJumlahTerima,
                'tgl_expired' => $tglExpired,
            ]);

            $this->tambahStokGudang($detail['produk_id'], $qtyTerimaBaru);

            $this->catatMutasiGudang([
                'produk_id' => $detail['produk_id'],
                'jenis' => 'masuk',
                'jumlah' => $qtyTerimaBaru,
                'referensi_id' => $id,
                'referensi_tabel' => 'pembelian',
                'keterangan' => 'Penerimaan PO: ' . $po['nomor_po'],
                'dibuat_oleh' => $userId,
            ]);

            $anyReceived = true;
        }

        $updatedDetails = $this->detailModel->where('pembelian_id', $id)->findAll();
        foreach ($updatedDetails as $d) {
            if ($d['jumlah_terima'] < $d['jumlah_pesan']) {
                $allFullyReceived = false;
                break;
            }
        }

        if (!$anyReceived) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Tidak ada barang yang diterima (qty harus > 0)');
        }

        $newStatus = $allFullyReceived ? 'diterima' : 'sebagian';
        $tanggalTerima = date('Y-m-d');

        $this->pembelianModel->update($id, [
            'status' => $newStatus,
            'tanggal_terima' => $tanggalTerima,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses penerimaan');
        }

        cache()->deleteMatching('pembelian_index_*');

        $msg = 'Barang berhasil diterima. Status PO: ' . ucfirst($newStatus);
        return redirect()->to('/pembelian/detail/' . $id)->with('success', $msg);
    }

    public function destroy($id)
    {
        $this->checkAccess();

        $po = $this->pembelianModel->find($id);
        if (!$po) {
            return redirect()->to('/pembelian')->with('error', 'PO tidak ditemukan');
        }

        if ($po['status'] !== 'pending') {
            return redirect()->to('/pembelian')->with('error', 'Hanya PO dengan status pending yang dapat dibatalkan');
        }

        $this->pembelianModel->update($id, ['status' => 'dibatalkan']);

        cache()->deleteMatching('pembelian_index_*');
        return redirect()->to('/pembelian')->with('success', 'PO berhasil dibatalkan');
    }

    protected function tambahStokGudang(int $produkId, int $qty)
    {
        $stokModel = new StokGudangModel();
        $stok = $stokModel->where('produk_id', $produkId)->first();

        if ($stok) {
            $stokModel->update($stok['id'], [
                'stok_tersedia' => $stok['stok_tersedia'] + $qty,
            ]);
        } else {
            $stokModel->insert([
                'produk_id' => $produkId,
                'stok_tersedia' => $qty,
            ]);
        }
    }

    protected function catatMutasiGudang(array $data)
    {
        $mutasiModel = new MutasiGudangModel();
        $mutasiModel->insert($data);
    }

    protected function checkAccess(): void
    {
        if (session()->get('role') !== 'admin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}
