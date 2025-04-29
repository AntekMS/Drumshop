<?php
/**
 * Admin Header Template
 *
 * @package DrumShop
 */
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - DrumShop Admin' : 'DrumShop Admin' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h2 class="text-white h4">DrumShop Admin</h2>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white <?= uri_string() == 'admin' ? 'active' : '' ?>" href="<?= base_url('admin') ?>">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white <?= strpos(uri_string(), 'admin/produkte') === 0 ? 'active' : '' ?>" href="<?= base_url('admin/produkte') ?>">
                                <i class="fas fa-drum"></i> Produkte
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white <?= strpos(uri_string(), 'admin/kategorien') === 0 ? 'active' : '' ?>" href="<?= base_url('admin/kategorien') ?>">
                                <i class="fas fa-folder"></i> Kategorien
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white <?= strpos(uri_string(), 'admin/bestellungen') === 0 ? 'active' : '' ?>" href="<?= base_url('admin/bestellungen') ?>">
                                <i class="fas fa-shopping-bag"></i> Bestellungen
                            </a>
                        </li>
                    </ul>

                    <hr class="text-white">

                    <div class="px-3 mt-4">
                        <a href="<?= base_url() ?>" class="btn btn-outline-light btn-sm w-100" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Shop anzeigen
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>