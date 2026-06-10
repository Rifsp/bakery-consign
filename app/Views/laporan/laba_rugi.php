<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Laba Rugi</h1>
        <a href="<?= base_url('/laporan/export-csv/laba_rugi') ?>?tgl_dari=<?= $tglDari ?>&tgl_sampai=<?= $tglSampai ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export CSV</a>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <form class="form-inline" method="GET">
                <input type="date" name="tgl_dari" class="form-control mr-2" value="<?= $tglDari ?>">
                <input type="date" name="tgl_sampai" class="form-control mr-2" value="<?= $tglSampai ?>">
                <select name="sales_id" class="form-control mr-2">
                    <option value="">Semua Sales</option>
                    <?php foreach ($salesList as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $filterSales == $s['id'] ? 'selected' : '' ?>><?= esc($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="produk_id" class="form-control mr-2">
                    <option value="">Semua Produk</option>
                    <?php foreach ($produkList as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $filterProduk == $p['id'] ? 'selected' : '' ?>><?= esc($p['kode_produk']) ?> - <?= esc($p['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
            </form>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3"><div class="card bg-primary text-white p-3"><h5>Total Penjualan</h5><h3>Rp <?= number_format($summary['total_penjualan'], 0, ',', '.') ?></h3></div></div>
                <div class="col-md-3"><div class="card bg-danger text-white p-3"><h5>Total HPP</h5><h3>Rp <?= number_format($summary['total_hpp'], 0, ',', '.') ?></h3></div></div>
                <div class="col-md-3"><div class="card bg-info text-white p-3"><h5>Total Fee</h5><h3>Rp <?= number_format($summary['total_fee'], 0, ',', '.') ?></h3></div></div>
                <div class="col-md-3"><div class="card bg-success text-white p-3"><h5>Laba Bersih</h5><h3>Rp <?= number_format($summary['total_laba'], 0, ',', '.') ?></h3></div></div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="dataTable">
                    <thead><tr><th>No</th><th>Tgl</th><th>Toko</th><th>Sales</th><th>Produk</th><th>Qty</th><th>Harga</th><th>Fee</th><th>HPP</th><th>Subtotal</th><th>Laba</th></tr></thead>
                    <tbody>
                        <?php foreach ($records as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $r['tanggal'] ?></td>
                            <td><?= esc($r['nama_toko']) ?></td>
                            <td><?= esc($r['nama_sales']) ?></td>
                            <td><?= esc($r['nama_produk']) ?></td>
                            <td class="text-right"><?= $r['jumlah_terjual'] ?></td>
                            <td class="text-right"><?= number_format($r['harga_satuan'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($r['fee_satuan'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($r['hpp_satuan'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($r['subtotal_harga'], 0, ',', '.') ?></td>
                            <td class="text-right <?= $r['laba_bersih'] < 0 ? 'text-danger' : '' ?>"><?= number_format($r['laba_bersih'], 0, ',', '.') ?></td>
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
