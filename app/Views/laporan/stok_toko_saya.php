<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Stok Toko Area Saya</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>No</th><th>Toko</th><th>Sales</th><th>Total Item</th><th>Total Stok</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($records as $i => $r): ?>
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
                        <?php if (empty($records)): ?><tr><td colspan="99" class="text-center">Tidak ada data</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
