<?php
require_once 'vendor/autoload.php';

$conn = pg_connect("host=localhost dbname=db_bakery user=postgres password=qwe74123 port=5432");

$result = pg_query($conn, "SELECT table_name FROM information_schema.tables WHERE table_schema='public' AND table_name='suppliers'");

if (pg_num_rows($result) > 0) {
    echo "Table suppliers already exists\n";
} else {
    echo "Creating suppliers table...\n";
    pg_query($conn, "
        CREATE TABLE suppliers (
            id SERIAL PRIMARY KEY,
            nama VARCHAR(100) NOT NULL,
            alamat TEXT,
            telepon VARCHAR(20),
            email VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Table suppliers created successfully!\n";
}