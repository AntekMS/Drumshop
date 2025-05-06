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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Bestellübersicht</h5>
                <span class="badge <?= $bestellung['zahlungsstatus'] == 'bezahlt' ? 'bg-success' : 'bg-warning' ?>">
                    <?= $bestellung['zahlungsstatus'] ?>
                </span>
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

                        <?php if ($bestellung['zahlungsmethode'] === 'paypal'): ?>
                            <p><strong>Zahlungsmethode:</strong>
                                <span class="paypal-logo">
                                <span class="paypal-blue">Pay</span><span class="paypal-light-blue">Pal</span>
                            </span>
                            </p>
                        <?php else: ?>
                            <p><strong>Zahlungsmethode:</strong>
                                <?php
                                $icon = '';
                                switch ($bestellung['zahlungsmethode']) {
                                    case 'kreditkarte':
                                        $icon = '<i class="fas fa-credit-card text-primary me-1"></i>';
                                        break;
                                    case 'rechnung':
                                        $icon = '<i class="fas fa-file-invoice text-secondary me-1"></i>';
                                        break;
                                    case 'vorkasse':
                                        $icon = '<i class="fas fa-university text-secondary me-1"></i>';
                                        break;
                                }
                                echo $icon . ucfirst($bestellung['zahlungsmethode']);
                                ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <h6>Lieferadresse:</h6>
                        <p><?= nl2br($bestellung['lieferadresse']) ?></p>
                    </div>
                </div>

                <?php if ($bestellung['zahlungsmethode'] === 'paypal' && $bestellung['zahlungsstatus'] === 'bezahlt'): ?>
                    <div class="security-badge mb-4">
                        <i class="fas fa-shield-alt"></i>
                        <div class="text">
                            <strong>PayPal-Käuferschutz:</strong> Ihre Zahlung wurde sicher über PayPal abgewickelt und ist durch den PayPal-Käuferschutz abgesichert.
                        </div>
                    </div>
                <?php endif; ?>

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

                <?php if ($bestellung['zahlungsmethode'] === 'vorkasse'): ?>
                    <div class="paypal-info-box mt-4">
                        <div class="title">Zahlungsinformationen</div>
                        <p>Bitte überweisen Sie den Gesamtbetrag auf folgendes Konto:</p>
                        <p>
                            <strong>Empfänger:</strong> DrumShop GmbH<br>
                            <strong>IBAN:</strong> DE12 3456 7890 1234 5678 90<br>
                            <strong>BIC:</strong> DEUTDEDBXXX<br>
                            <strong>Verwendungszweck:</strong> Bestellung <?= $bestellung['id'] ?>
                        </p>
                        <p>Ihre Bestellung wird nach Zahlungseingang versandt.</p>
                    </div>
                <?php endif; ?>

                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle"></i> Eine Bestätigung wurde an Ihre E-Mail-Adresse gesendet.
                </div>
            </div>
        </div>

        <!-- Buttons nebeneinander in einer Zeile mit verbesserter Darstellung -->
        <div class="text-center mb-5">
            <div class="d-flex justify-content-center">
                <a href="<?= base_url() ?>" class="btn btn-primary me-2">
                    <i class="fas fa-home"></i> Zurück zur Startseite
                </a>
                <button onclick="printRechnung()" class="btn btn-success">
                    <i class="fas fa-print"></i> Rechnung drucken
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Versteckter Bereich für den Druck -->
<div id="rechnung-print" style="display: none;">
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <!-- Kopfzeile der Rechnung -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
            <div>
                <h1 style="margin: 0; font-size: 24px;">DrumShop GmbH</h1>
                <p style="margin: 5px 0;">Bildungscampus 4<br>74076 Heilbronn<br>Deutschland</p>
                <p style="margin: 5px 0;">Tel: +49 7131 1237 0<br>E-Mail: info@drumshop.de</p>
            </div>
            <div style="text-align: right;">
                <h2 style="margin: 0; font-size: 24px;">RECHNUNG</h2>
                <p style="margin: 5px 0;"><strong>Nummer:</strong> RE-<?= date('Y') ?>-<?= str_pad($bestellung['id'], 5, '0', STR_PAD_LEFT) ?></p>
                <p style="margin: 5px 0;"><strong>Datum:</strong> <?= date('d.m.Y') ?></p>
                <p style="margin: 5px 0;"><strong>Bestellnummer:</strong> <?= $bestellung['id'] ?></p>
                <p style="margin: 5px 0;"><strong>Bestelldatum:</strong> <?= date('d.m.Y', strtotime($bestellung['erstellt_am'])) ?></p>
            </div>
        </div>

        <!-- Kundeninformationen -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
            <div style="width: 48%;">
                <h3 style="margin: 0 0 10px 0; font-size: 16px;">Rechnungsadresse</h3>
                <p style="margin: 0;"><?= $bestellung['kunde_name'] ?><br><?= nl2br($bestellung['lieferadresse']) ?></p>
            </div>
            <div style="width: 48%;">
                <h3 style="margin: 0 0 10px 0; font-size: 16px;">Lieferadresse</h3>
                <p style="margin: 0;"><?= $bestellung['kunde_name'] ?><br><?= nl2br($bestellung['lieferadresse']) ?></p>
            </div>
        </div>

        <!-- Zahlungsinformationen -->
        <div style="margin-bottom: 20px;">
            <p><strong>Zahlungsmethode:</strong> <?= ucfirst($bestellung['zahlungsmethode']) ?></p>
            <p><strong>Zahlungsstatus:</strong> <?= ucfirst($bestellung['zahlungsstatus']) ?></p>
        </div>

        <!-- Artikel Tabelle -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
            <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Produkt</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Menge</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Einzelpreis</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Summe</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($positionen as $position) : ?>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?= $position['produkt_name'] ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?= $position['menge'] ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><?= number_format($position['einzelpreis'], 2, ',', '.') ?> €</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><?= number_format($position['zwischensumme'], 2, ',', '.') ?> €</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3" style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>Zwischensumme:</strong></td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><?= number_format($bestellung['gesamtpreis'] / 1.19, 2, ',', '.') ?> €</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>MwSt. (19%):</strong></td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><?= number_format($bestellung['gesamtpreis'] - ($bestellung['gesamtpreis'] / 1.19), 2, ',', '.') ?> €</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>Gesamtbetrag:</strong></td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong><?= number_format($bestellung['gesamtpreis'], 2, ',', '.') ?> €</strong></td>
            </tr>
            </tfoot>
        </table>

        <!-- Fußzeile -->
        <div style="margin-top: 30px;">
            <div style="margin-bottom: 15px;">
                <p>Wir danken Ihnen für Ihren Einkauf bei DrumShop.</p>
                <p>Bei Fragen zu Ihrer Bestellung kontaktieren Sie uns bitte unter info@drumshop.de.</p>
            </div>

            <div style="margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 12px; color: #666;">
                <p>DrumShop GmbH | Bildungscampus 4 | 74076 Heilbronn | Steuernummer: 123/456/78910 | USt-IdNr.: DE123456789</p>
                <p>Amtsgericht Heilbronn HRB 12345 | Geschäftsführer: Julius Walter</p>
                <p>Bankverbindung: Musterbank | IBAN: DE12 3456 7890 1234 5678 90 | BIC: MUSTEDEXXX</p>
            </div>
        </div>
    </div>
</div>

<script>
    function printRechnung() {
        // Druckansicht in einem neuen Fenster öffnen
        const printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Rechnung RE-<?= date('Y') ?>-<?= str_pad($bestellung['id'], 5, '0', STR_PAD_LEFT) ?></title>');

        // Stil für die Druckansicht
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Arial, sans-serif; color: #333; }');
        printWindow.document.write('table { border-collapse: collapse; width: 100%; }');
        printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; }');
        printWindow.document.write('th { background-color: #f2f2f2; text-align: left; }');
        printWindow.document.write('h1, h2, h3 { color: #444; }');
        printWindow.document.write('</style>');

        printWindow.document.write('</head><body>');

        // Inhalt der Rechnung einfügen
        printWindow.document.write(document.getElementById('rechnung-print').innerHTML);

        printWindow.document.write('</body></html>');
        printWindow.document.close();

        // Kurze Verzögerung, um sicherzustellen, dass der Inhalt geladen ist
        setTimeout(function() {
            printWindow.print();
        }, 500);
    }
</script>