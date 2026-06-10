<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Stok Sales</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <form class="form-inline" method="GET">
                <select name="sales_id" class="form-control mr-2">
                    <option value="">Semua Sales</option>
                    <?php foreach ($salesList as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $filterSales == $s['id'] ? 'selected' : '' ?>><?= esc($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>No</th><th>Sales</th><th>Kode</th><th>Produk</th><th>Satuan</th><th>Stok</th><th>Update</th></tr></thead>
                    <tbody>
                        <?php foreach ($records as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($r['nama_sales']) ?></td>
                            <td><?= esc($r['kode_produk']) ?></td>
                            <td><?= esc($r['nama_produk']) ?></td>
                            <td><?= esc($r['satuan']) ?></td>
                            <td class="text-right"><?= $r['stok_tersedia'] ?></td>
                            <td><?= $r['updated_at'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($records)): ?><tr><td colspan="99" class="text-center">Tidak ada data</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
