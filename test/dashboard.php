<?php
session_start();

// Cek apakah session is_login ada
if ( $_SESSION['is_login'] !== true) {
    header('Location: login.php');
    exit();
}
?>

<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Dashboard</h2>
    <p>Selamat datang! Anda sudah login.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
