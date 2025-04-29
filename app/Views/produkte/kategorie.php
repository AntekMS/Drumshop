<?php
/**
 * Produkt Kategorie View
 *
 * @package DrumShop
 */
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('produkte') ?>">Produkte</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $kategorie['name'] ?></li>
    </ol>
</nav>

<div class="row my-4">
    <div class="col-12">
        <h1><?= $kategorie['name'] ?></h1>
        <p class="lead"><?= $kategorie['beschreibung'] ?? '' ?></p>
    </div>
</div>

<?php
// Unterkategorien anzeigen, falls vorhanden
$kategorieModel = new \App\Models\KategorieModel();
$unterkategorien = $kategorieModel->getUnterkategorien($kategorie['id']);
if (!empty($unterkategorien)) :
    ?>
    <div class="row mb-4">
        <div class="col-12">
            <h2>Unterkategorien</h2>
            <div class="row">
                <?php foreach ($unterkategorien as $unterkat) : ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <?php if (!empty($unterkat['bild_url'])) : ?>
                                <img src="<?= base_url($unterkat['bild_url']) ?>" class="card-img-top" alt="<?= $unterkat['name'] ?>">
                            <?php else : ?>
                                <img src="<?= base_url('assets/images/no-image.jpg') ?>" class="card-img-top" alt="Kein Bild verfügbar">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $unterkat['name'] ?></h5>
                                <p class="card-text"><?= substr($unterkat['beschreibung'] ?? '', 0, 100) ?>...</p>
                                <a href="<?= base_url('produkte/kategorie/' . $unterkat['id']) ?>" class="btn btn-primary">Ansehen</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <?php if (empty($produkte)) : ?>
        <div class="col-12">
            <div class="alert alert-info">
                Keine Produkte in dieser Kategorie gefunden.
            </div>
        </div>
    <?php else : ?>
        <?php foreach ($produkte as $produkt) : ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if (!empty($produkt['bild_url'])) : ?>
                        <img src="<?= base_url($produkt['bild_url']) ?>" class="card-img-top" alt="<?= $produkt['name'] ?>">
                    <?php else : ?>
                        <img src="<?= base_url('assets/images/no-image.jpg') ?>" class="card-img-top" alt="Kein Bild verfügbar">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= $produkt['name'] ?></h5>
                        <p class="card-text"><?= substr($produkt['beschreibung'], 0, 100) ?>...</p>
                        <p class="card-text text-primary fw-bold"><?= number_format($produkt['preis'], 2, ',', '.') ?> €</p>
                        <div class="mt-auto d-flex justify-content-between">
                            <a href="<?= base_url('produkte/detail/' . $produkt['id']) ?>" class="btn btn-outline-primary">Details</a>
                            <?php if ($produkt['bestand'] > 0) : ?>
                                <form action="<?= base_url('warenkorb/hinzufuegen') ?>" method="post" class="d-inline">
                                    <input type="hidden" name="produkt_id" value="<?= $produkt['id'] ?>">
                                    <input type="hidden" name="menge" value="1">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>