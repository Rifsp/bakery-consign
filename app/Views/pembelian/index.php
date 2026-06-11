<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Pembelian</h1>
        <a href="<?= base_url('/pembelian/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Buat PO Baru
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row g-2 align-items-center">
                <select id="statusFilter" class="form-control mr-2">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="sebagian" <?= ($status ?? '') === 'sebagian' ? 'selected' : '' ?>>Sebagian</option>
                    <option value="diterima" <?= ($status ?? '') === 'diterima' ? 'selected' : '' ?>>Diterima</option>
                    <option value="dibatalkan" <?= ($status ?? '') === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
                <input type="date" id="tglDari" class="form-control mr-2" value="<?= $tglDari ?? date('Y-m-01') ?>">
                <input type="date" id="tglSampai" class="form-control mr-2" value="<?= $tglSampai ?? date('Y-m-t') ?>">
                <a href="<?= base_url('/pembelian') ?>" class="btn btn-outline-secondary"><i class="fas fa-undo"></i></a>
            </div>
        </div>
        <div class="card-body" id="tableWrap">
            <div class="table-responsive">
                <table class="table table-bordered table-datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor PO</th>
                            <th>Supplier</th>
                            <th>Tgl Pesan</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <?= view('pembelian/_table_data', ['records' => $records]) ?>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
const statusFilter = document.getElementById('statusFilter');
const tglDari = document.getElementById('tglDari');
const tglSampai = document.getElementById('tglSampai');

function getParams(page) {
    const params = new URLSearchParams();
    if (statusFilter.value) params.set('status', statusFilter.value);
    params.set('tgl_dari', tglDari.value);
    params.set('tgl_sampai', tglSampai.value);
    if (page) params.set('page', page);
    return params;
}

async function loadData(page) {
    const params = getParams(page);
    const url = '<?= base_url('/pembelian/fetch') ?>?' + params.toString();
    const resp = await fetch(url);
    const data = await resp.json();

    const oldTbody = document.getElementById('tableBody');
    if (oldTbody) oldTbody.outerHTML = data.table;

    history.replaceState(null, '', '<?= base_url('/pembelian') ?>?' + params.toString());
    reinitDataTable();
}

function reinitDataTable() {
    var table = $('.table-datatable');
    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().destroy();
    }
    table.DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
        order: [],
        columnDefs: [{ orderable: false, targets: -1 }]
    });
}

let debounceTimer;
function onFilterChange() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadData(), 300);
}

statusFilter.addEventListener('change', onFilterChange);
tglDari.addEventListener('change', onFilterChange);
tglSampai.addEventListener('change', onFilterChange);
</script>
<?= $this->endSection() ?>
