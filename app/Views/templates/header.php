<?php
/**
 * Header Template
 *
 * @package DrumShop
 */
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - DrumShop' : 'DrumShop - Ihr Spezialist für Schlagzeug' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/paypal.css') ?>">
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h1 class="h2"><a href="<?= base_url() ?>" class="text-white text-decoration-none">DrumShop</a></h1>
                </div>
                <div class="col-md-6">
                    <nav class="navbar navbar-expand navbar-dark">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url() ?>">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('produkte') ?>">Produkte</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    Kategorien
                                </a>
                                <ul class="dropdown-menu">
                                    <?php
                                    $kategorieModel = new \App\Models\KategorieModel();
                                    $kategorien = $kategorieModel->getHauptkategorien();
                                    foreach ($kategorien as $kat) : ?>
                                        <li><a class="dropdown-item" href="<?= base_url('produkte/kategorie/' . $kat['id']) ?>"><?= $kat['name'] ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="col-md-3 text-end">
                    <?php
                    $session = session();
                    $warenkorbModel = new \App\Models\WarenkorbModel();
                    $warenkorb = $warenkorbModel->getWarenkorbBySession($session->get('session_id'));
                    $anzahlArtikel = 0;

                    if ($warenkorb) {
                        $positionen = $warenkorbModel->getWarenkorbPositionen($warenkorb['id']);
                        foreach ($positionen as $position) {
                            $anzahlArtikel += $position['menge'];
                        }
                    }
                    ?>
                    <a href="<?= base_url('warenkorb') ?>" class="btn btn-outline-light">
                        <i class="fas fa-shopping-cart"></i> Warenkorb
                        <?php if ($anzahlArtikel > 0): ?>
                            <span class="badge bg-primary"><?= $anzahlArtikel ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container py-4">
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