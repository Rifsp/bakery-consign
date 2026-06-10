<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Terima Barang - <?= esc($po['nomor_po']) ?></h1>
        <a href="<?= base_url('/pembelian/detail/' . $po['id']) ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header">
            <strong>Supplier:</strong> <?= esc($po['supplier_nama'] ?? '-') ?> &nbsp;|&nbsp;
            <strong>Tgl PO:</strong> <?= esc($po['tanggal_pesan']) ?>
        </div>
        <div class="card-body">
            <form action="<?= base_url('/pembelian/proses-terima/' . $po['id']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th>Qty Pesan</th>
                                <th>Sudah Diterima</th>
                                <th>Sisa</th>
                                <th>Qty Terima Baru</th>
                                <th>Tgl Expired</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details as $i => $d): 
                                $sisa = $d['jumlah_pesan'] - $d['jumlah_terima'];
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($d['produk_nama']) ?></td>
                                <td class="text-right"><?= number_format($d['jumlah_pesan']) ?></td>
                                <td class="text-right"><?= number_format($d['jumlah_terima']) ?></td>
                                <td class="text-right"><?= number_format($sisa) ?></td>
                                <td>
                                    <?php if ($sisa > 0): ?>
                                        <input type="number" name="items[<?= $d['id'] ?>][qty_terima]" class="form-control" min="0" max="<?= $sisa ?>" placeholder="0">
                                    <?php else: ?>
                                        <span class="text-success"><i class="fas fa-check"></i> Lengkap</span>
                                        <input type="hidden" name="items[<?= $d['id'] ?>][qty_terima]" value="0">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($sisa > 0): ?>
                                        <input type="date" name="items[<?= $d['id'] ?>][tgl_expired]" class="form-control">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <hr>
                <button type="submit" class="btn btn-success">Simpan Penerimaan</button>
                <a href="<?= base_url('/pembelian/detail/' . $po['id']) ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
