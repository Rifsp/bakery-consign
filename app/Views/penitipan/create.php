<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Penitipan</h1>
    </div>

    <style>
    .item-row {
        background: #fff;
        padding: .5rem .75rem;
        margin-bottom: .5rem;
        border: 1px solid #dee2e6;
        border-radius: .25rem;
    }
    @media (min-width: 768px) {
        .item-row {
            padding: .35rem 0;
            margin-bottom: 0;
            border: none;
            border-bottom: 1px solid #f0f0f0;
            border-radius: 0;
        }
        .item-row:last-child {
            border-bottom: none;
        }
    }
    </style>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?= base_url('/penitipan/store') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="toko_id">Toko Tujuan</label>
                            <select class="form-control select2" id="toko_id" name="toko_id" required>
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
                            <label for="tanggal_titip">Tanggal Titip</label>
                            <input type="date" class="form-control" id="tanggal_titip" name="tanggal_titip" value="<?= old('tanggal_titip', date('Y-m-d')) ?>" <?= session()->get('role') === 'sales' ? 'readonly' : '' ?> required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Catatan (opsional)"><?= old('catatan') ?></textarea>
                        </div>
                    </div>
                </div>

                <hr>
                <h5 class="mb-3">Item Produk</h5>

                <div id="itemsContainer">
                    <div class="item-row">
                        <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                            <span class="fw-bold">Item <span class="row-number">1</span></span>
                            <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-4">
                                <label class="form-label small mb-0 d-md-none">Produk</label>
                                <select name="items[0][produk_id]" class="form-control form-control-sm produk-select select2" required>
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($produkList as $p): 
                                        $stok = $stokSalesMap[$p['id']] ?? 0;
                                        if ($stok <= 0) continue;
                                    ?>
                                    <option value="<?= $p['id'] ?>" data-nama="<?= esc($p['nama']) ?>" data-stok="<?= $stok ?>">
                                        <?= esc($p['kode_produk']) ?> - <?= esc($p['nama']) ?> (stok: <?= $stok ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-0 d-md-none">Tier Harga</label>
                                <select name="items[0][harga_jual_id]" class="form-control form-control-sm tier-select select2" required disabled>
                                    <option value="">-- Pilih Produk dulu --</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small mb-0 d-md-none">Qty</label>
                                <input type="number" name="items[0][jumlah_titip]" class="form-control form-control-sm qty" min="1" required>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small mb-0 d-md-none">Tgl Expired</label>
                                <input type="date" name="items[0][tgl_expired]" class="form-control form-control-sm tgl-expired" readonly>
                            </div>
                            <div class="col-4 col-md-1 text-center align-self-center">
                                <small class="d-block d-md-none text-muted">Stok</small>
                                <span class="stok-sales-display fw-bold">0</span>
                            </div>
                            <div class="col-4 col-md-1 text-center align-self-center">
                                <small class="d-block d-md-none text-muted">Harga</small>
                                <span class="harga-display fw-bold">0</span>
                            </div>
                            <?php if (session()->get('role') !== 'sales'): ?>
                            <div class="col-4 col-md-1 text-center align-self-center">
                                <small class="d-block d-md-none text-muted">Fee</small>
                                <span class="fee-display fw-bold">0</span>
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

                <button type="button" class="btn btn-sm btn-success mb-3" id="addRow">
                    <i class="fas fa-plus"></i> Tambah Item
                </button>

                <hr>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('/penitipan') ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
const tierCache = {};

document.getElementById('addRow').addEventListener('click', function() {
    const container = document.querySelector('#itemsContainer');
    const idx = document.querySelectorAll('.item-row').length;
    <?php 
    $role = session()->get('role');
    $produkOpts = '';
    foreach ($produkList as $p): 
        $stok = $stokSalesMap[$p['id']] ?? 0;
        if ($stok <= 0) continue;
        $produkOpts .= '<option value="'.$p['id'].'" data-nama="'.esc($p['nama']).'" data-stok="'.$stok.'">'.esc($p['kode_produk']).' - '.esc($p['nama']).' (stok: '.$stok.')</option>';
    endforeach; 
    ?>
    const div = document.createElement('div');
    div.className = 'item-row';
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
                <select name="items[${idx}][produk_id]" class="form-control form-control-sm produk-select select2" required>
                    <option value="">-- Pilih --</option>
                    <?= $produkOpts ?>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small mb-0 d-md-none">Tier Harga</label>
                <select name="items[${idx}][harga_jual_id]" class="form-control form-control-sm tier-select select2" required disabled>
                    <option value="">-- Pilih Produk dulu --</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0 d-md-none">Qty</label>
                <input type="number" name="items[${idx}][jumlah_titip]" class="form-control form-control-sm qty" min="1" required>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-0 d-md-none">Tgl Expired</label>
                <input type="date" name="items[${idx}][tgl_expired]" class="form-control form-control-sm tgl-expired" readonly>
            </div>
            <div class="col-4 col-md-1 text-center align-self-center">
                <small class="d-block d-md-none text-muted">Stok</small>
                <span class="stok-sales-display fw-bold">0</span>
            </div>
            <div class="col-4 col-md-1 text-center align-self-center">
                <small class="d-block d-md-none text-muted">Harga</small>
                <span class="harga-display fw-bold">0</span>
            </div>
            <?php if ($role !== 'sales'): ?>
            <div class="col-4 col-md-1 text-center align-self-center">
                <small class="d-block d-md-none text-muted">Fee</small>
                <span class="fee-display fw-bold">0</span>
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
    renumberRows();
    updateRemoveButtons();
    attachRowEvents(div);
});

function renumberRows() {
    document.querySelectorAll('.item-row').forEach((row, i) => {
        row.querySelector('.row-number').textContent = i + 1;
    });
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.item-row');
    rows.forEach((row, i) => {
        row.querySelector('.remove-row').disabled = rows.length === 1;
    });
}

function attachRowEvents(row) {
    const produkSelect = row.querySelector('.produk-select');
    produkSelect.addEventListener('change', function() {
        const produkId = parseInt(this.value);
        const opt = this.options[this.selectedIndex];
        const stok = opt ? parseInt(opt.dataset.stok || 0) : 0;
        row.querySelector('.stok-sales-display').textContent = stok;
        row.querySelector('.qty').max = stok;

        const tierSelect = row.querySelector('.tier-select');
        tierSelect.innerHTML = '<option value="">-- Loading --</option>';
        tierSelect.disabled = true;

        if (produkId) {
            if (tierCache[produkId]) {
                populateTiers(tierSelect, tierCache[produkId], row);
            } else {
                fetch('<?= base_url('/penitipan/get-stok-sales') ?>?produk_id=' + produkId)
                    .then(r => r.json())
                    .then(d => {
                        tierCache[produkId] = d.tiers;
                        populateTiers(tierSelect, d.tiers, row);
                    });
            }
        } else {
            tierSelect.innerHTML = '<option value="">-- Pilih Produk dulu --</option>';
            tierSelect.disabled = true;
            clearPrice(row);
        }
    });

    row.querySelector('.tier-select').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.harga) {
            row.querySelector('.harga-display').textContent = parseInt(opt.dataset.harga).toLocaleString('id-ID');
            <?php if (session()->get('role') !== 'sales'): ?>
            row.querySelector('.fee-display').textContent = parseInt(opt.dataset.fee).toLocaleString('id-ID');
            <?php endif; ?>
            const tgl = document.getElementById('tanggal_titip').value;
            const shelfLife = parseInt(opt.dataset.shelf || 3);
            if (tgl) {
                const d = new Date(tgl);
                d.setDate(d.getDate() + shelfLife);
                row.querySelector('.tgl-expired').value = d.toISOString().split('T')[0];
            }
        } else {
            clearPrice(row);
        }
    });
}

function populateTiers(select, tiers, row) {
    select.innerHTML = '<option value="">-- Pilih Tier --</option>';
    if (tiers && tiers.length) {
        tiers.forEach(t => {
            const o = document.createElement('option');
            o.value = t.id;
            o.textContent = t.nama_harga + ' (Rp ' + parseInt(t.harga).toLocaleString('id-ID') + ')';
            o.dataset.harga = t.harga;
            o.dataset.fee = t.fee_sales;
            o.dataset.shelf = t.shelf_life_hari || 3;
            select.appendChild(o);
        });
        select.disabled = false;
    } else {
        select.innerHTML = '<option value="">-- Tidak ada tier harga --</option>';
        select.disabled = true;
    }
}

function clearPrice(row) {
    row.querySelector('.harga-display').textContent = '0';
    <?php if (session()->get('role') !== 'sales'): ?>
    row.querySelector('.fee-display').textContent = '0';
    <?php endif; ?>
    row.querySelector('.tgl-expired').value = '';
}

document.querySelectorAll('.item-row').forEach(row => attachRowEvents(row));
updateRemoveButtons();

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        const btn = e.target.closest('.remove-row');
        if (!btn.disabled) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                btn.closest('.item-row').remove();
                renumberRows();
                updateRemoveButtons();
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
