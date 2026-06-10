<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Penitipan</h1>
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
            <form action="<?= base_url('/penitipan/store') ?>" method="POST">
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

                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="itemTable">
                        <thead>
                            <tr>
                                <th style="width:4%">No</th>
                                <th style="width:22%">Produk</th>
                                <th style="width:18%">Tier Harga</th>
                                <th style="width:10%">Qty</th>
                                <th style="width:12%">Tgl Expired</th>
                                <th style="width:12%">Stok Sales</th>
                                <th style="width:10%">Harga</th>
                                <?php if (session()->get('role') !== 'sales'): ?>
                                <th style="width:10%">Fee</th>
                                <?php endif; ?>
                                <th style="width:2%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item-row">
                                <td class="text-center row-number">1</td>
                                <td>
                                    <select name="items[0][produk_id]" class="form-control produk-select" required>
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
                                </td>
                                <td>
                                    <select name="items[0][harga_jual_id]" class="form-control tier-select" required disabled>
                                        <option value="">-- Pilih Produk dulu --</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][jumlah_titip]" class="form-control qty" min="1" required>
                                </td>
                                <td>
                                    <input type="date" name="items[0][tgl_expired]" class="form-control tgl-expired" readonly>
                                </td>
                                <td class="stok-sales-display text-center">0</td>
                                <td class="harga-display text-right">0</td>
                                <?php if (session()->get('role') !== 'sales'): ?>
                                <td class="fee-display text-right">0</td>
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
    const tbody = document.querySelector('#itemTable tbody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td class="text-center row-number">${document.querySelectorAll('.item-row').length + 1}</td>
        <td>
            <select name="items[${document.querySelectorAll('.item-row').length}][produk_id]" class="form-control produk-select" required>
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
        </td>
        <td>
            <select name="items[${document.querySelectorAll('.item-row').length}][harga_jual_id]" class="form-control tier-select" required disabled>
                <option value="">-- Pilih Produk dulu --</option>
            </select>
        </td>
        <td><input type="number" name="items[${document.querySelectorAll('.item-row').length}][jumlah_titip]" class="form-control qty" min="1" required></td>
        <td><input type="date" name="items[${document.querySelectorAll('.item-row').length}][tgl_expired]" class="form-control tgl-expired" readonly></td>
        <td class="stok-sales-display text-center">0</td>
        <td class="harga-display text-right">0</td>
        <?php if (session()->get('role') !== 'sales'): ?>
        <td class="fee-display text-right">0</td>
        <?php endif; ?>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    renumberRows();
    updateRemoveButtons();
    attachRowEvents(row);
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
