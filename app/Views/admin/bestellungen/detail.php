<?php
/**
 * Admin Bestellungsdetail View
 *
 * @package DrumShop
 */
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Bestellung #<?= $bestellung['id'] ?></h1>
    <div class="btn-toolbar">
        <a href="<?= base_url('admin/bestellungen') ?>" class="btn btn-sm btn-outline-secondary me-2">
            <i class="fas fa-arrow-left"></i> Zurück zur Übersicht
        </a>
        <a href="javascript:window.print();" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-print"></i> Drucken
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Kundeninformationen</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= $bestellung['kunde_name'] ?></p>
                <p><strong>E-Mail:</strong> <a href="mailto:<?= $bestellung['kunde_email'] ?>"><?= $bestellung['kunde_email'] ?></a></p>
                <p><strong>Bestelldatum:</strong> <?= date('d.m.Y H:i', strtotime($bestellung['erstellt_am'])) ?> Uhr</p>
                <p><strong>Zahlungsmethode:</strong> <?= $bestellung['zahlungsmethode'] ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Lieferadresse</h5>
            </div>
            <div class="card-body">
                <address>
                    <?= nl2br($bestellung['lieferadresse']) ?>
                </address>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Bestellstatus</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/bestellungen/statusAendern/' . $bestellung['id']) ?>" method="post" class="row">
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select <?php
                        if ($bestellung['status'] == 'neu') echo 'bg-primary text-white';
                        elseif ($bestellung['status'] == 'bearbeitet') echo 'bg-warning';
                        elseif ($bestellung['status'] == 'versandt') echo 'bg-info';
                        elseif ($bestellung['status'] == 'geliefert') echo 'bg-success';
                        elseif ($bestellung['status'] == 'storniert') echo 'bg-danger';
                        ?>">
                            <option value="neu" <?= $bestellung['status'] == 'neu' ? 'selected' : '' ?> data-color="bg-primary text-white">Neu</option>
                            <option value="bearbeitet" <?= $bestellung['status'] == 'bearbeitet' ? 'selected' : '' ?> data-color="bg-warning">In Bearbeitung</option>
                            <option value="versandt" <?= $bestellung['status'] == 'versandt' ? 'selected' : '' ?> data-color="bg-info">Versandt</option>
                            <option value="geliefert" <?= $bestellung['status'] == 'geliefert' ? 'selected' : '' ?> data-color="bg-success">Geliefert</option>
                            <option value="storniert" <?= $bestellung['status'] == 'storniert' ? 'selected' : '' ?> data-color="bg-danger">Storniert</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="zahlungsstatus" class="form-label">Zahlungsstatus</label>
                        <select name="zahlungsstatus" id="zahlungsstatus" class="form-select <?php
                        if ($bestellung['zahlungsstatus'] == 'ausstehend') echo 'bg-warning';
                        elseif ($bestellung['zahlungsstatus'] == 'bezahlt') echo 'bg-success';
                        elseif ($bestellung['zahlungsstatus'] == 'zurückerstattet') echo 'bg-danger';
                        ?>">
                            <option value="ausstehend" <?= $bestellung['zahlungsstatus'] == 'ausstehend' ? 'selected' : '' ?> data-color="bg-warning">Ausstehend</option>
                            <option value="bezahlt" <?= $bestellung['zahlungsstatus'] == 'bezahlt' ? 'selected' : '' ?> data-color="bg-success">Bezahlt</option>
                            <option value="zurückerstattet" <?= $bestellung['zahlungsstatus'] == 'zurückerstattet' ? 'selected' : '' ?> data-color="bg-danger">Zurückerstattet</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="sendungsnummer" class="form-label">Sendungsnummer</label>
                        <input type="text" name="sendungsnummer" id="sendungsnummer" class="form-control" value="<?= $bestellung['sendungsnummer'] ?? '' ?>">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Status aktualisieren</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Bestellpositionen</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                <tr>
                    <th>Produkt</th>
                    <th class="text-center">Anzahl</th>
                    <th class="text-end">Einzelpreis</th>
                    <th class="text-end">Summe</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($positionen as $position) : ?>
                    <tr>
                        <td>
                            <?= $position['produkt_name'] ?>
                            <small class="text-muted d-block">ID: <?= $position['produkt_id'] ?></small>
                        </td>
                        <td class="text-center"><?= $position['menge'] ?></td>
                        <td class="text-end"><?= number_format($position['einzelpreis'], 2, ',', '.') ?> €</td>
                        <td class="text-end"><?= number_format($position['zwischensumme'], 2, ',', '.') ?> €</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Gesamtsumme:</th>
                    <th class="text-end"><?= number_format($bestellung['gesamtpreis'], 2, ',', '.') ?> €</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($bestellung['anmerkungen'])) : ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Anmerkungen</h5>
        </div>
        <div class="card-body">
            <p><?= nl2br($bestellung['anmerkungen']) ?></p>
        </div>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Aktionen</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('admin/bestellungen/email/' . $bestellung['id']) ?>" class="btn btn-info w-100 mb-2">
                    <i class="fas fa-envelope"></i> E-Mail an Kunden senden
                </a>
            </div>
            <div class="col-md-6">
                <a href="<?= base_url('admin/bestellungen/rechnung/' . $bestellung['id']) ?>" class="btn btn-secondary w-100 mb-2">
                    <i class="fas fa-file-invoice"></i> Rechnung erstellen
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn-toolbar, form, .card-header, .card:last-child {
            display: none;
        }
        .card {
            border: none !important;
        }
        .card-body {
            padding: 0 !important;
        }
    }
</style>