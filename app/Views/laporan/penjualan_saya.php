<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Penjualan Saya</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <form class="form-inline" method="GET">
                <input type="date" name="tgl_dari" class="form-control mr-2" value="<?= $tglDari ?>">
                <input type="date" name="tgl_sampai" class="form-control mr-2" value="<?= $tglSampai ?>">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>No</th><th>Nomor Jual</th><th>Tgl</th><th>Toko</th><th>Total Harga</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($records as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($r['nomor_jual']) ?></td>
                            <td><?= $r['tanggal'] ?></td>
                            <td><?= esc($r['toko_nama']) ?></td>
                            <td class="text-right"><?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                            <td><a href="<?= base_url('/kunjungan/detail/' . $r['kunjungan_id']) ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($records)): ?><tr><td colspan="99" class="text-center">Tidak ada data</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
