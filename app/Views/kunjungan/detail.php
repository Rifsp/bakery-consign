<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <a href="<?= base_url('/kunjungan') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-md-3">
                    <strong>Nomor:</strong> <?= esc($header['nomor_kunjungan']) ?>
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
            <?php if ($header['catatan']): ?>
            <div class="row mt-2">
                <div class="col-12">
                    <strong>Catatan:</strong> <?= esc($header['catatan']) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($penjualan): ?>
    <div class="card shadow mb-4">
        <div class="card-header">
            <strong>Penjualan</strong>
            <span class="ml-3 text-muted"><?= esc($penjualan['nomor_jual']) ?></span>
            <span class="ml-3">Total: Rp <?= number_format($penjualan['total_harga'], 0, ',', '.') ?></span>
            <?php if (session()->get('role') !== 'sales'): ?>
            <span class="ml-3 text-info">Fee: Rp <?= number_format($penjualan['total_fee'], 0, ',', '.') ?></span>
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
                            <th>Harga Satuan</th>
                            <?php if (session()->get('role') !== 'sales'): ?>
                            <th>Fee Satuan</th>
                            <th>HPP Satuan</th>
                            <?php endif; ?>
                            <th>Subtotal Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($penjualanDetails as $i => $d): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($d['produk_nama']) ?></td>
                            <td><?= esc($d['tier_nama'] ?? '-') ?></td>
                            <td class="text-right"><?= number_format($d['jumlah_terjual']) ?></td>
                            <td class="text-right"><?= number_format($d['harga_satuan'], 0, ',', '.') ?></td>
                            <?php if (session()->get('role') !== 'sales'): ?>
                            <td class="text-right"><?= number_format($d['fee_satuan'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($d['hpp_satuan'], 0, ',', '.') ?></td>
                            <?php endif; ?>
                            <td class="text-right"><?= number_format($d['subtotal_harga'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($retur): ?>
    <div class="card shadow mb-4">
        <div class="card-header">
            <strong>Retur</strong>
            <span class="ml-3 text-muted"><?= esc($retur['nomor_retur']) ?></span>
            <span class="ml-3">
                Status:
                <span class="badge <?= $retur['status'] === 'disetujui' ? 'bg-success' : ($retur['status'] === 'ditolak' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                    <?= ucfirst($retur['status']) ?>
                </span>
            </span>
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
                        <?php foreach ($returDetails as $i => $d): ?>
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
    <?php endif; ?>

    <?php if (!$penjualan && !$retur): ?>
    <div class="card shadow mb-4">
        <div class="card-body text-center text-muted">
            Tidak ada penjualan atau retur pada kunjungan ini.
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
