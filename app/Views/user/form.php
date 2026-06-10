<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('/user/' . ($record ? 'update' : 'store') . ($record ? '/' . $record['id'] : '')) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= old('username', $record['username'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama', $record['nama'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password <?= $record ? '(kosongkan jika tidak diubah)' : '' ?></label>
                    <input type="password" class="form-control" id="password" name="password" <?= $record ? '' : 'required' ?> minlength="6">
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" <?= old('role', $record['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="sales" <?= old('role', $record['role'] ?? '') === 'sales' ? 'selected' : '' ?>>Sales</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $record['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="telepon">Telepon</label>
                    <input type="text" class="form-control" id="telepon" name="telepon" value="<?= old('telepon', $record['telepon'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_aktif" name="is_aktif" value="1" <?= old('is_aktif', $record['is_aktif'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_aktif">Aktif</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('/user') ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
