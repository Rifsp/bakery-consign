<?php

require_once 'vendor/autoload.php';

$conn = pg_connect("host=localhost dbname=db_bakery user=postgres password=qwe74123 port=5432");

$result = pg_query($conn, "SELECT id, username, email, role, is_aktif FROM users");

echo "Data User di Database:\n";
echo "======================\n";
while ($row = pg_fetch_assoc($result)) {
    $status = $row['is_aktif'] ? 'Aktif' : 'Nonaktif';
    echo "ID: {$row['id']} | Username: {$row['username']} | Email: {$row['email']} | Role: {$row['role']} | Status: $status\n";
}

$checkAdmin = pg_query($conn, "SELECT * FROM users WHERE username = 'admin'");
if (pg_num_rows($checkAdmin) == 0) {
    echo "\n[User admin belum ada, akan dibuat...]\n";
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    pg_query($conn, "INSERT INTO users (username, email, password, nama, role, telepon, is_aktif) 
                     VALUES ('admin', 'admin@bakery.com', '$hash', 'Administrator', 'admin', '081234567890', true)");
    echo "User admin berhasil dibuat!\n";
}

$checkSales = pg_query($conn, "SELECT * FROM users WHERE username = 'sales'");
if (pg_num_rows($checkSales) == 0) {
    echo "\n[User sales belum ada, akan dibuat...]\n";
    $hash = password_hash('sales123', PASSWORD_DEFAULT);
    pg_query($conn, "INSERT INTO users (username, email, password, nama, role, telepon, is_aktif) 
                     VALUES ('sales', 'sales@bakery.com', '$hash', 'Sales Person', 'sales', '081234567891', true)");
    echo "User sales berhasil dibuat!\n";
}