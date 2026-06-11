<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data <?= $config->title ?></h1>
        <a href="<?= base_url($config->route . '/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah <?= $config->title ?>
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
                            <th>Nama Toko</th>
                            <th>Pemilik</th>
                            <th>Kota</th>
                            <th>Telepon</th>
                            <th>Sales</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $i => $record): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($record['kode_toko']) ?></td>
                            <td><?= esc($record['nama']) ?></td>
                            <td><?= esc($record['pemilik']) ?></td>
                            <td><?= esc($record['kota']) ?></td>
                            <td><?= esc($record['telepon']) ?></td>
                            <td><?= esc($record['sales_id']) ?></td>
                            <td><?= $record['is_aktif'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' ?></td>
                            <td>
                                <a href="<?= base_url($config->route . '/edit/' . $record['id']) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= base_url($config->route . '/delete/' . $record['id']) ?>" method="POST" style="display:inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger btn-delete-confirm" data-confirm="Yakin hapus data ini?">
                                        <i class="fas fa-trash"></i>
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
