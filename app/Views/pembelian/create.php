<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Purchase Order</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?= base_url('/pembelian/store') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="supplier_id">Supplier</label>
                            <select class="form-control" id="supplier_id" name="supplier_id" required>
                                <option value="">-- Pilih Supplier --</option>
                                <?php foreach ($suppliers as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= old('supplier_id') == $s['id'] ? 'selected' : '' ?>>
                                    <?= esc($s['nama']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_pesan">Tanggal PO</label>
                            <input type="date" class="form-control" id="tanggal_pesan" name="tanggal_pesan" value="<?= old('tanggal_pesan', date('Y-m-d')) ?>" required>
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
                                <th style="width:5%">No</th>
                                <th style="width:35%">Produk</th>
                                <th style="width:15%">Qty</th>
                                <th style="width:20%">Harga Beli</th>
                                <th style="width:20%">Subtotal</th>
                                <th style="width:5%"></th>
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
                                    <input type="number" name="items[0][jumlah_pesan]" class="form-control qty" min="1" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][harga_beli]" class="form-control harga-beli" min="0" step="0.01" required>
                                </td>
                                <td>
                                    <span class="subtotal-display">0</span>
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

                <div class="row mb-3">
                    <div class="col-md-3 offset-md-9">
                        <div class="form-group">
                            <label><strong>Total Nilai</strong></label>
                            <div class="form-control-plaintext h5" id="totalDisplay">Rp 0</div>
                            <input type="hidden" name="total_nilai" id="totalHidden" value="0">
                        </div>
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-primary">Simpan PO</button>
                <a href="<?= base_url('/pembelian') ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
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
        <td><input type="number" name="items[${rowIndex}][jumlah_pesan]" class="form-control qty" min="1" required></td>
        <td><input type="number" name="items[${rowIndex}][harga_beli]" class="form-control harga-beli" min="0" step="0.01" required></td>
        <td><span class="subtotal-display">0</span></td>
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
    row.querySelectorAll('.qty, .harga-beli').forEach(el => {
        el.addEventListener('input', calculateRow);
    });
}

function calculateRow() {
    const rows = document.querySelectorAll('.item-row');
    let total = 0;
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
        const harga = parseFloat(row.querySelector('.harga-beli')?.value) || 0;
        const subtotal = qty * harga;
        row.querySelector('.subtotal-display').textContent = subtotal.toLocaleString('id-ID');
        total += subtotal;
    });
    document.getElementById('totalDisplay').innerHTML = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('totalHidden').value = total;
}

document.querySelectorAll('.item-row').forEach(row => {
    attachRowEvents(row);
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        const btn = e.target.closest('.remove-row');
        if (!btn.disabled) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                btn.closest('.item-row').remove();
                renumberRows();
                updateRemoveButtons();
                calculateRow();
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
