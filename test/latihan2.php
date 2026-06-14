<?php

// Cek apakah form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['nama'])) {
    $nama = $_POST['nama'];
    
    // Simpan cookie selama 7 hari (604800 detik)
    setcookie('pengunjung', $nama, time() + (7 * 24 * 60 * 60));
    
    // Update $_COOKIE agar pesan langsung muncul
    $_COOKIE['pengunjung'] = $nama;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cookie Pengunjung</title>
</head>
<body>
    <h2>Sistem Pengunjung dengan Cookie</h2>
    
    <?php
    // Cek apakah cookie pengunjung sudah ada
    if (isset($_COOKIE['pengunjung'])) {
        echo '<p style="color: green;"><strong>Selamat datang kembali, ' . htmlspecialchars($_COOKIE['pengunjung']) . '</strong></p>';
    } else {
        echo '<p style="color: blue;"><strong>Halo pengunjung baru</strong></p>';
    }
    ?>
    
    <h3>Masukkan Nama Anda</h3>
    <form method="POST" action="latihan2.php">
        <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama" required><br><br>
        <input type="submit" value="Simpan">
    </form>
    
    <?php
    // Tampilkan cookie yang tersimpan (untuk debugging)
    if (isset($_COOKIE['pengunjung'])) {
        echo '<p><small>Cookie yang tersimpan: ' . htmlspecialchars($_COOKIE['pengunjung']) . '</small></p>';
    }
    ?>
</body>
</html>
