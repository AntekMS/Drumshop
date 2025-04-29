<?php
/**
 * Produkt Liste View
 *
 * @package DrumShop
 */
?>
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filter</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('produkte') ?>" method="get">
                    <!-- Kategoriefilter -->
                    <div class="mb-3">
                        <label for="kategorie" class="form-label">Kategorie</label>
                        <select name="kategorie" id="kategorie" class="form-select">
                            <option value="">Alle Kategorien</option>
                            <?php
                            $kategorieModel = new \App\Models\KategorieModel();
                            $alleKategorien = $kategorieModel->findAll();
                            foreach ($alleKategorien as $kat) :
                                $selected = (isset($_GET['kategorie']) && $_GET['kategorie'] == $kat['id']) ? 'selected' : '';
                                ?>
                                <option value="<?= $kat['id'] ?>" <?= $selected ?>><?= $kat['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Preisfilter -->
                    <div class="mb-3">
                        <label for="preis_min" class="form-label">Preis von</label>
                        <input type="number" class="form-control" id="preis_min" name="preis_min" value="<?= isset($_GET['preis_min']) ? $_GET['preis_min'] : '' ?>" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="preis_max" class="form-label">Preis bis</label>
                        <input type="number" class="form-control" id="preis_max" name="preis_max" value="<?= isset($_GET['preis_max']) ? $_GET['preis_max'] : '' ?>" min="0">
                    </div>

                    <!-- Sortierung -->
                    <div class="mb-3">
                        <label for="sortierung" class="form-label">Sortierung</label>
                        <select name="sortierung" id="sortierung" class="form-select">
                            <option value="name_asc" <?= (isset($_GET['sortierung']) && $_GET['sortierung'] == 'name_asc') ? 'selected' : '' ?>>Name (A-Z)</option>
                            <option value="name_desc" <?= (isset($_GET['sortierung']) && $_GET['sortierung'] == 'name_desc') ? 'selected' : '' ?>>Name (Z-A)</option>
                            <option value="preis_asc" <?= (isset($_GET['sortierung']) && $_GET['sortierung'] == 'preis_asc') ? 'selected' : '' ?>>Preis (aufsteigend)</option>
                            <option value="preis_desc" <?= (isset($_GET['sortierung']) && $_GET['sortierung'] == 'preis_desc') ? 'selected' : '' ?>>Preis (absteigend)</option>
                            <option value="neu" <?= (isset($_GET['sortierung']) && $_GET['sortierung'] == 'neu') ? 'selected' : '' ?>>Neueste zuerst</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Filtern</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <h1 class="mb-4">Unsere Produkte</h1>

        <?php if (empty($produkte)) : ?>
            <div class="alert alert-info">
                Keine Produkte gefunden.
            </div>
        <?php else : ?>
            <div class="row">
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
                                <p class="card-text"><?= substr($produkt['beschreibung'], 0, 80) ?>...</p>
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
                                    <?php else : ?>
                                        <span class="badge bg-danger">Nicht verfügbar</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filter</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('produkte') ?>" method="get">
                    <!-- Kategoriefilter -->
                    <div class="mb-3">
                        <label for="kategorie" class="form-label">Kategorie</label>
                        <select name="kategorie" id="kategorie" class="form-select">
                            <option value="">Alle Kategorien</option>
                            <?php
                            $kategorieModel = new \App\Models\KategorieModel();
                            $alleKategorien = $kategorieModel->findAll();
                            foreach ($alleKategorien as $kat) :
                                $selected = (isset($_GET['kategorie']) && $_GET['kategorie'] == $kat['id']) ? 'selected' : '';
                                ?>
                                <option value="<?= $kat['id'] ?>" <?= $selected ?>><?= $kat['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Preisfilter -->
                    <div class="mb-3">
                        <label for="preis_min" class="form-label">Preis von</label>
                        <input type="number" class="form-control" id="preis_min" name="preis_min" value="<?= isset($_GET['preis_min']) ? $_GET['preis_min'] : '' ?>" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="preis_max" class="form-label">Preis bis</label>
                        <input type="number" class="form-control" id="preis_max" name="preis_max" value="<?= isset($_GET['preis_max']) ? $_GET['preis_max'] : '' ?>" min="0">
                    </div>

                    <!-- Sortierung -->
                    <div class="mb-3">
                        <label for="sortierung" class="form-label">Sortierung</label>
                        <select name="sortierung" id="sortierung" class="form-select">
                            <option value="name_asc" <?= (isset($_GET['sortierung']) && $_GET['sortierung'] == 'name_asc') ? 'selected' : '' ?>>Name (A-Z)</option>
                            <option value="name_desc" <?= (isset($_GET['sortierung']) && $_GET['sortierung'] == 'name_desc') ? 'selected' : '' ?>>Name (Z-A)</option>
                            <option value="preis_asc" <?= (isset($_GET['sortierung']) && $_GET['sortierung'] == 'preis_asc') ? 'selected' : '' ?>>Preis (aufsteigend)</option>
                            <option value="preis_desc" <?= (isset($_GET['sortierung']) && $_GET['sortierung'] == 'preis_desc') ? 'selected' : '' ?>>Preis (absteigend)</option>
                            <option value="neu" <?= (isset($_GET['sortierung']) && $_GET['sortierung'] == 'neu') ? 'selected' : '' ?>>Neueste zuerst</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Filtern</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <h1 class="mb-4">Unsere Produkte</h1>

        <?php if (empty($produkte)) : ?>
            <div class="alert alert-info">
                Keine Produkte gefunden.
            </div>
        <?php else : ?>
            <div class="row">
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
                                <p class="card-text"><?= substr($produkt['beschreibung'], 0, 80) ?>...</p>
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
                                    <?php else : ?>
                                        <span class="badge bg-danger">Nicht verfügbar</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>