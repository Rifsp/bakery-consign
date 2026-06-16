<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Kunjungan</h1>
    </div>

    <style>
    .penjualan-row, .retur-row {
        background: #fff;
        padding: .5rem .75rem;
        margin-bottom: .5rem;
        border: 1px solid #dee2e6;
        border-radius: .25rem;
    }
    @media (min-width: 768px) {
        .penjualan-row, .retur-row {
            padding: .35rem 0;
            margin-bottom: 0;
            border: none;
            border-bottom: 1px solid #f0f0f0;
            border-radius: 0;
        }
        .penjualan-row:last-child, .retur-row:last-child {
            border-bottom: none;
        }
    }
    </style>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?= base_url('/kunjungan/store') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="toko_id">Toko Tujuan</label>
                            <select class="form-control" id="toko_id" name="toko_id" required>
                                <option value="">-- Pilih Toko --</option>
                                <?php foreach ($tokoList as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= old('toko_id') == $t['id'] ? 'selected' : '' ?>>
                                    <?= esc($t['nama']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal">Tanggal Kunjungan</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= old('tanggal', date('Y-m-d')) ?>" <?= session()->get('role') === 'sales' ? 'readonly' : '' ?> required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="catatan">Catatan Kunjungan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Catatan (opsional)"><?= old('catatan') ?></textarea>
                        </div>
                    </div>
                </div>

                <hr>
                <h5 class="mb-3">
                    Penjualan
                    <small class="text-muted">(isi jika ada penjualan)</small>
                </h5>

                <div id="penjualanContainer">
                    <div class="penjualan-row">
                        <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                            <span class="fw-bold">Item <span class="row-number">1</span></span>
                            <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-4">
                                <label class="form-label small mb-0 d-md-none">Produk</label>
                                <select name="penjualan_items[0][produk_id]" class="form-control form-control-sm produk-select" data-section="penjualan">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($produkList as $p): ?>
                                    <option value="<?= $p['id'] ?>" data-nama="<?= esc($p['nama']) ?>">
                                        <?= esc($p['kode_produk']) ?> - <?= esc($p['nama']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="penjualan_items[0][harga_satuan]" class="harga-satuan-input" value="0">
                                <input type="hidden" name="penjualan_items[0][fee_satuan]" class="fee-satuan-input" value="0">
                                <input type="hidden" name="penjualan_items[0][hpp_satuan]" class="hpp-satuan-input" value="0">
                                <input type="hidden" name="penjualan_items[0][harga_jual_id]" class="harga-jual-id-input" value="0">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small mb-0 d-md-none">Qty Terjual</label>
                                <input type="number" name="penjualan_items[0][jumlah_terjual]" class="form-control form-control-sm qty" min="1" step="1">
                            </div>
                            <div class="col-6 col-md-2 text-center align-self-center">
                                <small class="d-block d-md-none text-muted">Stok Toko</small>
                                <span class="stok-display fw-bold">0</span>
                            </div>
                            <div class="col-6 col-md-2 text-center align-self-center">
                                <small class="d-block d-md-none text-muted">Harga</small>
                                <span class="harga-display fw-bold">0</span>
                            </div>
                            <?php if (session()->get('role') !== 'sales'): ?>
                            <div class="col-3 col-md-1 text-center align-self-center">
                                <small class="d-block d-md-none text-muted">Fee</small>
                                <span class="fee-display fw-bold">0</span>
                            </div>
                            <div class="col-3 col-md-1 text-center align-self-center">
                                <small class="d-block d-md-none text-muted">HPP</small>
                                <span class="hpp-display fw-bold">0</span>
                            </div>
                            <?php endif; ?>
                            <div class="col-12 col-md-1 text-center align-self-center d-none d-md-block">
                                <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-success mb-4" id="addPenjualanRow">
                    <i class="fas fa-plus"></i> Tambah Item Penjualan
                </button>

                <hr>
                <h5 class="mb-3">
                    Retur
                    <small class="text-muted">(isi jika ada retur)</small>
                </h5>

                <div id="returContainer">
                    <div class="retur-row">
                        <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                            <span class="fw-bold">Item <span class="row-number">1</span></span>
                            <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-4">
                                <label class="form-label small mb-0 d-md-none">Produk</label>
                                <select name="retur_items[0][produk_id]" class="form-control form-control-sm produk-select" data-section="retur">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($produkList as $p): ?>
                                    <option value="<?= $p['id'] ?>" data-nama="<?= esc($p['nama']) ?>">
                                        <?= esc($p['kode_produk']) ?> - <?= esc($p['nama']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small mb-0 d-md-none">Qty Retur</label>
                                <input type="number" name="retur_items[0][jumlah_retur]" class="form-control form-control-sm qty" min="1" step="1">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small mb-0 d-md-none">Kondisi</label>
                                <select name="retur_items[0][kondisi]" class="form-control form-control-sm">
                                    <option value="baik">Baik</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small mb-0 d-md-none">Tgl Expired</label>
                                <input type="date" name="retur_items[0][tgl_expired]" class="form-control form-control-sm">
                            </div>
                            <div class="col-6 col-md-1 text-center align-self-center">
                                <small class="d-block d-md-none text-muted">Stok</small>
                                <span class="stok-display fw-bold">0</span>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-0 d-md-none">Keterangan</label>
                                <input type="text" name="retur_items[0][keterangan]" class="form-control form-control-sm" placeholder="Ket (opsional)">
                            </div>
                            <div class="col-12 col-md-1 text-center align-self-center d-none d-md-block">
                                <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-success mb-4" id="addReturRow">
                    <i class="fas fa-plus"></i> Tambah Item Retur
                </button>

                <hr>
                <button type="submit" class="btn btn-primary">Simpan Kunjungan</button>
                <a href="<?= base_url('/kunjungan') ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
let stokCache = {};
let hargaCache = {};

function getNextIndex(section) {
    return document.querySelectorAll('.' + section + '-row').length;
}

function getTokoId() {
    return document.getElementById('toko_id').value;
}

function attachPenjualanEvents(row) {
    const produkSelect = row.querySelector('.produk-select');
    produkSelect.addEventListener('change', function() {
        const produkId = parseInt(this.value);
        const tokoId = getTokoId();

        row.querySelector('.stok-display').textContent = '0';
        row.querySelector('.harga-display').textContent = '0';
        <?php if (session()->get('role') !== 'sales'): ?>
        row.querySelector('.fee-display').textContent = '0';
        row.querySelector('.hpp-display').textContent = '0';
        <?php endif; ?>
        row.querySelector('.harga-satuan-input').value = '0';
        row.querySelector('.fee-satuan-input').value = '0';
        row.querySelector('.hpp-satuan-input').value = '0';
        row.querySelector('.harga-jual-id-input').value = '0';

        if (produkId && tokoId) {
            const key = tokoId + '_' + produkId;
            if (hargaCache[key]) {
                displayPenjualanHarga(row, hargaCache[key]);
            } else {
                fetch('<?= base_url('/kunjungan/get-stok-toko') ?>?toko_id=' + tokoId + '&produk_id=' + produkId)
                    .then(r => r.json())
                    .then(d => {
                        hargaCache[key] = d;
                        stokCache[key] = d.stok;
                        displayPenjualanHarga(row, d);
                    });
            }
        }
    });
}

function displayPenjualanHarga(row, data) {
    row.querySelector('.stok-display').textContent = data.stok;
    const qtyInput = row.querySelector('.qty');
    if (qtyInput) qtyInput.max = data.stok;

    if (data.harga && data.harga.harga_satuan > 0) {
        row.querySelector('.harga-display').textContent = parseInt(data.harga.harga_satuan).toLocaleString('id-ID');
        <?php if (session()->get('role') !== 'sales'): ?>
        row.querySelector('.fee-display').textContent = parseInt(data.harga.fee_satuan).toLocaleString('id-ID');
        row.querySelector('.hpp-display').textContent = parseInt(data.harga.hpp).toLocaleString('id-ID');
        <?php endif; ?>
        row.querySelector('.harga-satuan-input').value = data.harga.harga_satuan;
        row.querySelector('.fee-satuan-input').value = data.harga.fee_satuan;
        row.querySelector('.hpp-satuan-input').value = data.harga.hpp;
        row.querySelector('.harga-jual-id-input').value = data.harga.harga_jual_id;
    } else {
        row.querySelector('.harga-display').textContent = 'Tidak ada harga';
    }
}

function attachReturEvents(row) {
    const produkSelect = row.querySelector('.produk-select');
    produkSelect.addEventListener('change', function() {
        const produkId = parseInt(this.value);
        const tokoId = getTokoId();

        row.querySelector('.stok-display').textContent = '0';

        if (produkId && tokoId) {
            const key = tokoId + '_' + produkId;
            if (stokCache[key] !== undefined) {
                row.querySelector('.stok-display').textContent = stokCache[key];
            } else {
                fetch('<?= base_url('/kunjungan/get-stok-toko') ?>?toko_id=' + tokoId + '&produk_id=' + produkId)
                    .then(r => r.json())
                    .then(d => {
                        stokCache[key] = d.stok;
                        row.querySelector('.stok-display').textContent = d.stok;
                        const qtyInput = row.querySelector('.qty');
                        if (qtyInput) qtyInput.max = d.stok;
                    });
            }
        }
    });
}

function updateRemoveButtons(containerId) {
    const rows = document.querySelectorAll('#' + containerId + ' .penjualan-row, #' + containerId + ' .retur-row');
    rows.forEach((row, i) => {
        const btn = row.querySelector('.remove-row');
        if (btn) btn.disabled = rows.length === 1;
    });
}

function addPenjualanRow() {
    const container = document.querySelector('#penjualanContainer');
    const idx = getNextIndex('penjualan');
    <?php $role = session()->get('role'); ?>
    const div = document.createElement('div');
    div.className = 'penjualan-row';
    div.innerHTML = `
        <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
            <span class="fw-bold">Item <span class="row-number">${idx + 1}</span></span>
            <button type="button" class="btn btn-sm btn-danger remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <label class="form-label small mb-0 d-md-none">Produk</label>
                <select name="penjualan_items[${idx}][produk_id]" class="form-control form-control-sm produk-select" data-section="penjualan">
                    <option value="">-- Pilih --</option>
                    <?php foreach ($produkList as $p): ?>
                    <option value="<?= $p['id'] ?>" data-nama="<?= esc($p['nama']) ?>">
                        <?= esc($p['kode_produk']) ?> - <?= esc($p['nama']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="penjualan_items[${idx}][harga_satuan]" class="harga-satuan-input" value="0">
                <input type="hidden" name="penjualan_items[${idx}][fee_satuan]" class="fee-satuan-input" value="0">
                <input type="hidden" name="penjualan_items[${idx}][hpp_satuan]" class="hpp-satuan-input" value="0">
                <input type="hidden" name="penjualan_items[${idx}][harga_jual_id]" class="harga-jual-id-input" value="0">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0 d-md-none">Qty Terjual</label>
                <input type="number" name="penjualan_items[${idx}][jumlah_terjual]" class="form-control form-control-sm qty" min="1" step="1">
            </div>
            <div class="col-6 col-md-2 text-center align-self-center">
                <small class="d-block d-md-none text-muted">Stok Toko</small>
                <span class="stok-display fw-bold">0</span>
            </div>
            <div class="col-6 col-md-2 text-center align-self-center">
                <small class="d-block d-md-none text-muted">Harga</small>
                <span class="harga-display fw-bold">0</span>
            </div>
            <?php if ($role !== 'sales'): ?>
            <div class="col-3 col-md-1 text-center align-self-center">
                <small class="d-block d-md-none text-muted">Fee</small>
                <span class="fee-display fw-bold">0</span>
            </div>
            <div class="col-3 col-md-1 text-center align-self-center">
                <small class="d-block d-md-none text-muted">HPP</small>
                <span class="hpp-display fw-bold">0</span>
            </div>
            <?php endif; ?>
            <div class="col-12 col-md-1 text-center align-self-center d-none d-md-block">
                <button type="button" class="btn btn-sm btn-danger remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(div);
    reinitSelect2(div);
    updateRemoveButtons('penjualanContainer');
    attachPenjualanEvents(div);
}

function addReturRow() {
    const container = document.querySelector('#returContainer');
    const idx = getNextIndex('retur');
    const div = document.createElement('div');
    div.className = 'retur-row';
    div.innerHTML = `
        <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
            <span class="fw-bold">Item <span class="row-number">${idx + 1}</span></span>
            <button type="button" class="btn btn-sm btn-danger remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <label class="form-label small mb-0 d-md-none">Produk</label>
                <select name="retur_items[${idx}][produk_id]" class="form-control form-control-sm produk-select" data-section="retur">
                    <option value="">-- Pilih --</option>
                    <?php foreach ($produkList as $p): ?>
                    <option value="<?= $p['id'] ?>" data-nama="<?= esc($p['nama']) ?>">
                        <?= esc($p['kode_produk']) ?> - <?= esc($p['nama']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0 d-md-none">Qty Retur</label>
                <input type="number" name="retur_items[${idx}][jumlah_retur]" class="form-control form-control-sm qty" min="1" step="1">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0 d-md-none">Kondisi</label>
                <select name="retur_items[${idx}][kondisi]" class="form-control form-control-sm">
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0 d-md-none">Tgl Expired</label>
                <input type="date" name="retur_items[${idx}][tgl_expired]" class="form-control form-control-sm">
            </div>
            <div class="col-6 col-md-1 text-center align-self-center">
                <small class="d-block d-md-none text-muted">Stok</small>
                <span class="stok-display fw-bold">0</span>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small mb-0 d-md-none">Keterangan</label>
                <input type="text" name="retur_items[${idx}][keterangan]" class="form-control form-control-sm" placeholder="Ket (opsional)">
            </div>
            <div class="col-12 col-md-1 text-center align-self-center d-none d-md-block">
                <button type="button" class="btn btn-sm btn-danger remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(div);
    reinitSelect2(div);
    updateRemoveButtons('returContainer');
    attachReturEvents(div);
}

document.querySelectorAll('#penjualanContainer .penjualan-row').forEach(row => {
    attachPenjualanEvents(row);
    row.querySelector('.row-number').textContent = '1';
});
document.querySelectorAll('#returContainer .retur-row').forEach(row => {
    attachReturEvents(row);
    row.querySelector('.row-number').textContent = '1';
});
updateRemoveButtons('penjualanContainer');
updateRemoveButtons('returContainer');

document.getElementById('addPenjualanRow').addEventListener('click', addPenjualanRow);
document.getElementById('addReturRow').addEventListener('click', addReturRow);

document.getElementById('toko_id').addEventListener('change', function() {
    stokCache = {};
    hargaCache = {};
    document.querySelectorAll('#penjualanContainer .penjualan-row').forEach(row => {
        const produkSelect = row.querySelector('.produk-select');
        if (produkSelect.value) {
            produkSelect.dispatchEvent(new Event('change'));
        }
    });
    document.querySelectorAll('#returContainer .retur-row').forEach(row => {
        const produkSelect = row.querySelector('.produk-select');
        if (produkSelect.value) {
            produkSelect.dispatchEvent(new Event('change'));
        }
    });
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        const btn = e.target.closest('.remove-row');
        if (!btn.disabled) {
            const row = btn.closest('.penjualan-row, .retur-row');
            const container = row.parentNode;
            const rows = container.querySelectorAll('.penjualan-row, .retur-row');
            if (rows.length > 1) {
                row.remove();
                const rows2 = container.querySelectorAll('.penjualan-row, .retur-row');
                rows2.forEach((r, i) => {
                    const num = r.querySelector('.row-number');
                    if (num) num.textContent = i + 1;
                });
                if (row.classList.contains('penjualan-row')) updateRemoveButtons('penjualanContainer');
                if (row.classList.contains('retur-row')) updateRemoveButtons('returContainer');
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
