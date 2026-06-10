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
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url($config->route . ($record ? '/update' : '/store') . ($record ? '/' . $record['id'] : '')) ?>" method="POST">
                <?= csrf_field() ?>

                <input type="hidden" name="produk_id" value="<?= old('produk_id', $record['produk_id'] ?? $produk['id']) ?>">

                <div class="form-group">
                    <label>Produk</label>
                    <div class="form-control-plaintext">
                        <strong><?= esc($produk['nama']) ?></strong> (<?= esc($produk['kode_produk']) ?>)
                    </div>
                </div>

                <div class="form-group">
                    <label for="nama_harga">Nama Harga</label>
                    <input type="text" class="form-control" id="nama_harga" name="nama_harga" value="<?= old('nama_harga', $record['nama_harga'] ?? '') ?>" placeholder="Contoh: Harga 1, Harga Grosir" required>
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" class="form-control" id="harga" name="harga" value="<?= old('harga', $record['harga'] ?? '') ?>" placeholder="0" required>
                </div>

                <div class="form-group">
                    <label for="fee_sales">Fee Sales</label>
                    <input type="number" class="form-control" id="fee_sales" name="fee_sales" value="<?= old('fee_sales', $record['fee_sales'] ?? '') ?>" placeholder="0">
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_aktif" name="is_aktif" value="1" <?= old('is_aktif', $record['is_aktif'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_aktif">Aktif</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url($config->route . '?produk_id=' . ($record['produk_id'] ?? $produk['id'])) ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
