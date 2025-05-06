<?php
/**
 * Home Page
 *
 * @package DrumShop
 */
?>
<div class="jumbotron text-center my-4 p-5 bg-light rounded">
    <h1 class="display-4">Willkommen im DrumShop</h1>
    <p class="lead">Ihr Spezialist für Schlagzeug und Percussion</p>
    <hr class="my-4">
    <p>Entdecken Sie unsere große Auswahl an Drums, Becken, Hardware und Zubehör.</p>
    <a class="btn btn-primary btn-lg" href="<?= base_url('produkte') ?>" role="button">Produkte entdecken</a>
</div>

<!-- Hervorgehobene Produkte -->
<section class="my-5">
    <h2 class="text-center mb-4">Ausgewählte Produkte</h2>
    <div class="row">
        <?php foreach ($hervorgehobeneProdukte as $produkt) : ?>
            <div class="col-md-3 mb-4">
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
                        <?php if ($produkt['bestand'] > 0 && $produkt['bestand'] <= 5): ?>
                            <p class="card-text"><small class="text-danger">Nur noch <?= $produkt['bestand'] ?> auf Lager</small></p>
                        <?php endif; ?>
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
    </div>
    <div class="text-center mt-3">
        <a href="<?= base_url('produkte') ?>" class="btn btn-outline-primary">Alle Produkte ansehen</a>
    </div>
</section>

<!-- Kategorien -->
<section class="my-5">
    <h2 class="text-center mb-4">Unsere Kategorien</h2>
    <div class="row">
        <?php foreach ($kategorien as $kategorie) : ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <?php if (!empty($kategorie['bild_url'])) : ?>
                    <img src="<?= base_url($kategorie['bild_url']) ?>" class="card-img-top" alt="<?= $kategorie['name'] ?>">
                <?php else : ?>
                    <img src="<?= base_url('assets/images/no-image.jpg') ?>" class="card-img-top" alt="Kein Bild verfügbar">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?= $kategorie['name'] ?></h5>
                    <p class="card-text"><?= substr($kategorie['beschreibung'] ?? '', 0, 100) ?>...</p>
                    <a href="<?= base_url('produkte/kategorie/' . $kategorie['id']) ?>" class="btn btn-primary">Ansehen</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Neue Produkte -->
<section class="my-5">
    <h2 class="text-center mb-4">Neue Produkte</h2>
    <div class="row">
        <?php foreach ($neueProdukte as $produkt) : ?>
            <div class="col-md-3 mb-4">
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
                        <?php if ($produkt['bestand'] > 0 && $produkt['bestand'] <= 5): ?>
                            <p class="card-text"><small class="text-danger">Nur noch <?= $produkt['bestand'] ?> auf Lager</small></p>
                        <?php endif; ?>
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
    </div>
</section>