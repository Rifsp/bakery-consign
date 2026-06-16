<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Stok Toko</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <form class="row g-2 align-items-center" method="GET">
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
                <table class="table table-bordered table-sm table-datatable">
                    <thead><tr><th>No</th><th>Toko</th><th>Sales</th><th>Total Item</th><th>Total Stok</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php $sumItem = 0; $sumStok = 0; ?>
                        <?php foreach ($records as $i => $r): ?>
                        <?php $sumItem += $r['total_item']; $sumStok += $r['total_stok']; ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($r['nama_toko']) ?></td>
                            <td><?= esc($r['nama_sales']) ?></td>
                            <td class="text-center"><?= $r['total_item'] ?></td>
                            <td class="text-right"><?= $r['total_stok'] ?></td>
                            <td>
                                <a href="<?= base_url('/laporan/stok-toko-detail/' . $r['toko_id']) ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Detail</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <td colspan="3" class="text-right">Total</td>
                            <td class="text-center"><?= number_format($sumItem, 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($sumStok, 0, ',', '.') ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
