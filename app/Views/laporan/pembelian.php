<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Pembelian</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <form class="form-inline" method="GET">
                <input type="date" name="tgl_dari" class="form-control mr-2" value="<?= $tglDari ?>">
                <input type="date" name="tgl_sampai" class="form-control mr-2" value="<?= $tglSampai ?>">
                <select name="supplier_id" class="form-control mr-2">
                    <option value="">Semua Supplier</option>
                    <?php foreach ($supplierList as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $filterSupplier == $s['id'] ? 'selected' : '' ?>><?= esc($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>No</th><th>Nomor PO</th><th>Tgl Pesan</th><th>Tgl Terima</th><th>Supplier</th><th>Produk</th><th>Pesan</th><th>Terima</th><th>Harga</th><th>Subtotal</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($records as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($r['nomor_po']) ?></td>
                            <td><?= $r['tanggal_pesan'] ?></td>
                            <td><?= $r['tanggal_terima'] ?? '-' ?></td>
                            <td><?= esc($r['nama_supplier']) ?></td>
                            <td><?= esc($r['nama_produk']) ?></td>
                            <td class="text-right"><?= $r['jumlah_pesan'] ?></td>
                            <td class="text-right"><?= $r['jumlah_terima'] ?? 0 ?></td>
                            <td class="text-right"><?= number_format($r['harga_beli'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($r['subtotal'], 0, ',', '.') ?></td>
                            <td><span class="badge bg-<?= $r['status'] === 'diterima' ? 'success' : ($r['status'] === 'dibatalkan' ? 'danger' : 'warning') ?>"><?= ucfirst($r['status']) ?></span></td>
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
