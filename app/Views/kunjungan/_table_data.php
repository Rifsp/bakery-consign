<tbody id="tableBody">
    <?php foreach ($records as $i => $r): ?>
    <tr>
        <td><?= $i + 1 ?></td>
        <td><?= esc($r['nomor_kunjungan']) ?></td>
        <td><?= esc($r['toko_nama'] ?? '-') ?></td>
        <td><?= esc($r['sales_nama'] ?? '-') ?></td>
        <td><?= esc($r['tanggal']) ?></td>
        <td>
            <span class="badge <?= $statusLabels[$r['status']]['class'] ?? 'bg-secondary' ?>">
                <?= $statusLabels[$r['status']]['label'] ?? ucfirst($r['status']) ?>
            </span>
        </td>
        <td>
            <a href="<?= base_url('/kunjungan/detail/' . $r['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                <i class="fas fa-eye"></i>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
