<?php
require_once 'vendor/autoload.php';

$conn = pg_connect("host=localhost dbname=db_bakery user=postgres password=qwe74123 port=5432");

$result = pg_query($conn, "SELECT id, username, email, role, is_aktif FROM users WHERE username='admin'");
$row = pg_fetch_assoc($result);

echo "Data Admin:\n";
print_r($row);

$pwResult = pg_query($conn, "SELECT password FROM users WHERE username='admin'");
$hashedPw = pg_fetch_result($pwResult, 0);
echo "\nPassword hash: $hashedPw\n";
echo "Test login admin123: " . (password_verify('admin123', $hashedPw) ? 'BERHASIL' : 'GAGAL') . "\n";

echo "\n--- Reset password admin ---\n";
$newHash = password_hash('admin123', PASSWORD_DEFAULT);
pg_query($conn, "UPDATE users SET password='$newHash' WHERE username='admin'");
echo "Password admin berhasil di-reset ke: admin123\n";