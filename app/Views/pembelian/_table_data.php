<tbody id="tableBody">
    <?php foreach ($records as $i => $r): ?>
    <tr>
        <td><?= $i + 1 ?></td>
        <td><?= esc($r['nomor_po']) ?></td>
        <td><?= esc($r['supplier_nama'] ?? '-') ?></td>
        <td><?= esc($r['tanggal_pesan']) ?></td>
        <td>
            <?php if ($r['status'] === 'pending'): ?>
                <span class="badge bg-warning text-dark">Pending</span>
            <?php elseif ($r['status'] === 'sebagian'): ?>
                <span class="badge bg-info text-dark">Sebagian</span>
            <?php elseif ($r['status'] === 'diterima'): ?>
                <span class="badge bg-success">Diterima</span>
            <?php elseif ($r['status'] === 'dibatalkan'): ?>
                <span class="badge bg-secondary">Dibatalkan</span>
            <?php endif; ?>
        </td>
        <td><?= number_format($r['total_nilai'] ?? 0, 0, ',', '.') ?></td>
        <td>
            <a href="<?= base_url('/pembelian/detail/' . $r['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                <i class="fas fa-eye"></i>
            </a>
            <?php if ($r['status'] === 'pending' || $r['status'] === 'sebagian'): ?>
                <a href="<?= base_url('/pembelian/terima/' . $r['id']) ?>" class="btn btn-sm btn-success" title="Terima Barang">
                    <i class="fas fa-truck-loading"></i>
                </a>
            <?php endif; ?>
            <?php if ($r['status'] === 'pending'): ?>
                <form action="<?= base_url('/pembelian/delete/' . $r['id']) ?>" method="POST" style="display:inline" onsubmit="return confirm('Yakin batalkan PO ini?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-danger" title="Batalkan">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($records)): ?>
    <tr>
        <td colspan="99" class="text-center">Tidak ada data</td>
    </tr>
    <?php endif; ?>
</tbody>
