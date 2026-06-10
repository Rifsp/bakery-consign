<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <div>
            <?php if ($po['status'] === 'pending' || $po['status'] === 'sebagian'): ?>
                <a href="<?= base_url('/pembelian/terima/' . $po['id']) ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-truck-loading"></i> Terima Barang
                </a>
            <?php endif; ?>
            <a href="<?= base_url('/pembelian') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
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
                    <strong>Nomor PO:</strong> <?= esc($po['nomor_po']) ?>
                </div>
                <div class="col-md-3">
                    <strong>Supplier:</strong> <?= esc($po['supplier_nama'] ?? '-') ?>
                </div>
                <div class="col-md-2">
                    <strong>Tgl Pesan:</strong> <?= esc($po['tanggal_pesan']) ?>
                </div>
                <div class="col-md-2">
                    <strong>Status:</strong>
                    <?php if ($po['status'] === 'pending'): ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php elseif ($po['status'] === 'sebagian'): ?>
                        <span class="badge bg-info text-dark">Sebagian</span>
                    <?php elseif ($po['status'] === 'diterima'): ?>
                        <span class="badge bg-success">Diterima</span>
                    <?php elseif ($po['status'] === 'dibatalkan'): ?>
                        <span class="badge bg-secondary">Dibatalkan</span>
                    <?php endif; ?>
                </div>
                <div class="col-md-2">
                    <strong>Total:</strong> Rp <?= number_format($po['total_nilai'] ?? 0, 0, ',', '.') ?>
                </div>
            </div>
            <?php if ($po['catatan']): ?>
            <div class="row mt-2">
                <div class="col-12">
                    <strong>Catatan:</strong> <?= esc($po['catatan']) ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($po['tanggal_terima']): ?>
            <div class="row mt-2">
                <div class="col-12">
                    <strong>Tgl Terima:</strong> <?= esc($po['tanggal_terima']) ?>
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
                            <th>Qty Pesan</th>
                            <th>Qty Terima</th>
                            <th>Sisa</th>
                            <th>Harga Beli</th>
                            <th>Subtotal</th>
                            <th>Tgl Expired</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $i => $d): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($d['produk_nama']) ?></td>
                            <td class="text-right"><?= number_format($d['jumlah_pesan']) ?></td>
                            <td class="text-right"><?= number_format($d['jumlah_terima']) ?></td>
                            <td class="text-right"><?= number_format($d['jumlah_pesan'] - $d['jumlah_terima']) ?></td>
                            <td class="text-right"><?= number_format($d['harga_beli'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($d['jumlah_terima'] * $d['harga_beli'], 0, ',', '.') ?></td>
                            <td><?= $d['tgl_expired'] ? esc($d['tgl_expired']) : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
