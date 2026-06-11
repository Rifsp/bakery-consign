<tbody id="tableBody">
    <?php foreach ($records as $i => $r): ?>
    <tr>
        <td><?= $i + 1 ?></td>
        <td><?= esc($r['nomor_kirim']) ?></td>
        <td><?= esc($r['sales_nama'] ?? '-') ?></td>
        <td><?= esc($r['tanggal_kirim']) ?></td>
        <td class="text-center"><?= $r['total_item'] ?? 0 ?></td>
        <td>
            <a href="<?= base_url('/pengiriman/detail/' . $r['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                <i class="fas fa-eye"></i>
            </a>
            <form action="<?= base_url('/pengiriman/delete/' . $r['id']) ?>" method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
