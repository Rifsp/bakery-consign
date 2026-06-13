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
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <p class="mb-0"><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url($config->route . ($record ? '/update' : '/store') . ($record ? '/' . $record['id'] : '')) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="kode_toko">Kode Toko</label>
                    <input type="text" class="form-control" id="kode_toko" name="kode_toko" value="<?= old('kode_toko', $record['kode_toko'] ?? '') ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="nama">Nama Toko</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama', $record['nama'] ?? '') ?>" placeholder="Masukkan nama toko" required>
                </div>

                <div class="form-group">
                    <label for="pemilik">Pemilik</label>
                    <input type="text" class="form-control" id="pemilik" name="pemilik" value="<?= old('pemilik', $record['pemilik'] ?? '') ?>" placeholder="Nama pemilik toko" >
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap" ><?= old('alamat', $record['alamat'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="kelurahan">Kelurahan</label>
                    <input type="text" class="form-control" id="kelurahan" name="kelurahan" value="<?= old('kelurahan', $record['kelurahan'] ?? '') ?>" placeholder="Kelurahan" >
                </div>

                <div class="form-group">
                    <label for="kecamatan">Kecamatan</label>
                    <input type="text" class="form-control" id="kecamatan" name="kecamatan" value="<?= old('kecamatan', $record['kecamatan'] ?? '') ?>" placeholder="Kecamatan" >
                </div>

                <div class="form-group">
                    <label for="kota">Kota</label>
                    <input type="text" class="form-control" id="kota" name="kota" value="<?= old('kota', $record['kota'] ?? '') ?>" placeholder="Kota" >
                </div>

                <div class="form-group">
                    <label for="telepon">Telepon</label>
                    <input type="text" class="form-control" id="telepon" name="telepon" value="<?= old('telepon', $record['telepon'] ?? '') ?>" placeholder="021-xxxxxxx" >
                </div>

                <div class="form-group">
                    <label for="sales_id">Sales</label>
                    <select class="form-control select2" id="sales_id" name="sales_id" >
                        <option value="">-- Pilih --</option>
                        <?php 
                        $salesOptions = (new \App\Models\UserModel())->where('role', 'sales')->where('is_aktif', TRUE)->findAll();
                        foreach ($salesOptions as $sales):
                        ?>
                        <option value="<?= $sales['id'] ?>" <?= old('sales_id', $record['sales_id'] ?? '') == $sales['id'] ? 'selected' : '' ?>>
                            <?= $sales['nama'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
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
