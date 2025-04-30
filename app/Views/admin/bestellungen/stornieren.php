<?php
/**
 * Admin Bestellung Stornieren View
 *
 * @package DrumShop
 */
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Bestellung #<?= $bestellung['id'] ?> stornieren</h1>
    <div class="btn-toolbar">
        <a href="<?= base_url('admin/bestellungen') ?>" class="btn btn-sm btn-outline-secondary me-2">
            <i class="fas fa-arrow-left"></i> Zurück zur Übersicht
        </a>
        <a href="<?= base_url('admin/bestellungen/detail/' . $bestellung['id']) ?>" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-eye"></i> Zur Detailansicht
        </a>
    </div>
</div>

<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Achtung:</strong> Das Stornieren einer Bestellung kann nicht rückgängig gemacht werden.
    Bitte prüfen Sie vor dem Fortfahren alle Informationen.
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Bestellinformationen</h5>
            </div>
            <div class="card-body">
                <p><strong>Bestellnummer:</strong> #<?= $bestellung['id'] ?></p>
                <p><strong>Kunde:</strong> <?= $bestellung['kunde_name'] ?></p>
                <p><strong>E-Mail:</strong> <?= $bestellung['kunde_email'] ?></p>
                <p><strong>Bestelldatum:</strong> <?= date('d.m.Y H:i', strtotime($bestellung['erstellt_am'])) ?> Uhr</p>
                <p><strong>Gesamtbetrag:</strong> <?= number_format($bestellung['gesamtpreis'], 2, ',', '.') ?> €</p>
                <p><strong>Aktueller Status:</strong>
                    <span class="badge <?php
                    switch ($bestellung['status']) {
                        case 'neu': echo 'bg-primary'; break;
                        case 'bearbeitet': echo 'bg-warning'; break;
                        case 'versandt': echo 'bg-info'; break;
                        case 'geliefert': echo 'bg-success'; break;
                        case 'storniert': echo 'bg-danger'; break;
                        default: echo 'bg-secondary';
                    }
                    ?>">
                        <?= $bestellung['status'] ?>
                    </span>
                </p>
                <p><strong>Zahlungsstatus:</strong>
                    <span class="badge <?php
                    switch ($bestellung['zahlungsstatus']) {
                        case 'ausstehend': echo 'bg-warning'; break;
                        case 'bezahlt': echo 'bg-success'; break;
                        case 'zurückerstattet': echo 'bg-danger'; break;
                        default: echo 'bg-secondary';
                    }
                    ?>">
                        <?= $bestellung['zahlungsstatus'] ?>
                    </span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Stornierung bestätigen</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/bestellungen/stornierungDurchfuehren') ?>" method="post">
                    <input type="hidden" name="bestellung_id" value="<?= $bestellung['id'] ?>">

                    <div class="mb-3">
                        <label for="stornierung_grund" class="form-label">Grund der Stornierung</label>
                        <select class="form-select" id="stornierung_grund" name="stornierung_grund">
                            <option value="Kundenwunsch">Stornierung auf Kundenwunsch</option>
                            <option value="Zahlungsprobleme">Zahlungsprobleme</option>
                            <option value="Lieferengpass">Artikel nicht mehr verfügbar</option>
                            <option value="Doppelte Bestellung">Doppelte Bestellung</option>
                            <option value="Technisches Problem">Technisches Problem</option>
                            <option value="Sonstiges">Sonstiges</option>
                        </select>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="bestand_zurueckbuchen" name="bestand_zurueckbuchen" value="1" checked>
                        <label class="form-check-label" for="bestand_zurueckbuchen">Artikelbestand zurückbuchen</label>
                        <div class="form-text">Die Artikel werden wieder dem Lagerbestand hinzugefügt.</div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="email_senden" name="email_senden" value="1" checked>
                        <label class="form-check-label" for="email_senden">Stornierungsbestätigung per E-Mail senden</label>
                        <div class="form-text">Der Kunde erhält eine E-Mail mit der Stornierungsbestätigung.</div>
                    </div>

                    <?php if ($bestellung['zahlungsstatus'] == 'bezahlt'): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Da die Bestellung bereits bezahlt wurde, wird der Zahlungsstatus auf "zurückerstattet" gesetzt.
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="<?= base_url('admin/bestellungen/detail/' . $bestellung['id']) ?>" class="btn btn-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban"></i> Bestellung stornieren
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bestätigung vor dem Absenden
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!confirm('Sind Sie sicher, dass Sie diese Bestellung stornieren möchten?')) {
                e.preventDefault();
            }
        });
    });
</script>