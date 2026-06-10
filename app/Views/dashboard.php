<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <div class="row">

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Kunjungan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalKunjungan ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Penjualan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($totalPenjualan, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Penitipan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPenitipan ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php if ($role === 'admin' && $totalReturPending > 0): ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle"></i>
        Ada <strong><?= $totalReturPending ?></strong> retur pending yang perlu disetujui.
        <a href="<?= base_url('/retur') ?>" class="alert-link">Lihat Retur</a>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php endif; ?>

    <div class="row">

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kunjungan Terbaru Hari Ini</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr><th>No</th><th>Nomor</th><th>Toko</th><th>Status</th><th>Aksi</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($kunjunganTerbaru)): ?>
                                <tr><td colspan="5" class="text-center">Belum ada kunjungan hari ini</td></tr>
                                <?php else: ?>
                                <?php foreach ($kunjunganTerbaru as $i => $k): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($k['nomor_kunjungan']) ?></td>
                                    <td><?= esc($k['toko_nama']) ?></td>
                                    <td><span class="badge bg-<?= $k['status'] === 'selesai' ? 'success' : 'warning text-dark' ?>"><?= ucfirst($k['status']) ?></span></td>
                                    <td><a href="<?= base_url('/kunjungan/detail/' . $k['id']) ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Penjualan Terbaru Hari Ini</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr><th>No</th><th>Nomor Jual</th><th>Toko</th><th>Total</th><th>Aksi</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($penjualanTerbaru)): ?>
                                <tr><td colspan="5" class="text-center">Belum ada penjualan hari ini</td></tr>
                                <?php else: ?>
                                <?php foreach ($penjualanTerbaru as $i => $p): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($p['nomor_jual']) ?></td>
                                    <td><?= esc($p['toko_nama']) ?></td>
                                    <td class="text-right">Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
                                    <td><a href="<?= base_url('/kunjungan/detail/' . $p['kunjungan_id']) ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-gray-800">Selamat Datang, <?= esc($nama) ?>!</h6>
                </div>
                <div class="card-body">
                    Anda login sebagai <strong><?= esc(ucfirst($role)) ?></strong>.
                    Dashboard menampilkan ringkasan aktivitas hari ini.
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
