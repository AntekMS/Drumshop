<?php
/**
 * Admin Bestellungsbearbeitung View
 *
 * @package DrumShop
 */
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Bestellung #<?= $bestellung['id'] ?> bearbeiten</h1>
    <div class="btn-toolbar">
        <a href="<?= base_url('admin/bestellungen') ?>" class="btn btn-sm btn-outline-secondary me-2">
            <i class="fas fa-arrow-left"></i> Zurück zur Übersicht
        </a>
        <a href="<?= base_url('admin/bestellungen/detail/' . $bestellung['id']) ?>" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-eye"></i> Zur Detailansicht
        </a>
    </div>
</div>

<form action="<?= base_url('admin/bestellungen/aktualisieren/' . $bestellung['id']) ?>" method="post">
    <div class="row mb-4">
        <!-- Kundendaten -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Kundeninformationen</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="kunde_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="kunde_name" name="kunde_name" value="<?= $bestellung['kunde_name'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="kunde_email" class="form-label">E-Mail</label>
                        <input type="email" class="form-control" id="kunde_email" name="kunde_email" value="<?= $bestellung['kunde_email'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="lieferadresse" class="form-label">Lieferadresse</label>
                        <textarea class="form-control" id="lieferadresse" name="lieferadresse" rows="3" required><?= $bestellung['lieferadresse'] ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="zahlungsmethode" class="form-label">Zahlungsmethode</label>
                        <select class="form-select" id="zahlungsmethode" name="zahlungsmethode" required>
                            <option value="paypal" <?= $bestellung['zahlungsmethode'] == 'paypal' ? 'selected' : '' ?>>PayPal</option>
                            <option value="kreditkarte" <?= $bestellung['zahlungsmethode'] == 'kreditkarte' ? 'selected' : '' ?>>Kreditkarte</option>
                            <option value="rechnung" <?= $bestellung['zahlungsmethode'] == 'rechnung' ? 'selected' : '' ?>>Rechnung</option>
                            <option value="vorkasse" <?= $bestellung['zahlungsmethode'] == 'vorkasse' ? 'selected' : '' ?>>Vorkasse</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="anmerkungen" class="form-label">Anmerkungen</label>
                        <textarea class="form-control" id="anmerkungen" name="anmerkungen" rows="3"><?= $bestellung['anmerkungen'] ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bestellungsstatus -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Bestellstatus</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
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

                    <div class="mb-3">
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

                    <div class="mb-3">
                        <label for="sendungsnummer" class="form-label">Sendungsnummer</label>
                        <input type="text" name="sendungsnummer" id="sendungsnummer" class="form-control" value="<?= $bestellung['sendungsnummer'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bestelldatum</label>
                        <p class="form-control-plaintext"><?= date('d.m.Y H:i', strtotime($bestellung['erstellt_am'])) ?> Uhr</p>
                    </div>

                    <?php if (!empty($bestellung['aktualisiert_am']) && $bestellung['aktualisiert_am'] != $bestellung['erstellt_am']) : ?>
                        <div class="mb-3">
                            <label class="form-label">Letzte Aktualisierung</label>
                            <p class="form-control-plaintext"><?= date('d.m.Y H:i', strtotime($bestellung['aktualisiert_am'])) ?> Uhr</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bestellpositionen -->
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
                        <th class="text-center">Menge</th>
                        <th class="text-end">Einzelpreis (€)</th>
                        <th class="text-end">Summe (€)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $gesamtsumme = 0;
                    foreach ($positionen as $position) :
                        $gesamtsumme += $position['zwischensumme'];
                        ?>
                        <tr>
                            <td>
                                <?= $position['produkt_name'] ?>
                                <small class="text-muted d-block">ID: <?= $position['produkt_id'] ?></small>
                            </td>
                            <td class="text-center">
                                <input type="number" class="form-control form-control-sm text-center position-menge"
                                       name="positionen[<?= $position['id'] ?>]"
                                       value="<?= $position['menge'] ?>"
                                       min="1"
                                       style="max-width: 70px; margin: 0 auto;"
                                       data-id="<?= $position['id'] ?>">
                            </td>
                            <td class="text-end">
                                <input type="number" class="form-control form-control-sm text-end position-preis"
                                       name="preise[<?= $position['id'] ?>]"
                                       value="<?= $position['einzelpreis'] ?>"
                                       step="0.01" min="0.01"
                                       data-id="<?= $position['id'] ?>">
                            </td>
                            <td class="text-end position-summe" data-id="<?= $position['id'] ?>">
                                <?= number_format($position['zwischensumme'], 2, ',', '.') ?> €
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Gesamtsumme:</th>
                        <th class="text-end" id="gesamtsumme"><?= number_format($bestellung['gesamtpreis'], 2, ',', '.') ?> €</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Speichern-Button -->
    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
        <a href="<?= base_url('admin/bestellungen/detail/' . $bestellung['id']) ?>" class="btn btn-secondary">Abbrechen</a>
        <button type="submit" class="btn btn-primary">Änderungen speichern</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status-Dropdown-Farben aktualisieren
        const statusSelect = document.getElementById('status');
        const zahlungsstatusSelect = document.getElementById('zahlungsstatus');

        // Event-Handler für Status-Änderung
        statusSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const colorClass = selectedOption.getAttribute('data-color');

            // Alle Farbklassen entfernen
            this.classList.remove('bg-primary', 'bg-warning', 'bg-info', 'bg-success', 'bg-danger', 'text-white');

            // Neue Farbklasse hinzufügen
            if (colorClass) {
                const classes = colorClass.split(' ');
                classes.forEach(cls => this.classList.add(cls));
            }
        });

        // Event-Handler für Zahlungsstatus-Änderung
        zahlungsstatusSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const colorClass = selectedOption.getAttribute('data-color');

            // Alle Farbklassen entfernen
            this.classList.remove('bg-warning', 'bg-success', 'bg-danger');

            // Neue Farbklasse hinzufügen
            if (colorClass) {
                this.classList.add(colorClass);
            }
        });

        // Berechnung der Positionssummen und Gesamtsumme beim Ändern von Menge oder Preis
        const mengenInputs = document.querySelectorAll('.position-menge');
        const preisInputs = document.querySelectorAll('.position-preis');

        // Funktion zum Aktualisieren der Summen
        function updateSummen() {
            let gesamtsumme = 0;

            // Alle Positionen durchgehen und Summen berechnen
            mengenInputs.forEach(mengenInput => {
                const id = mengenInput.getAttribute('data-id');
                const menge = parseFloat(mengenInput.value) || 0;
                const preisInput = document.querySelector(`.position-preis[data-id="${id}"]`);
                const summeElement = document.querySelector(`.position-summe[data-id="${id}"]`);

                if (preisInput && summeElement) {
                    const preis = parseFloat(preisInput.value) || 0;
                    const summe = menge * preis;
                    gesamtsumme += summe;

                    // Zwischensumme formatiert anzeigen
                    summeElement.textContent = summe.toLocaleString('de-DE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' €';
                }
            });

            // Gesamtsumme aktualisieren
            const gesamtsummeElement = document.getElementById('gesamtsumme');
            if (gesamtsummeElement) {
                gesamtsummeElement.textContent = gesamtsumme.toLocaleString('de-DE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' €';
            }
        }

        // Event-Listener für Mengen- und Preisänderungen
        mengenInputs.forEach(input => {
            input.addEventListener('change', updateSummen);
            input.addEventListener('input', updateSummen);
        });

        preisInputs.forEach(input => {
            input.addEventListener('change', updateSummen);
            input.addEventListener('input', updateSummen);
        });
    });
</script>