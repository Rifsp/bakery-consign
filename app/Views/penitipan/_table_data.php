<tbody id="tableBody">
    <?php foreach ($records as $i => $r): ?>
    <tr>
        <td><?= $i + 1 ?></td>
        <td><?= esc($r['nomor_titip']) ?></td>
        <td><?= esc($r['toko_nama'] ?? '-') ?></td>
        <td><?= esc($r['sales_nama'] ?? '-') ?></td>
        <td><?= esc($r['tanggal_titip']) ?></td>
        <td class="text-center"><?= $r['total_item'] ?? 0 ?></td>
        <td>
            <span class="badge <?= $statusLabels[$r['status']]['class'] ?? 'bg-secondary' ?>">
                <?= $statusLabels[$r['status']]['label'] ?? ucfirst($r['status']) ?>
            </span>
        </td>
        <td>
            <a href="<?= base_url('/penitipan/detail/' . $r['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                <i class="fas fa-eye"></i>
            </a>
            <?php if (session()->get('role') === 'admin' && $r['status'] === 'aktif'): ?>
                <form action="<?= base_url('/penitipan/delete/' . $r['id']) ?>" method="POST" style="display:inline" onsubmit="return confirm('Yakin tarik penitipan ini?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-warning" title="Tarik">
                        <i class="fas fa-undo-alt"></i>
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
