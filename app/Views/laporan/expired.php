<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Monitor Expired</h1>
        <a href="<?= base_url('/laporan/export-csv/expired') ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export CSV</a>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <form class="row g-2 align-items-center" method="GET">
                <select name="toko_id" class="form-control mr-2">
                    <option value="">Semua Toko</option>
                    <?php foreach ($tokoList as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $filterToko == $t['id'] ? 'selected' : '' ?>><?= esc($t['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-check mr-3">
                    <input type="checkbox" name="hampir_expired" value="1" id="chkHampir" <?= $hampirExpired ? 'checked' : '' ?> onchange="this.form.submit()">
                    <label for="chkHampir">Hampir expired (2 hari)</label>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-datatable">
                    <thead><tr><th>No</th><th>Toko</th><th>Produk</th><th>Jml Titip</th><th>Stok Toko</th><th>Tgl Expired</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($records as $i => $r): ?>
                        <?php $isExpired = $r['sudah_expired']; ?>
                        <tr class="<?= $isExpired ? 'table-danger' : ($r['tgl_expired'] <= date('Y-m-d', strtotime('+2 days')) ? 'table-warning' : '') ?>">
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($r['nama_toko']) ?></td>
                            <td><?= esc($r['nama_produk']) ?></td>
                            <td class="text-right"><?= $r['jumlah_titip'] ?></td>
                            <td class="text-right"><?= $r['stok_tersedia'] ?></td>
                            <td><?= $r['tgl_expired'] ?></td>
                            <td>
                                <?php if ($isExpired): ?>
                                <span class="badge bg-danger">Expired</span>
                                <?php elseif ($r['tgl_expired'] <= date('Y-m-d', strtotime('+2 days'))): ?>
                                <span class="badge bg-warning text-dark">Akan Expired</span>
                                <?php else: ?>
                                <span class="badge bg-success">Aman</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
