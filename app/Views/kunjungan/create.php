<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Kunjungan</h1>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

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

                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="penjualanTable">
                        <thead>
                            <tr>
                                <th style="width:4%">No</th>
                                <th style="width:22%">Produk</th>
                                <th style="width:10%">Qty Terjual</th>
                                <th style="width:10%">Stok Toko</th>
                                <th style="width:14%">Harga Satuan</th>
                                <?php if (session()->get('role') !== 'sales'): ?>
                                <th style="width:12%">Fee</th>
                                <th style="width:12%">HPP</th>
                                <?php endif; ?>
                                <th style="width:2%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="penjualan-row">
                                <td class="text-center row-number">1</td>
                                <td>
                                    <select name="penjualan_items[0][produk_id]" class="form-control produk-select" data-section="penjualan">
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
                                </td>
                                <td>
                                    <input type="number" name="penjualan_items[0][jumlah_terjual]" class="form-control qty" min="1" required>
                                </td>
                                <td class="stok-display text-center">0</td>
                                <td class="harga-display text-right">0</td>
                                <?php if (session()->get('role') !== 'sales'): ?>
                                <td class="fee-display text-right">0</td>
                                <td class="hpp-display text-right">0</td>
                                <?php endif; ?>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-success mb-4" id="addPenjualanRow">
                    <i class="fas fa-plus"></i> Tambah Item Penjualan
                </button>

                <hr>
                <h5 class="mb-3">
                    Retur
                    <small class="text-muted">(isi jika ada retur)</small>
                </h5>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="returTable">
                        <thead>
                            <tr>
                                <th style="width:4%">No</th>
                                <th style="width:20%">Produk</th>
                                <th style="width:10%">Qty Retur</th>
                                <th style="width:12%">Kondisi</th>
                                <th style="width:12%">Tgl Expired</th>
                                <th style="width:10%">Stok Toko</th>
                                <th style="width:20%">Keterangan</th>
                                <th style="width:2%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="retur-row">
                                <td class="text-center row-number">1</td>
                                <td>
                                    <select name="retur_items[0][produk_id]" class="form-control produk-select" data-section="retur">
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($produkList as $p): ?>
                                        <option value="<?= $p['id'] ?>" data-nama="<?= esc($p['nama']) ?>">
                                            <?= esc($p['kode_produk']) ?> - <?= esc($p['nama']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="retur_items[0][jumlah_retur]" class="form-control qty" min="1" required>
                                </td>
                                <td>
                                    <select name="retur_items[0][kondisi]" class="form-control">
                                        <option value="baik">Baik</option>
                                        <option value="rusak">Rusak</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" name="retur_items[0][tgl_expired]" class="form-control">
                                </td>
                                <td class="stok-display text-center">0</td>
                                <td>
                                    <input type="text" name="retur_items[0][keterangan]" class="form-control" placeholder="Keterangan (opsional)">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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

function updateRemoveButtons(tableId) {
    const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
    rows.forEach((row, i) => {
        const btn = row.querySelector('.remove-row');
        if (btn) btn.disabled = rows.length === 1;
    });
}

function addPenjualanRow() {
    const tbody = document.querySelector('#penjualanTable tbody');
    const idx = getNextIndex('penjualan');
    const row = document.createElement('tr');
    row.className = 'penjualan-row';
    <?php $role = session()->get('role'); ?>
    row.innerHTML = `
        <td class="text-center row-number">${idx + 1}</td>
        <td>
            <select name="penjualan_items[${idx}][produk_id]" class="form-control produk-select" data-section="penjualan">
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
        </td>
        <td><input type="number" name="penjualan_items[${idx}][jumlah_terjual]" class="form-control qty" min="1" required></td>
        <td class="stok-display text-center">0</td>
        <td class="harga-display text-right">0</td>
        <?php if ($role !== 'sales'): ?>
        <td class="fee-display text-right">0</td>
        <td class="hpp-display text-right">0</td>
        <?php endif; ?>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    updateRemoveButtons('penjualanTable');
    attachPenjualanEvents(row);
}

function addReturRow() {
    const tbody = document.querySelector('#returTable tbody');
    const idx = getNextIndex('retur');
    const row = document.createElement('tr');
    row.className = 'retur-row';
    row.innerHTML = `
        <td class="text-center row-number">${idx + 1}</td>
        <td>
            <select name="retur_items[${idx}][produk_id]" class="form-control produk-select" data-section="retur">
                <option value="">-- Pilih --</option>
                <?php foreach ($produkList as $p): ?>
                <option value="<?= $p['id'] ?>" data-nama="<?= esc($p['nama']) ?>">
                    <?= esc($p['kode_produk']) ?> - <?= esc($p['nama']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="number" name="retur_items[${idx}][jumlah_retur]" class="form-control qty" min="1" required></td>
        <td>
            <select name="retur_items[${idx}][kondisi]" class="form-control">
                <option value="baik">Baik</option>
                <option value="rusak">Rusak</option>
                <option value="expired">Expired</option>
            </select>
        </td>
        <td><input type="date" name="retur_items[${idx}][tgl_expired]" class="form-control"></td>
        <td class="stok-display text-center">0</td>
        <td><input type="text" name="retur_items[${idx}][keterangan]" class="form-control" placeholder="Keterangan (opsional)"></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    updateRemoveButtons('returTable');
    attachReturEvents(row);
}

document.querySelectorAll('#penjualanTable .penjualan-row').forEach(row => {
    attachPenjualanEvents(row);
    row.querySelector('.row-number').textContent = '1';
});
document.querySelectorAll('#returTable .retur-row').forEach(row => {
    attachReturEvents(row);
    row.querySelector('.row-number').textContent = '1';
});
updateRemoveButtons('penjualanTable');
updateRemoveButtons('returTable');

document.getElementById('addPenjualanRow').addEventListener('click', addPenjualanRow);
document.getElementById('addReturRow').addEventListener('click', addReturRow);

document.getElementById('toko_id').addEventListener('change', function() {
    stokCache = {};
    hargaCache = {};
    document.querySelectorAll('#penjualanTable .penjualan-row').forEach(row => {
        const produkSelect = row.querySelector('.produk-select');
        if (produkSelect.value) {
            produkSelect.dispatchEvent(new Event('change'));
        }
    });
    document.querySelectorAll('#returTable .retur-row').forEach(row => {
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
            const row = btn.closest('tr');
            const table = row.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 1) {
                row.remove();
                const rows2 = tbody.querySelectorAll('tr');
                rows2.forEach((r, i) => {
                    const num = r.querySelector('.row-number');
                    if (num) num.textContent = i + 1;
                });
                if (table.id === 'penjualanTable') updateRemoveButtons('penjualanTable');
                if (table.id === 'returTable') updateRemoveButtons('returTable');
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
