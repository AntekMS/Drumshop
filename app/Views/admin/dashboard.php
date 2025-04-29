<?php
/**
 * Admin Dashboard View
 *
 * @package DrumShop
 */
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Exportieren</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Drucken</button>
        </div>
    </div>
</div>

<!-- Statistik-Kacheln -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Bestellungen</h6>
                        <h2 class="mb-0"><?= $bestellungen_gesamt ?></h2>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x"></i>
                </div>
                <small>Davon <?= $bestellungen_neu ?> neue Bestellungen</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Umsatz</h6>
                        <h2 class="mb-0"><?= number_format($umsatz_gesamt, 2, ',', '.') ?> €</h2>
                    </div>
                    <i class="fas fa-euro-sign fa-2x"></i>
                </div>
                <small>Gesamt</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Produkte</h6>
                        <h2 class="mb-0"><?= $produkte_gesamt ?></h2>
                    </div>
                    <i class="fas fa-drum fa-2x"></i>
                </div>
                <small><?= $produkte_lagernd ?> auf Lager, <?= $produkte_ohne_bestand ?> nicht verfügbar</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Kategorien</h6>
                        <h2 class="mb-0"><?= $kategorieModel->countAll() ?></h2>
                    </div>
                    <i class="fas fa-folder fa-2x"></i>
                </div>
                <small>&nbsp;</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Letzte Bestellungen -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Letzte Bestellungen</h5>
            </div>
            <div class="card-body">
                <?php if (empty($letzte_bestellungen)) : ?>
                    <div class="alert alert-info">
                        Keine Bestellungen vorhanden.
                    </div>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kunde</th>
                                <th>Betrag</th>
                                <th>Status</th>
                                <th>Datum</th>
                                <th>Aktionen</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($letzte_bestellungen as $bestellung) : ?>
                                <tr>
                                    <td>#<?= $bestellung['id'] ?></td>
                                    <td><?= $bestellung['kunde_name'] ?></td>
                                    <td><?= number_format($bestellung['gesamtpreis'], 2, ',', '.') ?> €</td>
                                    <td>
                                            <span class="badge <?=
                                            $bestellung['status'] == 'neu' ? 'bg-primary' :
                                                ($bestellung['status'] == 'bearbeitet' ? 'bg-warning' :
                                                    ($bestellung['status'] == 'versandt' ? 'bg-info' :
                                                        ($bestellung['status'] == 'geliefert' ? 'bg-success' : 'bg-danger')))
                                            ?>">
                                                <?= $bestellung['status'] ?>
                                            </span>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($bestellung['erstellt_am'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/bestellungen/detail/' . $bestellung['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="<?= base_url('admin/bestellungen') ?>" class="btn btn-sm btn-outline-primary">Alle Bestellungen anzeigen</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bestseller -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Bestseller</h5>
            </div>
            <div class="card-body">
                <?php if (empty($bestseller)) : ?>
                    <div class="alert alert-info">
                        Keine Verkaufsdaten vorhanden.
                    </div>
                <?php else : ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($bestseller as $produkt) : ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= $produkt['produkt_name'] ?>
                                <span class="badge bg-primary rounded-pill"><?= $produkt['gesamt_verkauft'] ?> verkauft</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Produkte mit niedrigem Bestand -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Produkte mit niedrigem Bestand</h5>
            </div>
            <div class="card-body">
                <?php
                $produktModel = new \App\Models\ProduktModel();
                $niedrigerBestand = $produktModel->where('bestand >', 0)->where('bestand <', 5)->orderBy('bestand', 'ASC')->findAll();

                if (empty($niedrigerBestand)) :
                    ?>
                    <div class="alert alert-success">
                        Alle Produkte haben ausreichend Bestand.
                    </div>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produkt</th>
                                <th>Bestand</th>
                                <th>Preis</th>
                                <th>Aktionen</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($niedrigerBestand as $produkt) : ?>
                                <tr>
                                    <td><?= $produkt['id'] ?></td>
                                    <td><?= $produkt['name'] ?></td>
                                    <td>
                                        <span class="badge bg-warning"><?= $produkt['bestand'] ?></span>
                                    </td>
                                    <td><?= number_format($produkt['preis'], 2, ',', '.') ?> €</td>
                                    <td>
                                        <a href="<?= base_url('admin/produkte/bearbeiten/' . $produkt['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Bearbeiten
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>