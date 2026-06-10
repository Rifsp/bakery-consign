<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <a href="<?= base_url('/penitipan') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-md-3">
                    <strong>Nomor Titip:</strong> <?= esc($header['nomor_titip']) ?>
                </div>
                <div class="col-md-3">
                    <strong>Toko:</strong> <?= esc($header['toko_nama'] ?? '-') ?>
                </div>
                <div class="col-md-2">
                    <strong>Sales:</strong> <?= esc($header['sales_nama'] ?? '-') ?>
                </div>
                <div class="col-md-2">
                    <strong>Tgl Titip:</strong> <?= esc($header['tanggal_titip']) ?>
                </div>
                <div class="col-md-2">
                    <strong>Status:</strong>
                    <span class="badge <?= $statusLabels[$header['status']]['class'] ?? 'bg-secondary' ?>">
                        <?= $statusLabels[$header['status']]['label'] ?? ucfirst($header['status']) ?>
                    </span>
                </div>
            </div>
            <?php if ($header['catatan']): ?>
            <div class="row mt-2">
                <div class="col-12">
                    <strong>Catatan:</strong> <?= esc($header['catatan']) ?>
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
                            <th>Tier Harga</th>
                            <th>Qty</th>
                            <th>Tgl Expired</th>
                            <th>Harga Satuan</th>
                            <?php if (session()->get('role') !== 'sales'): ?>
                            <th>Fee Satuan</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $i => $d): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($d['produk_nama']) ?></td>
                            <td><?= esc($d['tier_nama'] ?? '-') ?></td>
                            <td class="text-right"><?= number_format($d['jumlah_titip']) ?></td>
                            <td><?= esc($d['tgl_expired']) ?></td>
                            <td class="text-right"><?= number_format($d['harga_satuan'], 0, ',', '.') ?></td>
                            <?php if (session()->get('role') !== 'sales'): ?>
                            <td class="text-right"><?= number_format($d['fee_satuan'], 0, ',', '.') ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
