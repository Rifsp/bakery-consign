<?php
$role = session()->get('role');
$nama = session()->get('nama');
$flashSuccess = session()->getFlashdata('success');
$flashError   = session()->getFlashdata('error');
$flashWarning = session()->getFlashdata('warning');
$flashInfo    = session()->getFlashdata('info');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= ($title ?? '') ? $title . ' — DIANTRA BAKERY' : 'DIANTRA BAKERY' ?></title>
    <link href="<?= base_url('templates/sb-admin/dist/css/styles.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <div id="pageLoader" class="page-loader"><div class="bakery-spinner"></div></div>

    <nav class="sb-topnav navbar navbar-expand navbar-dark">
        <a class="navbar-brand ps-3" href="<?= base_url('dashboard') ?>">
            <i class="fas fa-bread-slice"></i> DIANTRA BAKERY
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fa-fw"></i> <?= esc($nama ?? 'User') ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt fa-fw"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Menu</div>
                        <a class="nav-link" href="<?= base_url('dashboard') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-th-large"></i></div>
                            Dashboard
                        </a>

                        <?php if ($role === 'admin'): ?>
                        <div class="sb-sidenav-menu-heading">Master</div>
                        <a class="nav-link" href="<?= base_url('produk') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                            Produk
                        </a>
                        <a class="nav-link" href="<?= base_url('supplier') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>
                            Supplier
                        </a>
                        <a class="nav-link" href="<?= base_url('toko') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-store"></i></div>
                            Toko
                        </a>
                        <?php endif; ?>

                        <div class="sb-sidenav-menu-heading">Transaksi</div>
                        <?php if ($role === 'admin'): ?>
                        <a class="nav-link" href="<?= base_url('pembelian') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-invoice"></i></div>
                            Pembelian
                        </a>
                        <a class="nav-link" href="<?= base_url('pengiriman') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-dolly"></i></div>
                            Transfer Sales
                        </a>
                        <?php endif; ?>
                        <a class="nav-link" href="<?= base_url('kunjungan') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-store-alt"></i></div>
                            Kunjungan
                        </a>
                        <a class="nav-link" href="<?= base_url('penitipan') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-people-arrows"></i></div>
                            Penitipan
                        </a>
                        <?php if ($role === 'admin'): ?>
                        <a class="nav-link" href="<?= base_url('retur') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-undo-alt"></i></div>
                            Retur
                        </a>
                        <?php endif; ?>

                        <div class="sb-sidenav-menu-heading">Laporan</div>
                        <?php if ($role === 'admin'): ?>
                        <a class="nav-link" href="<?= base_url('/laporan/laba-rugi') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                            Laba Rugi
                        </a>
                        <a class="nav-link" href="<?= base_url('/laporan/penjualan') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                            Penjualan
                        </a>
                        <a class="nav-link" href="<?= base_url('/laporan/pembelian') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                            Pembelian
                        </a>
                        <a class="nav-link" href="<?= base_url('/laporan/stok-gudang') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-warehouse"></i></div>
                            Stok Gudang
                        </a>
                        <a class="nav-link" href="<?= base_url('/laporan/stok-sales') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-boxes"></i></div>
                            Stok Sales
                        </a>
                        <a class="nav-link" href="<?= base_url('/laporan/stok-toko') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-store"></i></div>
                            Stok Toko
                        </a>
                        <a class="nav-link" href="<?= base_url('/laporan/fee-sales') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-hand-holding-usd"></i></div>
                            Fee Sales
                        </a>
                        <a class="nav-link" href="<?= base_url('/laporan/expired') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            Monitor Expired
                        </a>
                        <?php endif; ?>
                        <?php if ($role === 'sales'): ?>
                        <a class="nav-link" href="<?= base_url('/laporan/penjualan-saya') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                            Penjualan Saya
                        </a>
                        <a class="nav-link" href="<?= base_url('/laporan/stok-sales-saya') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-boxes"></i></div>
                            Stok Sales Saya
                        </a>
                        <a class="nav-link" href="<?= base_url('/laporan/stok-toko-saya') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-store"></i></div>
                            Stok Toko Saya
                        </a>
                        <?php endif; ?>

                        <div class="sb-sidenav-menu-heading">Pengaturan</div>
                        <?php if ($role === 'admin'): ?>
                        <a class="nav-link" href="<?= base_url('/user') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                            Kelola User
                        </a>
                        <?php endif; ?>
                        <a class="nav-link" href="<?= base_url('logout') ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                            Logout
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?= esc(ucfirst($role ?? '')) ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div id="flash-data"
                         data-success="<?= esc($flashSuccess ?? '') ?>"
                         data-error="<?= esc($flashError ?? '') ?>"
                         data-warning="<?= esc($flashWarning ?? '') ?>"
                         data-info="<?= esc($flashInfo ?? '') ?>"
                         style="display:none"></div>
                    <?= $this->renderSection('content') ?>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy; <?= date('Y') ?> DIANTRA BAKERY APP</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('templates/sb-admin/dist/js/scripts.js') ?>"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" crossorigin="anonymous"></script>
    <script src="<?= base_url('js/custom.js') ?>"></script>
     <script src=""></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
