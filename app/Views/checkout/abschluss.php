<?php
/**
 * Checkout Abschluss View
 *
 * @package DrumShop
 */
?>
<div class="text-center my-5">
    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
    <h1 class="mt-4">Vielen Dank für Ihre Bestellung!</h1>
    <p class="lead">Ihre Bestellung wurde erfolgreich aufgenommen.</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Bestellübersicht</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Bestellnummer:</strong> <?= $bestellung['id'] ?></p>
                        <p><strong>Bestelldatum:</strong> <?= date('d.m.Y H:i', strtotime($bestellung['erstellt_am'])) ?> Uhr</p>
                        <p><strong>Status:</strong>
                            <span class="badge bg-primary"><?= $bestellung['status'] ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?= $bestellung['kunde_name'] ?></p>
                        <p><strong>E-Mail:</strong> <?= $bestellung['kunde_email'] ?></p>
                        <p><strong>Zahlungsmethode:</strong> <?= $bestellung['zahlungsmethode'] ?></p>
                        <p><strong>Zahlungsstatus:</strong>
                            <span class="badge <?= $bestellung['zahlungsstatus'] == 'bezahlt' ? 'bg-success' : 'bg-warning' ?>">
                                <?= $bestellung['zahlungsstatus'] ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <h6>Lieferadresse:</h6>
                        <p><?= nl2br($bestellung['lieferadresse']) ?></p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Produkt</th>
                            <th class="text-center">Menge</th>
                            <th class="text-end">Einzelpreis</th>
                            <th class="text-end">Summe</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($positionen as $position) : ?>
                            <tr>
                                <td><?= $position['produkt_name'] ?></td>
                                <td class="text-center"><?= $position['menge'] ?></td>
                                <td class="text-end"><?= number_format($position['einzelpreis'], 2, ',', '.') ?> €</td>
                                <td class="text-end"><?= number_format($position['zwischensumme'], 2, ',', '.') ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Gesamtsumme:</strong></td>
                            <td class="text-end"><strong><?= number_format($bestellung['gesamtpreis'], 2, ',', '.') ?> €</strong></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <?php if (!empty($bestellung['anmerkungen'])) : ?>
                    <div class="mt-3">
                        <h6>Anmerkungen:</h6>
                        <p><?= nl2br($bestellung['anmerkungen']) ?></p>
                    </div>
                <?php endif; ?>

                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle"></i> Eine Bestätigung wurde an Ihre E-Mail-Adresse gesendet.
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="<?= base_url() ?>" class="btn btn-primary">
                <i class="fas fa-home"></i> Zurück zur Startseite
            </a>
        </div>
    </div>
</div>