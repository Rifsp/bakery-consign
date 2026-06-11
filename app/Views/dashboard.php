<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4 animate-fade-in">
        <h1 class="h3 mb-0" style="color: var(--bakery-brown-dark);">Dashboard</h1>
        <div>
            <span class="text-muted small"><i class="fas fa-calendar-alt me-1"></i> <?= date('d F Y') ?></span>
        </div>
    </div>

    <div class="row animate-fade-in animate-fade-in-delay-1">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Kunjungan Hari Ini</div>
                            <div class="h3 mb-0 fw-bold" style="color: var(--bakery-brown-dark)"><?= $totalKunjungan ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store-alt fa-3x" style="color: var(--bakery-gold-light);"></i>
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
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Penjualan Hari Ini</div>
                            <div class="h3 mb-0 fw-bold" style="color: var(--bakery-brown-dark)">Rp <?= number_format($totalPenjualan, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-3x" style="color: var(--bakery-gold-light);"></i>
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
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Penitipan Hari Ini</div>
                            <div class="h3 mb-0 fw-bold" style="color: var(--bakery-brown-dark)"><?= $totalPenitipan ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-people-arrows fa-3x" style="color: var(--bakery-gold-light);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($role === 'admin' && $totalReturPending > 0): ?>
    <div class="alert alert-warning alert-dismissible fade show animate-fade-in animate-fade-in-delay-1" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Ada <strong><?= $totalReturPending ?></strong> retur pending yang perlu disetujui.
        <a href="<?= base_url('/retur') ?>" class="alert-link">Lihat Retur</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row animate-fade-in animate-fade-in-delay-2">
        <div class="col-xl-7 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold" style="color: var(--bakery-brown);">
                        <i class="fas fa-chart-area me-2"></i>Tren Penjualan 7 Hari
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-5 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold" style="color: var(--bakery-brown);">
                        <i class="fas fa-store-alt me-2"></i>Kunjungan Terbaru
                    </h6>
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
    </div>

    <div class="row animate-fade-in animate-fade-in-delay-3">
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold" style="color: var(--bakery-brown);">
                        <i class="fas fa-shopping-cart me-2"></i>Penjualan Terbaru
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr><th>No</th><th>Nomor Jual</th><th>Toko</th><th>Sales</th><th>Total</th><th>Aksi</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($penjualanTerbaru)): ?>
                                <tr><td colspan="6" class="text-center">Belum ada penjualan hari ini</td></tr>
                                <?php else: ?>
                                <?php foreach ($penjualanTerbaru as $i => $p): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($p['nomor_jual']) ?></td>
                                    <td><?= esc($p['toko_nama']) ?></td>
                                    <td><?= esc($p['sales_nama']) ?></td>
                                    <td class="text-end fw-bold">Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
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

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('salesChart');
    if (!ctx) return;
    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: <?= json_encode($chartValues) ?>,
                fill: true,
                borderColor: '#8B5E3C',
                backgroundColor: 'rgba(139, 94, 60, 0.12)',
                borderWidth: 3,
                pointBackgroundColor: '#8B5E3C',
                pointBorderColor: '#fff',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function(v) { return 'Rp' + v.toLocaleString('id-ID'); } },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
