<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Produk</h1>
        <a href="<?= base_url('/produk/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Produk
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>HPP</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $i => $record): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($record['kode_produk']) ?></td>
                            <td><?= esc($record['nama']) ?></td>
                            <td><?= esc($record['kategori_id']) ?></td>
                            <td><?= esc($record['satuan']) ?></td>
                            <td><?= esc($record['hpp']) ?></td>
                            <td><?= $record['is_aktif'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Nonaktif</span>' ?></td>
                            <td>
                                <a href="<?= base_url('/produk/edit/' . $record['id']) ?>" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= base_url('/harga-jual?produk_id=' . $record['id']) ?>" class="btn btn-sm btn-info" title="Harga Jual">
                                    <i class="fas fa-tag"></i>
                                </a>
                                <form action="<?= base_url('/produk/delete/' . $record['id']) ?>" method="POST" style="display:inline">
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