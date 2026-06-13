<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mutasi Stok</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header">
            <form class="row g-2 align-items-center" method="GET">
                <?php if (!$isSales): ?>
                <div class="col-auto">
                    <select name="jenis" class="form-control" id="filterJenis">
                        <option value="gudang" <?= $jenis === 'gudang' ? 'selected' : '' ?>>Gudang</option>
                        <option value="sales" <?= $jenis === 'sales' ? 'selected' : '' ?>>Sales</option>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-auto">
                    <input type="date" name="tgl_dari" class="form-control" value="<?= $tglDari ?>">
                </div>
                <div class="col-auto">
                    <input type="date" name="tgl_sampai" class="form-control" value="<?= $tglSampai ?>">
                </div>
                <?php if ($jenis === 'sales' && !$isSales): ?>
                <div class="col-auto">
                    <select name="sales_id" class="form-control">
                        <option value="">Semua Sales</option>
                        <?php foreach ($salesList as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $filterSales == $s['id'] ? 'selected' : '' ?>><?= esc($s['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th class="text-right">Saldo Awal</th>
                            <th class="text-right">Masuk</th>
                            <th class="text-right">Keluar</th>
                            <th class="text-right">Saldo Akhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($r['kode_produk']) ?></td>
                            <td><?= esc($r['nama_produk']) ?></td>
                            <td class="text-right"><?= number_format($r['saldo_awal'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($r['total_masuk'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($r['total_keluar'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($r['saldo_akhir'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-sm btn-detail"
                                    data-produk-id="<?= $r['produk_id'] ?>"
                                    data-nama="<?= esc($r['nama_produk']) ?>"
                                    data-saldo-awal="<?= $r['saldo_awal'] ?>">
                                    <i class="fas fa-list"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Mutasi: <span id="detailNamaBarang"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Referensi</th>
                                <th>Keterangan</th>
                                <th class="text-right">Masuk</th>
                                <th class="text-right">Keluar</th>
                                <th class="text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="detailBody"></tbody>
                        <tfoot id="detailFoot" class="fw-bold" style="display:none">
                            <tr>
                                <td colspan="4" class="text-center">TOTAL</td>
                                <td class="text-right" id="totalMasuk">0</td>
                                <td class="text-right" id="totalKeluar">0</td>
                                <td class="text-right" id="totalSaldo">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const JENIS_MASUK_GUDANG = ['masuk', 'retur_masuk'];
const JENIS_MASUK_SALES = ['masuk_dari_gudang', 'retur_dari_toko'];

function isMasuk(jenis, tipe) {
    const masuk = tipe === 'sales' ? JENIS_MASUK_SALES : JENIS_MASUK_GUDANG;
    return masuk.includes(jenis);
}

function numberFormat(n) {
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

$(document).ready(function() {
    const jenis = '<?= $jenis ?>';
    const salesId = '<?= $filterSales ?? '' ?>';

    $('#filterJenis').change(function() {
        const el = $(this);
        const v = el.val();
        const search = new URLSearchParams(window.location.search);
        search.set('jenis', v);
        if (v === 'gudang') search.delete('sales_id');
        window.location.search = search.toString();
    });

    $('.btn-detail').click(function() {
        const btn = $(this);
        const produkId = btn.data('produk-id');
        const nama = btn.data('nama');
        const saldoAwal = parseInt(btn.data('saldo-awal')) || 0;
        const tglDari = '<?= $tglDari ?>';
        const tglSampai = '<?= $tglSampai ?>';

        $('#detailNamaBarang').text(nama);
        $('#detailBody').html('<tr><td colspan="7" class="text-center">Memuat...</td></tr>');
        $('#detailFoot').hide();
        $('#modalDetail').modal('show');

        const params = {
            jenis: jenis,
            produk_id: produkId,
            tgl_dari: tglDari,
            tgl_sampai: tglSampai,
        };
        if (salesId) params.sales_id = salesId;

        $.get('<?= base_url('/laporan/mutasi-detail-json') ?>', params, function(data) {
            if (data.length === 0) {
                $('#detailBody').html('<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>');
                return;
            }

            let html = '';
            let saldo = saldoAwal;
            let sumMasuk = 0;
            let sumKeluar = 0;

            $.each(data, function(i, r) {
                const masuk = isMasuk(r.jenis, jenis) ? parseInt(r.jumlah) : 0;
                const keluar = isMasuk(r.jenis, jenis) ? 0 : parseInt(r.jumlah);
                saldo = saldo + masuk - keluar;
                sumMasuk += masuk;
                sumKeluar += keluar;

                html += '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + r.created_at + '</td>' +
                    '<td>' + (r.referensi_tabel ? r.referensi_tabel + ' #' + r.referensi_id : '-') + '</td>' +
                    '<td>' + (r.keterangan || '') + '</td>' +
                    '<td class="text-right">' + (masuk ? numberFormat(masuk) : '') + '</td>' +
                    '<td class="text-right">' + (keluar ? numberFormat(keluar) : '') + '</td>' +
                    '<td class="text-right">' + numberFormat(saldo) + '</td>' +
                    '</tr>';
            });

            $('#detailBody').html(html);
            $('#totalMasuk').text(numberFormat(sumMasuk));
            $('#totalKeluar').text(numberFormat(sumKeluar));
            $('#totalSaldo').text(numberFormat(saldo));
            $('#detailFoot').show();
        });
    });
});
</script>
<?= $this->endSection() ?>
