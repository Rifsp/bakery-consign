<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola User</h1>
        <a href="<?= base_url('/user/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah User
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="input-group mr-2">
                    <input type="text" class="form-control" name="search" placeholder="Cari nama/username/email" value="<?= esc($search) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <select name="role" class="form-control mr-2" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    <option value="admin" <?= $filterRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="sales" <?= $filterRole === 'sales' ? 'selected' : '' ?>>Sales</option>
                </select>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($r['username']) ?></td>
                            <td><?= esc($r['nama']) ?></td>
                            <td><span class="badge bg-<?= $r['role'] === 'admin' ? 'danger' : 'primary' ?>"><?= ucfirst($r['role']) ?></span></td>
                            <td><?= esc($r['email'] ?? '-') ?></td>
                            <td><?= esc($r['telepon'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-<?= $r['is_aktif'] ? 'success' : 'secondary' ?>">
                                    <?= $r['is_aktif'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('/user/edit/' . $r['id']) ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="<?= base_url('/user/delete/' . $r['id']) ?>" method="POST" style="display:inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-<?= $r['is_aktif'] ? 'danger' : 'success' ?> btn-delete-confirm" data-confirm="<?= $r['is_aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?> user ini?">
                                        <i class="fas fa-<?= $r['is_aktif'] ? 'ban' : 'check' ?>"></i>
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
