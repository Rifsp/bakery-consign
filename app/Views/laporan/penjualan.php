<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Rekap Penjualan</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <form class="row g-2 align-items-center" method="GET">
                <input type="date" name="tgl_dari" class="form-control mr-2" value="<?= $tglDari ?>">
                <input type="date" name="tgl_sampai" class="form-control mr-2" value="<?= $tglSampai ?>">
                <select name="sales_id" class="form-control mr-2">
                    <option value="">Semua Sales</option>
                    <?php foreach ($salesList as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $filterSales == $s['id'] ? 'selected' : '' ?>><?= esc($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="toko_id" class="form-control mr-2">
                    <option value="">Semua Toko</option>
                    <?php foreach ($tokoList as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $filterToko == $t['id'] ? 'selected' : '' ?>><?= esc($t['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-datatable">
                    <thead><tr><th>No</th><th>Nomor Jual</th><th>Tgl</th><th>Toko</th><th>Sales</th><th>Total Harga</th><th>Total Fee</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php $sumHarga = 0; $sumFee = 0; ?>
                        <?php foreach ($records as $i => $r): ?>
                        <?php $sumHarga += $r['total_harga']; $sumFee += $r['total_fee']; ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($r['nomor_jual']) ?></td>
                            <td><?= $r['tanggal'] ?></td>
                            <td><?= esc($r['toko_nama']) ?></td>
                            <td><?= esc($r['sales_nama']) ?></td>
                            <td class="text-right"><?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($r['total_fee'], 0, ',', '.') ?></td>
                            <td><a href="<?= base_url('/kunjungan/detail/' . $r['kunjungan_id']) ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <td colspan="5" class="text-right">Total</td>
                            <td class="text-right"><?= number_format($sumHarga, 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($sumFee, 0, ',', '.') ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
