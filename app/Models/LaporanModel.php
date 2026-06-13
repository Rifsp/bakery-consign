<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function labaRugi(?string $tglDari = null, ?string $tglSampai = null, ?int $salesId = null, ?int $produkId = null): array
    {
        $sql = "SELECT v.* FROM v_laporan_laba v";
        $params = [];
        $joins = [];
        if ($salesId) {
            $sql .= " JOIN penjualan pj ON pj.nomor_jual = v.nomor_jual";
            $sql .= " WHERE pj.sales_id = ?";
            $params[] = $salesId;
        } else {
            $sql .= " WHERE 1=1";
        }
        if ($tglDari) { $sql .= " AND v.tanggal >= ?"; $params[] = $tglDari; }
        if ($tglSampai) { $sql .= " AND v.tanggal <= ?"; $params[] = $tglSampai; }
        if ($produkId) { $sql .= " AND v.id IN (SELECT id FROM penjualan_detail WHERE produk_id = ?)"; $params[] = $produkId; }
        $sql .= " ORDER BY v.tanggal DESC";
        return $this->db->query($sql, $params)->getResultArray();
    }

    public function labaRugiSummary(?string $tglDari = null, ?string $tglSampai = null, ?int $salesId = null): array
    {
        $sql = "SELECT
            COALESCE(SUM(v.subtotal_harga), 0) as total_penjualan,
            COALESCE(SUM(v.subtotal_hpp), 0) as total_hpp,
            COALESCE(SUM(v.subtotal_fee), 0) as total_fee,
            COALESCE(SUM(v.laba_bersih), 0) as total_laba
            FROM v_laporan_laba v";
        $params = [];
        if ($salesId) {
            $sql .= " JOIN penjualan pj ON pj.nomor_jual = v.nomor_jual";
            $sql .= " WHERE pj.sales_id = ?";
            $params[] = $salesId;
        } else {
            $sql .= " WHERE 1=1";
        }
        if ($tglDari) { $sql .= " AND v.tanggal >= ?"; $params[] = $tglDari; }
        if ($tglSampai) { $sql .= " AND v.tanggal <= ?"; $params[] = $tglSampai; }
        return $this->db->query($sql, $params)->getRowArray();
    }

    public function penjualan(?string $tglDari = null, ?string $tglSampai = null, ?int $salesId = null, ?int $tokoId = null): array
    {
        $sql = "SELECT pj.*, pj.total_harga, pj.total_fee, t.nama as toko_nama, u.nama as sales_nama
                FROM penjualan pj
                JOIN toko t ON t.id = pj.toko_id
                JOIN users u ON u.id = pj.sales_id
                WHERE 1=1";
        $params = [];
        if ($tglDari) { $sql .= " AND pj.tanggal >= ?"; $params[] = $tglDari; }
        if ($tglSampai) { $sql .= " AND pj.tanggal <= ?"; $params[] = $tglSampai; }
        if ($salesId) { $sql .= " AND pj.sales_id = ?"; $params[] = $salesId; }
        if ($tokoId) { $sql .= " AND pj.toko_id = ?"; $params[] = $tokoId; }
        $sql .= " ORDER BY pj.tanggal DESC";
        return $this->db->query($sql, $params)->getResultArray();
    }

    public function pembelian(?string $tglDari = null, ?string $tglSampai = null, ?int $supplierId = null): array
    {
        $sql = "SELECT v.* FROM v_laporan_pembelian v";
        $params = [];
        if ($supplierId) {
            $sql .= " JOIN pembelian pb ON pb.nomor_po = v.nomor_po";
            $sql .= " WHERE pb.supplier_id = ?";
            $params[] = $supplierId;
        } else {
            $sql .= " WHERE 1=1";
        }
        if ($tglDari) { $sql .= " AND v.tanggal_pesan >= ?"; $params[] = $tglDari; }
        if ($tglSampai) { $sql .= " AND v.tanggal_pesan <= ?"; $params[] = $tglSampai; }
        $sql .= " ORDER BY v.tanggal_pesan DESC";
        return $this->db->query($sql, $params)->getResultArray();
    }

    public function stokGudang(): array
    {
        return $this->db->query("SELECT * FROM v_stok_gudang ORDER BY nama_produk")->getResultArray();
    }

    public function stokSales(?int $salesId = null): array
    {
        $sql = "SELECT * FROM v_stok_sales WHERE 1=1";
        $params = [];
        if ($salesId) { $sql .= " AND sales_id = ?"; $params[] = $salesId; }
        $sql .= " ORDER BY nama_sales, nama_produk";
        return $this->db->query($sql, $params)->getResultArray();
    }

    public function stokToko(?int $salesId = null): array
    {
        $sql = "SELECT toko_id, nama_toko, nama_sales, COUNT(*) as total_item,
                SUM(stok_tersedia) as total_stok
                FROM v_stok_toko WHERE 1=1";
        $params = [];
        if ($salesId) { $sql .= " AND toko_id IN (SELECT id FROM toko WHERE sales_id = ?)"; $params[] = $salesId; }
        $sql .= " GROUP BY toko_id, nama_toko, nama_sales ORDER BY nama_toko";
        return $this->db->query($sql, $params)->getResultArray();
    }

    public function stokTokoDetail(int $tokoId, ?int $salesId = null): array
    {
        $sql = "SELECT * FROM v_stok_toko WHERE toko_id = ?";
        $params = [$tokoId];
        if ($salesId) { $sql .= " AND toko_id IN (SELECT id FROM toko WHERE sales_id = ?)"; $params[] = $salesId; }
        $sql .= " ORDER BY nama_produk";
        return $this->db->query($sql, $params)->getResultArray();
    }

    public function expired(?int $tokoId = null, bool $hampirExpired = false): array
    {
        $sql = "
            SELECT
                p.toko_id,
                t.nama as nama_toko,
                pd.produk_id,
                pr.nama as nama_produk,
                pd.tgl_expired,
                pd.jumlah_titip,
                COALESCE(st.stok_tersedia, 0) as stok_tersedia,
                (pd.tgl_expired < CURRENT_DATE) as sudah_expired
            FROM penitipan_detail pd
            JOIN penitipan p ON p.id = pd.penitipan_id
            JOIN toko t ON t.id = p.toko_id
            JOIN produk pr ON pr.id = pd.produk_id
            LEFT JOIN stok_toko st ON st.toko_id = p.toko_id AND st.produk_id = pd.produk_id
            WHERE (pd.tgl_expired <= CURRENT_DATE OR pd.tgl_expired <= (CURRENT_DATE + INTERVAL '2 days'))
            AND COALESCE(st.stok_tersedia, 0) > 0
        ";
        $params = [];
        if ($tokoId) { $sql .= " AND p.toko_id = ?"; $params[] = $tokoId; }
        $sql .= " ORDER BY pd.tgl_expired ASC, pd.id";
        $result = $this->db->query($sql, $params)->getResultArray();

        if ($hampirExpired) {
            $filtered = [];
            foreach ($result as $r) {
                $tgl = strtotime($r['tgl_expired']);
                $now = strtotime(date('Y-m-d'));
                $daysLeft = ($tgl - $now) / 86400;
                if ($daysLeft >= 0 && $daysLeft <= 7) {
                    $filtered[] = $r;
                }
            }
            return $filtered;
        }
        return $result;
    }

    public function feeSales(?string $tglDari = null, ?string $tglSampai = null, ?int $salesId = null): array
    {
        $sql = "SELECT * FROM v_laporan_fee_sales WHERE 1=1";
        $params = [];
        if ($salesId) { $sql .= " AND sales_id = ?"; $params[] = $salesId; }
        if ($tglDari) { $sql .= " AND tanggal >= ?"; $params[] = $tglDari; }
        if ($tglSampai) { $sql .= " AND tanggal <= ?"; $params[] = $tglSampai; }
        $sql .= " ORDER BY tanggal DESC, nama_sales";
        return $this->db->query($sql, $params)->getResultArray();
    }

    public function feeSalesSummary(?string $tglDari = null, ?string $tglSampai = null, ?int $salesId = null): array
    {
        $sql = "SELECT COALESCE(SUM(v.total_fee), 0) as total_fee FROM v_laporan_fee_sales v WHERE 1=1";
        $params = [];
        if ($salesId) { $sql .= " AND v.sales_id = ?"; $params[] = $salesId; }
        if ($tglDari) { $sql .= " AND v.tanggal >= ?"; $params[] = $tglDari; }
        if ($tglSampai) { $sql .= " AND v.tanggal <= ?"; $params[] = $tglSampai; }
        return $this->db->query($sql, $params)->getRowArray();
    }

    // --- MUTASI STOK ---

    // --- MUTASI GUDANG ---

    public function mutasiGudangRingkasan(?string $tglDari = null, ?string $tglSampai = null): array
    {
        $sql = "WITH mutasi_periode AS (
                    SELECT produk_id,
                        COALESCE(SUM(CASE WHEN jenis IN ('masuk','retur_masuk') THEN jumlah ELSE 0 END), 0) as total_masuk,
                        COALESCE(SUM(CASE WHEN jenis IN ('keluar','retur_keluar','kirim_ke_sales','retur_dari_sales','penyesuaian') THEN jumlah ELSE 0 END), 0) as total_keluar
                    FROM mutasi_gudang
                    WHERE created_at::date >= ? AND created_at::date <= ?
                    GROUP BY produk_id
                ),
                saldo_sebelum AS (
                    SELECT produk_id,
                        COALESCE(SUM(CASE WHEN jenis IN ('masuk','retur_masuk') THEN jumlah ELSE 0 END), 0) -
                        COALESCE(SUM(CASE WHEN jenis IN ('keluar','retur_keluar','kirim_ke_sales','retur_dari_sales','penyesuaian') THEN jumlah ELSE 0 END), 0) as saldo_awal
                    FROM mutasi_gudang
                    WHERE created_at::date < ?
                    GROUP BY produk_id
                )
                SELECT p.id as produk_id, p.kode_produk, p.nama as nama_produk, p.satuan,
                    COALESCE(sb.saldo_awal, 0) as saldo_awal,
                    COALESCE(mp.total_masuk, 0) as total_masuk,
                    COALESCE(mp.total_keluar, 0) as total_keluar,
                    COALESCE(sb.saldo_awal, 0) + COALESCE(mp.total_masuk, 0) - COALESCE(mp.total_keluar, 0) as saldo_akhir
                FROM produk p
                JOIN mutasi_periode mp ON mp.produk_id = p.id
                LEFT JOIN saldo_sebelum sb ON sb.produk_id = p.id
                ORDER BY p.nama";
        return $this->db->query($sql, [$tglDari, $tglSampai, $tglDari])->getResultArray();
    }

    public function mutasiGudangDetail(int $produkId, ?string $tglDari = null, ?string $tglSampai = null): array
    {
        $sql = "SELECT mg.*, p.nama as nama_produk, p.kode_produk, p.satuan, u.nama as dibuat_oleh_nama
                FROM mutasi_gudang mg
                JOIN produk p ON p.id = mg.produk_id
                LEFT JOIN users u ON u.id = mg.dibuat_oleh
                WHERE mg.produk_id = ? AND mg.created_at::date >= ? AND mg.created_at::date <= ?
                ORDER BY mg.created_at ASC";
        return $this->db->query($sql, [$produkId, $tglDari, $tglSampai])->getResultArray();
    }

    // --- MUTASI SALES ---

    public function mutasiSalesRingkasan(?string $tglDari = null, ?string $tglSampai = null, ?int $salesId = null): array
    {
        $params = [$tglDari, $tglSampai];
        if ($salesId) {
            $params[] = $salesId;
        }
        $params[] = $tglDari;
        if ($salesId) {
            $params[] = $salesId;
        }
        $salesFilter = $salesId ? ' AND sales_id = ?' : '';

        $sql = "WITH mutasi_periode AS (
                    SELECT produk_id,
                        COALESCE(SUM(CASE WHEN jenis IN ('masuk_dari_gudang','retur_dari_toko') THEN jumlah ELSE 0 END), 0) as total_masuk,
                        COALESCE(SUM(CASE WHEN jenis IN ('keluar_ke_toko','penyesuaian') THEN jumlah ELSE 0 END), 0) as total_keluar
                    FROM mutasi_sales
                    WHERE created_at::date >= ? AND created_at::date <= ? $salesFilter
                    GROUP BY produk_id
                ),
                saldo_sebelum AS (
                    SELECT produk_id,
                        COALESCE(SUM(CASE WHEN jenis IN ('masuk_dari_gudang','retur_dari_toko') THEN jumlah ELSE 0 END), 0) -
                        COALESCE(SUM(CASE WHEN jenis IN ('keluar_ke_toko','penyesuaian') THEN jumlah ELSE 0 END), 0) as saldo_awal
                    FROM mutasi_sales
                    WHERE created_at::date < ? $salesFilter
                    GROUP BY produk_id
                )
                SELECT p.id as produk_id, p.kode_produk, p.nama as nama_produk, p.satuan,
                    COALESCE(sb.saldo_awal, 0) as saldo_awal,
                    COALESCE(mp.total_masuk, 0) as total_masuk,
                    COALESCE(mp.total_keluar, 0) as total_keluar,
                    COALESCE(sb.saldo_awal, 0) + COALESCE(mp.total_masuk, 0) - COALESCE(mp.total_keluar, 0) as saldo_akhir
                FROM produk p
                JOIN mutasi_periode mp ON mp.produk_id = p.id
                LEFT JOIN saldo_sebelum sb ON sb.produk_id = p.id
                ORDER BY p.nama";
        $result = $this->db->query($sql, $params);
        if ($result === false) {
            $err = $this->db->error();
            throw new \RuntimeException('Query error: ' . $err['message'] . ' | SQL: ' . $sql);
        }
        return $result->getResultArray();
    }

    public function mutasiSalesDetail(int $produkId, ?string $tglDari = null, ?string $tglSampai = null, ?int $salesId = null): array
    {
        $sql = "SELECT ms.*, p.nama as nama_produk, p.kode_produk, p.satuan, u.nama as sales_nama
                FROM mutasi_sales ms
                JOIN produk p ON p.id = ms.produk_id
                LEFT JOIN users u ON u.id = ms.sales_id
                WHERE ms.produk_id = ? AND ms.created_at::date >= ? AND ms.created_at::date <= ?";
        $params = [$produkId, $tglDari, $tglSampai];
        if ($salesId) {
            $sql .= " AND ms.sales_id = ?";
            $params[] = $salesId;
        }
        $sql .= " ORDER BY ms.created_at ASC";
        return $this->db->query($sql, $params)->getResultArray();
    }
}
