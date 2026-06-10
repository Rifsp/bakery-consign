<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        $salesId = (int) session()->get('id');
        $today = date('Y-m-d');
        $filter = $role === 'sales' ? " AND sales_id = {$salesId}" : '';
        $filterK = $role === 'sales' ? " AND k.sales_id = {$salesId}" : '';
        $filterPj = $role === 'sales' ? " AND pj.sales_id = {$salesId}" : '';

        $db = \Config\Database::connect();

        $totalKunjungan = $db->query("SELECT COUNT(*) as total FROM kunjungan WHERE tanggal = '{$today}'{$filter}")->getRow()->total;
        $totalPenjualan = $db->query("SELECT COALESCE(SUM(total_harga),0) as total FROM penjualan WHERE tanggal = '{$today}'{$filter}")->getRow()->total;
        $totalPenitipan = $db->query("SELECT COUNT(*) as total FROM penitipan WHERE tanggal_titip = '{$today}'{$filter}")->getRow()->total;
        $totalReturPending = $db->query("SELECT COUNT(*) as total FROM retur WHERE status = 'pending'")->getRow()->total;

        $penjualanTerbaru = $db->query("
            SELECT pj.id, pj.nomor_jual, pj.tanggal, pj.total_harga, pj.kunjungan_id, t.nama as toko_nama, u.nama as sales_nama
            FROM penjualan pj
            JOIN toko t ON t.id = pj.toko_id
            JOIN users u ON u.id = pj.sales_id
            WHERE pj.tanggal = '{$today}'{$filterPj}
            ORDER BY pj.id DESC LIMIT 5
        ")->getResultArray();

        $kunjunganTerbaru = $db->query("
            SELECT k.id, k.nomor_kunjungan, k.tanggal, k.status, t.nama as toko_nama
            FROM kunjungan k
            JOIN toko t ON t.id = k.toko_id
            WHERE k.tanggal = '{$today}'{$filterK}
            ORDER BY k.id DESC LIMIT 5
        ")->getResultArray();

        $data = [
            'nama' => session()->get('nama'),
            'role' => $role,
            'totalKunjungan' => $totalKunjungan,
            'totalPenjualan' => $totalPenjualan,
            'totalPenitipan' => $totalPenitipan,
            'totalReturPending' => $totalReturPending,
            'penjualanTerbaru' => $penjualanTerbaru,
            'kunjunganTerbaru' => $kunjunganTerbaru,
        ];

        return view('dashboard', $data);
    }
}
