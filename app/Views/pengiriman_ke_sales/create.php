<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Pengiriman ke Sales</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?= base_url('/pengiriman/store') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sales_id">Sales Tujuan</label>
                            <select class="form-control" id="sales_id" name="sales_id" required>
                                <option value="">-- Pilih Sales --</option>
                                <?php foreach ($salesList as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= old('sales_id') == $s['id'] ? 'selected' : '' ?>>
                                    <?= esc($s['nama']) ?> (<?= esc($s['username']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_kirim">Tanggal Kirim</label>
                            <input type="date" class="form-control" id="tanggal_kirim" name="tanggal_kirim" value="<?= old('tanggal_kirim', date('Y-m-d')) ?>" required>
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
                                <th style="width:28%">Produk</th>
                                <th style="width:12%">Qty</th>
                                <th style="width:14%">Tgl Expired</th>
                                <th style="width:14%">Harga Beli</th>
                                <th style="width:14%">Stok Gudang</th>
                                <th style="width:14%">Stok Sales</th>
                                <th style="width:4%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item-row">
                                <td class="text-center row-number">1</td>
                                <td>
                                    <select name="items[0][produk_id]" class="form-control produk-select" required>
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($produkList as $p): ?>
                                        <option value="<?= $p['id'] ?>" data-nama="<?= esc($p['nama']) ?>">
                                            <?= esc($p['kode_produk']) ?> - <?= esc($p['nama']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][jumlah]" class="form-control qty" min="1" required>
                                </td>
                                <td>
                                    <input type="date" name="items[0][tgl_expired]" class="form-control" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][harga_beli]" class="form-control harga-beli" min="0" step="0.01" value="0">
                                </td>
                                <td class="stok-gudang-display text-center">0</td>
                                <td class="stok-sales-display text-center">
                                    <span class="text-muted">Pilih sales & produk</span>
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

                <button type="button" class="btn btn-sm btn-success mb-3" id="addRow">
                    <i class="fas fa-plus"></i> Tambah Item
                </button>

                <hr>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('/pengiriman') ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
const produkList = <?= json_encode($produkList) ?>;
const stokGudangAll = <?= json_encode($stokGudangAll) ?>;

const stokGudangMap = {};
stokGudangAll.forEach(s => { stokGudangMap[s.produk_id] = parseInt(s.stok_tersedia) || 0; });

let rowIndex = 1;

document.getElementById('addRow').addEventListener('click', function() {
    const tbody = document.querySelector('#itemTable tbody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td class="text-center row-number">${rowIndex + 1}</td>
        <td>
            <select name="items[${rowIndex}][produk_id]" class="form-control produk-select" required>
                <option value="">-- Pilih --</option>
                <?php foreach ($produkList as $p): ?>
                <option value="<?= $p['id'] ?>" data-nama="<?= esc($p['nama']) ?>">
                    <?= esc($p['kode_produk']) ?> - <?= esc($p['nama']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="number" name="items[${rowIndex}][jumlah]" class="form-control qty" min="1" required></td>
        <td><input type="date" name="items[${rowIndex}][tgl_expired]" class="form-control" required></td>
        <td><input type="number" name="items[${rowIndex}][harga_beli]" class="form-control harga-beli" min="0" step="0.01" value="0"></td>
        <td class="stok-gudang-display text-center">0</td>
        <td class="stok-sales-display text-center">
            <span class="text-muted">Pilih sales & produk</span>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    rowIndex++;
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
        const btn = row.querySelector('.remove-row');
        btn.disabled = rows.length === 1;
    });
}

function attachRowEvents(row) {
    row.querySelector('.produk-select').addEventListener('change', function() {
        updateStokInfo(row);
    });
}

function updateStokInfo(row) {
    const select = row.querySelector('.produk-select');
    const produkId = parseInt(select.value);
    const salesId = parseInt(document.getElementById('sales_id').value);

    const stokGd = stokGudangMap[produkId] || 0;
    row.querySelector('.stok-gudang-display').textContent = stokGd;

    const stokSalesDisplay = row.querySelector('.stok-sales-display');
    if (produkId && salesId) {
        fetch('<?= base_url('/pengiriman/get-stok-sales') ?>?sales_id=' + salesId + '&produk_id=' + produkId)
            .then(r => r.json())
            .then(d => { stokSalesDisplay.textContent = d.stok; })
            .catch(() => { stokSalesDisplay.textContent = '0'; });
    } else {
        stokSalesDisplay.innerHTML = '<span class="text-muted">Pilih sales & produk</span>';
    }
}

document.getElementById('sales_id').addEventListener('change', function() {
    document.querySelectorAll('.item-row').forEach(row => updateStokInfo(row));
});

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
