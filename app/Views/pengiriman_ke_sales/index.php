<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengiriman ke Sales</h1>
        <a href="<?= base_url('/pengiriman/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Buat Pengiriman
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="form-inline">
                <input type="text" id="searchInput" class="form-control mr-2" placeholder="Cari nomor kirim atau sales..." value="<?= $search ?? '' ?>" style="width:250px">
                <select id="salesFilter" class="form-control mr-2">
                    <option value="">Semua Sales</option>
                    <?php foreach ($salesList as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($salesId ?? '') == $s['id'] ? 'selected' : '' ?>><?= esc($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="date" id="tglDari" class="form-control mr-2" value="<?= $tglDari ?? date('Y-m-01') ?>">
                <input type="date" id="tglSampai" class="form-control mr-2" value="<?= $tglSampai ?? date('Y-m-t') ?>">
                <a href="<?= base_url('/pengiriman') ?>" class="btn btn-outline-secondary"><i class="fas fa-undo"></i></a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Kirim</th>
                            <th>Sales</th>
                            <th>Tgl Kirim</th>
                            <th>Total Item</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <?= view('pengiriman_ke_sales/_table_data', ['records' => $records]) ?>
                </table>
            </div>
            <div class="mt-3" id="paginationWrap">
                <?= $pager->links('default', 'bootstrap_pagination') ?>
            </div>
        </div>
    </div>
</div>

<script>
const searchInput = document.getElementById('searchInput');
const salesFilter = document.getElementById('salesFilter');
const tglDari = document.getElementById('tglDari');
const tglSampai = document.getElementById('tglSampai');

function getParams(page) {
    const params = new URLSearchParams();
    const searchVal = searchInput.value.trim();
    if (searchVal) params.set('search', searchVal);
    if (salesFilter.value) params.set('sales_id', salesFilter.value);
    params.set('tgl_dari', tglDari.value);
    params.set('tgl_sampai', tglSampai.value);
    if (page) params.set('page', page);
    return params;
}

async function loadData(page) {
    const params = getParams(page);
    const url = '<?= base_url('/pengiriman/fetch') ?>?' + params.toString();
    const resp = await fetch(url);
    const data = await resp.json();

    const oldTbody = document.getElementById('tableBody');
    if (oldTbody) oldTbody.outerHTML = data.table;

    const oldPag = document.getElementById('paginationWrap');
    if (oldPag) oldPag.outerHTML = data.pagination;

    attachPaginationListeners();
    history.replaceState(null, '', '<?= base_url('/pengiriman') ?>?' + params.toString());
}

function attachPaginationListeners() {
    document.querySelectorAll('#paginationWrap a[href]').forEach(a => {
        if (a.href.includes('page=')) {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                loadData(url.searchParams.get('page'));
            });
        }
    });
}

let debounceTimer;
function onFilterChange() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadData(), 300);
}

searchInput.addEventListener('input', onFilterChange);
salesFilter.addEventListener('change', onFilterChange);
tglDari.addEventListener('change', onFilterChange);
tglSampai.addEventListener('change', onFilterChange);

attachPaginationListeners();
</script>
<?= $this->endSection() ?>
