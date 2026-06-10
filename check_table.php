<?php

require_once 'vendor/autoload.php';

$conn = pg_connect("host=localhost dbname=db_bakery user=postgres password=qwe74123 port=5432");

$result = pg_query($conn, "SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'users' ORDER BY ordinal_position");

echo "Struktur tabel 'users':\n";
echo "=====================\n";
while ($row = pg_fetch_assoc($result)) {
    echo "{$row['column_name']} ({$row['data_type']})\n";
}