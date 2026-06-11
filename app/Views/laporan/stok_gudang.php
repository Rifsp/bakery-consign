<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Stok Gudang Pusat</h1>
        <a href="<?= base_url('/laporan/export-csv/stok_gudang') ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export CSV</a>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-datatable">
                    <thead><tr><th>No</th><th>Kode</th><th>Produk</th><th>Satuan</th><th>HPP</th><th>Stok</th><th>Update</th></tr></thead>
                    <tbody>
                        <?php foreach ($records as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($r['kode_produk']) ?></td>
                            <td><?= esc($r['nama_produk']) ?></td>
                            <td><?= esc($r['satuan']) ?></td>
                            <td class="text-right"><?= number_format($r['hpp'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= $r['stok_tersedia'] ?></td>
                            <td><?= $r['updated_at'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
