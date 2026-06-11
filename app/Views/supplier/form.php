<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?= base_url($config->route . ($record ? '/update' : '/store') . ($record ? '/' . $record['id'] : '')) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="kode_supplier">Kode Supplier</label>
                    <input type="text" class="form-control" id="kode_supplier" name="kode_supplier" value="<?= old('kode_supplier', $record['kode_supplier'] ?? '') ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="nama">Nama Supplier</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama', $record['nama'] ?? '') ?>" placeholder="Masukkan nama supplier" required>
                </div>

                <div class="form-group">
                    <label for="kontak_person">Kontak Person</label>
                    <input type="text" class="form-control" id="kontak_person" name="kontak_person" value="<?= old('kontak_person', $record['kontak_person'] ?? '') ?>" placeholder="Nama kontak person" >
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap" ><?= old('alamat', $record['alamat'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="telepon">Telepon</label>
                    <input type="text" class="form-control" id="telepon" name="telepon" value="<?= old('telepon', $record['telepon'] ?? '') ?>" placeholder="021-xxxxxxx" >
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $record['email'] ?? '') ?>" placeholder="email@contoh.com" >
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_aktif" name="is_aktif" value="1" <?= old('is_aktif', $record['is_aktif'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_aktif">Status</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url($config->route) ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
