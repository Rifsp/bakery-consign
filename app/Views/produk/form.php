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
                    <label for="kode_produk">Kode</label>
                    <input type="text" class="form-control" id="kode_produk" name="kode_produk" value="<?= old('kode_produk', $record['kode_produk'] ?? '') ?>" placeholder="" >
                </div>

                <div class="form-group">
                    <label for="nama">Nama Produk</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama', $record['nama'] ?? '') ?>" placeholder="Masukkan nama produk" required>
                </div>

                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select class="form-control" id="kategori_id" name="kategori_id" >
                        <option value="">-- Pilih --</option>
                        <?php $kategoris = (new \App\Models\KategoriProdukModel())->findAll(); ?>
                        <?php foreach ($kategoris as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= old('kategori_id', $record['kategori_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                            <?= $k['nama'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="satuan">Satuan</label>
                    <select class="form-control" id="satuan" name="satuan" >
                        <option value="">-- Pilih --</option>
                        <?php $satuans = ['pcs' => 'Pcs', 'pack' => 'Pack', 'kg' => 'Kg', 'gram' => 'Gram', 'lusin' => 'Lusin', 'box' => 'Box']; ?>
                        <?php foreach ($satuans as $val => $lbl): ?>
                        <option value="<?= $val ?>" <?= old('satuan', $record['satuan'] ?? '') == $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="hpp">HPP</label>
                    <input type="number" class="form-control" id="hpp" name="hpp" value="<?= old('hpp', $record['hpp'] ?? '') ?>" placeholder="0" >
                </div>

                <div class="form-group">
                    <label for="shelf_life_hari">Shelf Life (Hari)</label>
                    <input type="number" class="form-control" id="shelf_life_hari" name="shelf_life_hari" value="<?= old('shelf_life_hari', $record['shelf_life_hari'] ?? '') ?>" placeholder="3" >
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi produk" ><?= old('deskripsi', $record['deskripsi'] ?? '') ?></textarea>
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