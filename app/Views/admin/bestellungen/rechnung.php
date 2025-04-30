<?php
/**
 * Admin Bestellung Rechnung View
 *
 * @package DrumShop
 */
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom no-print">
    <h1 class="h2">Rechnung: Bestellung #<?= $bestellung['id'] ?></h1>
    <div class="btn-toolbar">
        <a href="<?= base_url('admin/bestellungen/detail/' . $bestellung['id']) ?>" class="btn btn-sm btn-outline-secondary me-2">
            <i class="fas fa-arrow-left"></i> Zurück zur Bestellung
        </a>
        <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-print"></i> Drucken
        </button>
    </div>
</div>

<!-- Separate Druckversion -->
<div class="print-only" style="display: none;">
    <div class="print-page">
        <!-- Logo und Firmeninformationen -->
        <div class="print-header">
            <table width="100%">
                <tr>
                    <td width="50%">
                        <div style="font-weight: bold; font-size: 20px; margin-bottom: 5px;">DrumShop</div>
                        <div>Musterstraße 123<br>12345 Musterstadt<br>Deutschland</div>
                        <div style="margin-top: 10px;">
                            Tel: +49 123 45678-0<br>
                            E-Mail: info@drumshop.de<br>
                            Web: www.drumshop.de
                        </div>
                    </td>
                    <td width="50%" style="text-align: right;">
                        <div style="font-weight: bold; font-size: 20px; margin-bottom: 5px;">RECHNUNG</div>
                        <div><strong>Rechnungsnummer:</strong> <?= $rechnungsnummer ?></div>
                        <div><strong>Rechnungsdatum:</strong> <?= $rechnungsdatum ?></div>
                        <div><strong>Bestellnummer:</strong> <?= $bestellung['id'] ?></div>
                        <div><strong>Bestelldatum:</strong> <?= date('d.m.Y', strtotime($bestellung['erstellt_am'])) ?></div>
                    </td>
                </tr>
            </table>
        </div>

        <div style="border-top: 1px solid #ccc; margin: 20px 0;"></div>

        <!-- Kundeninformationen -->
        <table width="100%">
            <tr>
                <td width="50%" style="vertical-align: top;">
                    <div style="font-weight: bold; margin-bottom: 5px;">Rechnungsadresse</div>
                    <div><?= $bestellung['kunde_name'] ?><br><?= nl2br($bestellung['lieferadresse']) ?></div>
                </td>
                <td width="50%" style="vertical-align: top;">
                    <div style="font-weight: bold; margin-bottom: 5px;">Lieferadresse</div>
                    <div><?= $bestellung['kunde_name'] ?><br><?= nl2br($bestellung['lieferadresse']) ?></div>
                </td>
            </tr>
        </table>

        <div style="margin-top: 20px;">
            <div style="font-weight: bold; margin-bottom: 5px;">Zahlungsinformationen</div>
            <div><strong>Zahlungsmethode:</strong> <?= $bestellung['zahlungsmethode'] ?></div>
            <div><strong>Zahlungsstatus:</strong> <?= $bestellung['zahlungsstatus'] ?></div>
        </div>

        <!-- Artikel Tabelle -->
        <div style="margin-top: 20px;">
            <table width="100%" style="border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Artikelnr.</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Beschreibung</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Menge</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Einzelpreis</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Summe</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($positionen as $position) : ?>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?= $position['produkt_id'] ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?= $position['produkt_name'] ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?= $position['menge'] ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><?= number_format($position['einzelpreis'], 2, ',', '.') ?> €</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><?= number_format($position['zwischensumme'], 2, ',', '.') ?> €</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>Zwischensumme:</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><?= number_format($bestellung['gesamtpreis'] / 1.19, 2, ',', '.') ?> €</td>
                </tr>
                <tr>
                    <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>MwSt. (19%):</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><?= number_format($bestellung['gesamtpreis'] - ($bestellung['gesamtpreis'] / 1.19), 2, ',', '.') ?> €</td>
                </tr>
                <tr>
                    <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>Gesamtbetrag:</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong><?= number_format($bestellung['gesamtpreis'], 2, ',', '.') ?> €</strong></td>
                </tr>
                </tfoot>
            </table>
        </div>

        <!-- Fußzeile -->
        <div style="margin-top: 30px;">
            <div>Wir danken Ihnen für Ihren Einkauf bei DrumShop.</div>
            <div style="margin-bottom: 15px;">Bei Fragen zu Ihrer Bestellung kontaktieren Sie uns bitte unter info@drumshop.de.</div>

            <div style="margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 12px; color: #666;">
                <div>DrumShop GmbH | Musterstraße 123 | 12345 Musterstadt | Steuernummer: 123/456/78910 | USt-IdNr.: DE123456789</div>
                <div>Amtsgericht Musterstadt HRB 12345 | Geschäftsführer: Max Mustermann</div>
                <div>Bankverbindung: Musterbank | IBAN: DE12 3456 7890 1234 5678 90 | BIC: MUSTEDEXXX</div>
            </div>
        </div>
    </div>
</div>

<!-- Bildschirmansicht (wird nicht gedruckt) -->
<div class="screen-only">
    <div id="rechnung" class="border p-4 mb-4 bg-white rechnung-container">
        <!-- Logo und Firmeninformationen -->
        <div class="row mb-4">
            <div class="col-6">
                <div class="fw-bold fs-4 mb-1">DrumShop</div>
                <address class="mb-0">
                    Musterstraße 123<br>
                    12345 Musterstadt<br>
                    Deutschland<br><br>
                    Tel: +49 123 45678-0<br>
                    E-Mail: info@drumshop.de<br>
                    Web: www.drumshop.de
                </address>
            </div>
            <div class="col-6 text-end">
                <h1 class="fw-bold fs-4 mb-1">RECHNUNG</h1>
                <p class="mb-1"><strong>Rechnungsnummer:</strong> <?= $rechnungsnummer ?></p>
                <p class="mb-1"><strong>Rechnungsdatum:</strong> <?= $rechnungsdatum ?></p>
                <p class="mb-1"><strong>Bestellnummer:</strong> <?= $bestellung['id'] ?></p>
                <p><strong>Bestelldatum:</strong> <?= date('d.m.Y', strtotime($bestellung['erstellt_am'])) ?></p>
            </div>
        </div>

        <hr class="my-4">

        <!-- Kundeninformationen -->
        <div class="row">
            <div class="col-6">
                <h5 class="mb-2">Rechnungsadresse</h5>
                <address>
                    <?= $bestellung['kunde_name'] ?><br>
                    <?= nl2br($bestellung['lieferadresse']) ?>
                </address>
            </div>
            <div class="col-6">
                <h5 class="mb-2">Lieferadresse</h5>
                <address>
                    <?= $bestellung['kunde_name'] ?><br>
                    <?= nl2br($bestellung['lieferadresse']) ?>
                </address>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h5 class="mb-2">Zahlungsinformationen</h5>
                <p class="mb-0"><strong>Zahlungsmethode:</strong> <?= $bestellung['zahlungsmethode'] ?></p>
                <p><strong>Zahlungsstatus:</strong> <?= $bestellung['zahlungsstatus'] ?></p>
            </div>
        </div>

        <!-- Artikel Tabelle -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                        <tr>
                            <th>Artikelnr.</th>
                            <th>Beschreibung</th>
                            <th class="text-center">Menge</th>
                            <th class="text-end">Einzelpreis</th>
                            <th class="text-end">Summe</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($positionen as $position) : ?>
                            <tr>
                                <td><?= $position['produkt_id'] ?></td>
                                <td><?= $position['produkt_name'] ?></td>
                                <td class="text-center"><?= $position['menge'] ?></td>
                                <td class="text-end"><?= number_format($position['einzelpreis'], 2, ',', '.') ?> €</td>
                                <td class="text-end"><?= number_format($position['zwischensumme'], 2, ',', '.') ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Zwischensumme:</strong></td>
                            <td class="text-end"><?= number_format($bestellung['gesamtpreis'] / 1.19, 2, ',', '.') ?> €</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end"><strong>MwSt. (19%):</strong></td>
                            <td class="text-end"><?= number_format($bestellung['gesamtpreis'] - ($bestellung['gesamtpreis'] / 1.19), 2, ',', '.') ?> €</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Gesamtbetrag:</strong></td>
                            <td class="text-end"><strong><?= number_format($bestellung['gesamtpreis'], 2, ',', '.') ?> €</strong></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Fußzeile -->
        <div class="row mt-5">
            <div class="col-12">
                <p class="mb-1">Wir danken Ihnen für Ihren Einkauf bei DrumShop.</p>
                <p class="mb-3">Bei Fragen zu Ihrer Bestellung kontaktieren Sie uns bitte unter <a href="mailto:info@drumshop.de">info@drumshop.de</a>.</p>

                <div class="mt-5">
                    <hr class="mb-0">
                    <div class="row text-muted small mt-2">
                        <div class="col-md-6">
                            <p class="mb-0">DrumShop GmbH | Musterstraße 123 | 12345 Musterstadt</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0">Steuernummer: 123/456/78910 | USt-IdNr.: DE123456789</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0">Amtsgericht Musterstadt HRB 12345 | Geschäftsführer: Max Mustermann</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0">Bankverbindung: Musterbank | IBAN: DE12 3456 7890 1234 5678 90 | BIC: MUSTEDEXXX</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Allgemeine Stile */
    .rechnung-container {
        font-family: Arial, sans-serif;
        font-size: 14px;
        line-height: 1.5;
        color: #333;
    }

    /* Druckspezifische Stile */
    @media screen {
        .print-only {
            display: none !important;
        }
    }

    @media print {
        body {
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .no-print, .screen-only {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }

        .print-page {
            width: 210mm;
            padding: 10mm;
            margin: 0 auto;
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
        }

        /* Seitenumbrüche verhindern */
        .page-break-inside-avoid {
            page-break-inside: avoid;
        }

        /* Seitenformat */
        @page {
            size: A4;
            margin: 0;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const printButton = document.querySelector('button[onclick="window.print()"]');
        if (printButton) {
            printButton.addEventListener('click', function() {
                // Kurze Verzögerung, um sicherzustellen, dass die Druckansicht vollständig geladen ist
                setTimeout(function() {
                    window.print();
                }, 200);
            });
        }
    });
</script>