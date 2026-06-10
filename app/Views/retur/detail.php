<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <a href="<?= base_url('/retur') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-md-3">
                    <strong>Nomor Retur:</strong> <?= esc($header['nomor_retur']) ?>
                </div>
                <div class="col-md-3">
                    <strong>Toko:</strong> <?= esc($header['toko_nama'] ?? '-') ?>
                </div>
                <div class="col-md-2">
                    <strong>Sales:</strong> <?= esc($header['sales_nama'] ?? '-') ?>
                </div>
                <div class="col-md-2">
                    <strong>Tanggal:</strong> <?= esc($header['tanggal']) ?>
                </div>
                <div class="col-md-2">
                    <strong>Status:</strong>
                    <span class="badge <?= $statusLabels[$header['status']]['class'] ?? 'bg-secondary' ?>">
                        <?= $statusLabels[$header['status']]['label'] ?? ucfirst($header['status']) ?>
                    </span>
                </div>
            </div>
            <?php if ($header['alasan']): ?>
            <div class="row mt-2">
                <div class="col-12">
                    <strong>Alasan:</strong> <?= esc($header['alasan']) ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($header['catatan']): ?>
            <div class="row mt-2">
                <div class="col-12">
                    <strong>Catatan:</strong> <?= esc($header['catatan']) ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($kunjungan): ?>
            <div class="row mt-2">
                <div class="col-12">
                    <strong>Kunjungan:</strong>
                    <a href="<?= base_url('/kunjungan/detail/' . $kunjungan['id']) ?>">
                        <?= esc($kunjungan['nomor_kunjungan']) ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($header['status'] === 'pending'): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <form action="<?= base_url('/retur/approve/' . $header['id']) ?>" method="POST" style="display:inline" onsubmit="return confirm('Setujui retur ini? Stok akan diproses.')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Setujui</button>
                    </form>
                    <form action="<?= base_url('/retur/reject/' . $header['id']) ?>" method="POST" style="display:inline" onsubmit="return confirm('Tolak retur ini?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Tolak</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Kondisi</th>
                            <th>Tgl Expired</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $i => $d): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($d['produk_nama']) ?></td>
                            <td class="text-right"><?= number_format($d['jumlah_retur']) ?></td>
                            <td>
                                <span class="badge <?= $d['kondisi'] === 'baik' ? 'bg-success' : ($d['kondisi'] === 'rusak' ? 'bg-warning text-dark' : 'bg-danger') ?>">
                                    <?= ucfirst($d['kondisi']) ?>
                                </span>
                            </td>
                            <td><?= esc($d['tgl_expired']) ?></td>
                            <td><?= esc($d['keterangan']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
