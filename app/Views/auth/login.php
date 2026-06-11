<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>DIANTRA BAKERY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .brand-icon { font-size: 2.5rem; color: var(--bakery-gold); }
        .login-divider { border-top: 2px solid var(--bakery-gold-light); margin: 1.5rem 0; }
    </style>
</head>
<body class="login-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="login-card animate-fade-in">
                    <div class="card-header text-center">
                        <i class="fas fa-bread-slice brand-icon"></i>
                        <h3>DIANTRA BAKERY</h3>
                        <div class="subtitle">Sistem Manajemen Bakery</div>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('auth/login') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                                    <input class="form-control" id="inputUsername" name="username" type="text"
                                           placeholder="Masukkan username" required autofocus />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                    <input class="form-control" id="inputPassword" name="password" type="password"
                                           placeholder="Masukkan password" required />
                                </div>
                            </div>
                            <div class="login-divider"></div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="fas fa-sign-in-alt me-2"></i> Masuk
                            </button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3 text-muted small">
                    &copy; <?= date('Y') ?> DIANTRA BAKERY APP
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>