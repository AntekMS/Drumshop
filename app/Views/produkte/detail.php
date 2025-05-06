<?php
/**
 * Produkt Detail View
 *
 * @package DrumShop
 */
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('produkte') ?>">Produkte</a></li>
            <?php if (!empty($kategorie)) : ?>
                <li class="breadcrumb-item"><a href="<?= base_url('produkte/kategorie/' . $kategorie['id']) ?>"><?= $kategorie['name'] ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?= $produkt['name'] ?></li>
        </ol>
    </nav>

    <div class="row my-4">
        <div class="col-md-5">
            <?php if (!empty($produkt['bild_url'])) : ?>
                <img src="<?= base_url($produkt['bild_url']) ?>" class="img-fluid rounded" alt="<?= $produkt['name'] ?>">
            <?php else : ?>
                <img src="<?= base_url('assets/images/no-image.jpg') ?>" class="img-fluid rounded" alt="Kein Bild verfügbar">
            <?php endif; ?>
        </div>
        <div class="col-md-7">
            <h1><?= $produkt['name'] ?></h1>
            <p class="lead"><?= $produkt['beschreibung'] ?></p>

            <div class="d-flex align-items-center my-4">
                <h2 class="text-primary fw-bold me-3"><?= number_format($produkt['preis'], 2, ',', '.') ?> €</h2>
                <?php if ($produkt['bestand'] <= 0): ?>
                    <span class="badge bg-danger">Nicht verfügbar</span>
                <?php elseif ($produkt['bestand'] <= 5): ?>
                    <span class="badge bg-warning">Nur noch <?= $produkt['bestand'] ?> auf Lager</span>
                <?php else: ?>
                    <span class="badge bg-success">Auf Lager</span>
                <?php endif; ?>
            </div>

            <div class="product-details mt-4">
                <p><strong>Artikelnummer:</strong> <?= $produkt['artikelnummer'] ?></p>
                <?php if (!empty($produkt['gewicht'])) : ?>
                    <p><strong>Gewicht:</strong> <?= $produkt['gewicht'] ?> kg</p>
                <?php endif; ?>
                <?php if (!empty($produkt['abmessungen'])) : ?>
                    <p><strong>Abmessungen:</strong> <?= $produkt['abmessungen'] ?></p>
                <?php endif; ?>
            </div>

            <?php if ($produkt['bestand'] > 0) : ?>
                <form action="<?= base_url('warenkorb/hinzufuegen') ?>" method="post" class="mt-4">
                    <input type="hidden" name="produkt_id" value="<?= $produkt['id'] ?>">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label for="menge" class="col-form-label">Menge:</label>
                        </div>
                        <div class="col-auto">
                            <input type="number" id="menge" name="menge" class="form-control" min="1" max="<?= $produkt['bestand'] ?>" value="1">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> In den Warenkorb
                            </button>
                        </div>
                    </div>
                </form>
            <?php else : ?>
                <div class="alert alert-warning mt-4">
                    <i class="fas fa-exclamation-triangle"></i> Dieses Produkt ist derzeit nicht verfügbar.
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php if (!empty($aehnlicheProdukte)) : ?>
    <section class="my-5">
        <h2 class="mb-4">Ähnliche Produkte</h2>
        <div class="row">
            <?php foreach ($aehnlicheProdukte as $aProdukt) : ?>
                <?php if ($aProdukt['id'] != $produkt['id']) : ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($aProdukt['bild_url'])) : ?>
                                <img src="<?= base_url($aProdukt['bild_url']) ?>" class="card-img-top" alt="<?= $aProdukt['name'] ?>">
                            <?php else : ?>
                                <img src="<?= base_url('assets/images/no-image.jpg') ?>" class="card-img-top" alt="Kein Bild verfügbar">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= $aProdukt['name'] ?></h5>
                                <p class="card-text"><?= substr($aProdukt['beschreibung'], 0, 80) ?>...</p>
                                <p class="card-text text-primary fw-bold"><?= number_format($aProdukt['preis'], 2, ',', '.') ?> €</p>
                                <?php if ($aProdukt['bestand'] > 0 && $aProdukt['bestand'] <= 5): ?>
                                    <p class="card-text"><small class="text-danger">Nur noch <?= $aProdukt['bestand'] ?> auf Lager</small></p>
                                <?php endif; ?>
                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="<?= base_url('produkte/detail/' . $aProdukt['id']) ?>" class="btn btn-outline-primary">Details</a>
                                    <?php if ($aProdukt['bestand'] > 0) : ?>
                                        <form action="<?= base_url('warenkorb/hinzufuegen') ?>" method="post" class="d-inline">
                                            <input type="hidden" name="produkt_id" value="<?= $aProdukt['id'] ?>">
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
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>