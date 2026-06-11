-- =============================================================
-- SEED DUMMY DATA — toko roti bakery
-- Urutan INSERT sesuai dependency FK
-- =============================================================

BEGIN;

-- HAPUS semua data (urutannya dibalik biar ga kena FK)
DELETE FROM stok_expired_toko;
DELETE FROM stok_toko;
DELETE FROM stok_sales;
DELETE FROM stok_gudang;
DELETE FROM mutasi_sales;
DELETE FROM mutasi_gudang;
DELETE FROM penitipan_detail;
DELETE FROM penitipan;
DELETE FROM retur_detail;
DELETE FROM retur;
DELETE FROM penjualan_detail;
DELETE FROM penjualan;
DELETE FROM kunjungan;
DELETE FROM pengiriman_ke_sales_detail;
DELETE FROM pengiriman_ke_sales;
DELETE FROM pembelian_detail;
DELETE FROM pembelian;
DELETE FROM harga_jual;
DELETE FROM produk;
DELETE FROM toko;
DELETE FROM suppliers;
DELETE FROM kategori_produk;
DELETE FROM users;

-- Reset sequence
ALTER SEQUENCE users_id_seq RESTART WITH 1;
ALTER SEQUENCE kategori_produk_id_seq RESTART WITH 1;
ALTER SEQUENCE suppliers_id_seq RESTART WITH 1;
ALTER SEQUENCE produk_id_seq RESTART WITH 1;
ALTER SEQUENCE harga_jual_id_seq RESTART WITH 1;
ALTER SEQUENCE toko_id_seq RESTART WITH 1;
ALTER SEQUENCE stok_gudang_id_seq RESTART WITH 1;
ALTER SEQUENCE stok_sales_id_seq RESTART WITH 1;
ALTER SEQUENCE stok_toko_id_seq RESTART WITH 1;
ALTER SEQUENCE stok_expired_toko_id_seq RESTART WITH 1;
ALTER SEQUENCE mutasi_gudang_id_seq RESTART WITH 1;
ALTER SEQUENCE mutasi_sales_id_seq RESTART WITH 1;

-- =============================================================
-- 1. users
-- =============================================================
INSERT INTO users (nama, username, password, role, email, telepon, is_aktif) VALUES
('Administrator',    'admin',       '$2y$10$arBbwCYEAdeu/RBXn4E2VeOAd1370O.MAHbslqqKFGYrJAhn/Shf2', 'admin', 'admin@bakery.co.id', '081234567890', true),
('Sales Person',     'sales',       '$2y$10$iODOyrL3OmPKgEqG52zN0uAcLIZx0fxlDZ.xzRzWUsmrfCnLXKzFW', 'sales', 'sales@bakery.co.id', '081234567891', true),
('Anang Hermawan',   'anang',       '$2y$10$1.TDa8vZQzQqDA17qziu5.fffnZJJviFAZ0HLqF/7C6PxBXl5l2MS', 'sales', 'anang@bakery.co.id', '089619136616', true),
('Dewi Sartika',     'dewi',        '$2y$10$1.TDa8vZQzQqDA17qziu5.fffnZJJviFAZ0HLqF/7C6PxBXl5l2MS', 'sales', 'dewi@bakery.co.id', '081234567892', true);

-- =============================================================
-- 2. kategori_produk
-- =============================================================
INSERT INTO kategori_produk (nama, deskripsi) VALUES
('Roti Tawar',  'Roti dengan tekstur lembut, cocok untuk sandwich'),
('Roti Manis',  'Roti dengan rasa manis, berbagai isian'),
('Roti Sobek',  'Roti sobek lembut dengan topping'),
('Pastry',      'Kue pastry seperti croissant, puff pastry'),
('Kue Kering',  'Kue kering seperti nastar, kastengel');

-- =============================================================
-- 3. suppliers
-- =============================================================
INSERT INTO suppliers (kode_supplier, nama, alamat, telepon, email, kontak_person, is_aktif) VALUES
('SUP001', 'PT Berkah Jaya',      'Jl. Merdeka No. 10, Jakarta',     '021-5551234', 'berkah@email.com',   'Budi Santoso',  true),
('SUP002', 'CV Sumber Makmur',    'Jl. Sudirman No. 25, Bandung',    '022-6667890', 'sumber@email.com',  'Ani Wijaya',    true),
('SUP003', 'PD Tepung Sejahtera', 'Jl. Ahmad Yani No. 8, Surabaya',  '031-7776543', 'tepung@email.com',  'Citra Dewi',    true);

-- =============================================================
-- 4. produk
-- =============================================================
INSERT INTO produk (kode_produk, nama, kategori_id, satuan, hpp, shelf_life_hari, deskripsi) VALUES
('PRD001', 'Roti Tawar 50gr',    1, 'pcs', 3500.00, 3, 'Roti tawar putih 50 gram per pcs'),
('PRD002', 'Roti Tawar 100gr',   1, 'pcs', 5000.00, 3, 'Roti tawar putih 100 gram per pcs'),
('PRD003', 'Roti Manis Cokelat', 2, 'pcs', 3000.00, 3, 'Roti manis isi cokelat'),
('PRD004', 'Roti Manis Keju',    2, 'pcs', 3000.00, 3, 'Roti manis isi keju'),
('PRD005', 'Roti Manis Kacang',  2, 'pcs', 3000.00, 3, 'Roti manis isi kacang hijau'),
('PRD006', 'Roti Sobek Cokelat', 3, 'pcs', 4000.00, 3, 'Roti sobek topping cokelat'),
('PRD007', 'Roti Sobek Keju',    3, 'pcs', 4000.00, 3, 'Roti sobek topping keju'),
('PRD008', 'Croissant',          4, 'pcs', 6000.00, 2, 'Croissant mentega'),
('PRD009', 'Puff Pastry',        4, 'pcs', 5500.00, 2, 'Pastry isi daging/ayam'),
('PRD010', 'Nastar',             5, 'toples', 25000.00, 14, 'Kue nastar nanas per toples'),
('PRD011', 'Kastengel',          5, 'toples', 25000.00, 14, 'Kue kastengel keju per toples'),
('PRD012', 'Putri Salju',        5, 'toples', 23000.00, 14, 'Kue putri salju per toples');

-- =============================================================
-- 5. harga_jual
-- =============================================================
INSERT INTO harga_jual (produk_id, nama_harga, harga, fee_sales) VALUES
(1,  'Harga 1',  5000.00,  500.00),
(1,  'Harga 2',  5500.00,  550.00),
(2,  'Harga 1',  7000.00,  700.00),
(3,  'Harga 1',  5000.00,  500.00),
(4,  'Harga 1',  5000.00,  500.00),
(5,  'Harga 1',  5000.00,  500.00),
(6,  'Harga 1',  6500.00,  650.00),
(7,  'Harga 1',  6500.00,  650.00),
(8,  'Harga 1',  10000.00, 1000.00),
(9,  'Harga 1',  9000.00,  900.00),
(10, 'Harga 1',  45000.00, 4500.00),
(11, 'Harga 1',  45000.00, 4500.00),
(12, 'Harga 1',  40000.00, 4000.00);

-- =============================================================
-- 6. toko
-- =============================================================
INSERT INTO toko (kode_toko, nama, pemilik, alamat, kelurahan, kecamatan, kota, telepon, sales_id) VALUES
('TKO0001', 'Toko Berkah',        'Rudi Hartono',   'Jl. Merdeka No. 5, Bandung',        'Sukawarna', 'Sukajadi',   'Bandung', '081234567891', 2),
('TKO0002', 'Toko Makmur',        'Siti Rahmawati',  'Jl. Sudirman No. 12, Bandung',       'Cihapit',   'Bandung Kidul', 'Bandung', '081234567892', 2),
('TKO0003', 'Toko Sejahtera',     'Ahmad Fauzi',    'Jl. Diponegoro No. 8, Bandung',      'Kebon Jeruk', 'Andir',    'Bandung', '081234567893', 3),
('TKO0004', 'Toko Jaya Abadi',    'Dewi Lestari',   'Jl. Asia Afrika No. 15, Bandung',    'Braga',     'Sumur Bandung', 'Bandung', '081234567894', 3),
('TKO0005', 'Toko Harapan Indah', 'Bambang Sutejo', 'Jl. Raya Kopo No. 20, Bandung',      'Mekarwangi', 'Bojongloa Kidul', 'Bandung', '081234567895', 2);

-- =============================================================
-- 7. stok_gudang — stok awal setiap produk
-- =============================================================
INSERT INTO stok_gudang (produk_id, stok_tersedia) VALUES
(1,  2000),
(2,  500),
(3,  1000),
(4,  500),
(5,  500),
(6,  300),
(7,  300),
(8,  200),
(9,  150),
(10, 100),
(11, 100),
(12, 100);

-- =============================================================
-- 8. stok_sales — stok sales diisi 0 dulu (akan bertambah via pengiriman)
-- =============================================================
INSERT INTO stok_sales (sales_id, produk_id, stok_tersedia) VALUES
(2, 1, 0), (2, 2, 0), (2, 3, 0), (2, 4, 0), (2, 5, 0), (2, 6, 0), (2, 7, 0), (2, 8, 0), (2, 9, 0), (2, 10, 0), (2, 11, 0), (2, 12, 0),
(3, 1, 0), (3, 2, 0), (3, 3, 0), (3, 4, 0), (3, 5, 0), (3, 6, 0), (3, 7, 0), (3, 8, 0), (3, 9, 0), (3, 10, 0), (3, 11, 0), (3, 12, 0),
(4, 1, 0), (4, 2, 0), (4, 3, 0), (4, 4, 0), (4, 5, 0), (4, 6, 0), (4, 7, 0), (4, 8, 0), (4, 9, 0), (4, 10, 0), (4, 11, 0), (4, 12, 0);

-- Reset sequence untuk tabel transaksi (yg gapunya data dummy)
ALTER SEQUENCE pembelian_id_seq               RESTART WITH 1;
ALTER SEQUENCE pembelian_detail_id_seq         RESTART WITH 1;
ALTER SEQUENCE pengiriman_ke_sales_id_seq      RESTART WITH 1;
ALTER SEQUENCE pengiriman_ke_sales_detail_id_seq RESTART WITH 1;
ALTER SEQUENCE kunjungan_id_seq                RESTART WITH 1;
ALTER SEQUENCE penjualan_id_seq                RESTART WITH 1;
ALTER SEQUENCE penjualan_detail_id_seq         RESTART WITH 1;
ALTER SEQUENCE penitipan_id_seq                RESTART WITH 1;
ALTER SEQUENCE penitipan_detail_id_seq         RESTART WITH 1;
ALTER SEQUENCE retur_id_seq                    RESTART WITH 1;
ALTER SEQUENCE retur_detail_id_seq             RESTART WITH 1;

COMMIT;
