<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <div>
            <a href="<?= base_url('/harga-jual/create?produk_id=' . $produk['id']) ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Harga
            </a>
            <a href="<?= base_url('/produk') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center">
            <div class="mr-3">
                <strong>Produk:</strong> <?= esc($produk['nama']) ?> (<?= esc($produk['kode_produk']) ?>)
            </div>
            <form method="GET" class="row g-2 align-items-center ms-auto">
                <input type="hidden" name="produk_id" value="<?= $produk['id'] ?>">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= $search ?? '' ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Harga</th>
                            <th>Harga</th>
                            <th>Fee Sales</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $i => $record): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($record['nama_harga']) ?></td>
                            <td><?= number_format($record['harga'], 0, ',', '.') ?></td>
                            <td><?= number_format($record['fee_sales'], 0, ',', '.') ?></td>
                            <td><?= $record['is_aktif'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Nonaktif</span>' ?></td>
                            <td>
                                <a href="<?= base_url('/harga-jual/edit/' . $record['id']) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= base_url('/harga-jual/delete/' . $record['id']) ?>" method="POST" style="display:inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm <?= $record['is_aktif'] ? 'btn-warning' : 'btn-success' ?> btn-delete-confirm" data-confirm="Yakin <?= $record['is_aktif'] ? 'nonaktifkan' : 'aktifkan' ?> data ini?">
                                        <i class="fas <?= $record['is_aktif'] ? 'fa-ban' : 'fa-check' ?>"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
