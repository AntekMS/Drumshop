<?php
/**
 * Admin Bestellung E-Mail View
 *
 * @package DrumShop
 */
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">E-Mail an Kunden: Bestellung #<?= $bestellung['id'] ?></h1>
    <div class="btn-toolbar">
        <a href="<?= base_url('admin/bestellungen/detail/' . $bestellung['id']) ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Zurück zur Bestellung
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Kundeninformationen</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= $bestellung['kunde_name'] ?></p>
                <p><strong>E-Mail:</strong> <a href="mailto:<?= $bestellung['kunde_email'] ?>"><?= $bestellung['kunde_email'] ?></a></p>
                <p><strong>Bestelldatum:</strong> <?= date('d.m.Y H:i', strtotime($bestellung['erstellt_am'])) ?> Uhr</p>
                <p><strong>Status:</strong>
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
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">E-Mail verfassen</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/bestellungen/emailSenden/' . $bestellung['id']) ?>" method="post">
                    <div class="mb-3">
                        <label for="betreff" class="form-label">Betreff</label>
                        <input type="text" class="form-control" id="betreff" name="betreff" required
                               value="Ihre Bestellung #<?= $bestellung['id'] ?> bei DrumShop">
                    </div>

                    <div class="mb-3">
                        <label for="nachricht" class="form-label">Nachricht</label>
                        <textarea class="form-control" id="nachricht" name="nachricht" rows="10" required><?php
                            echo "Sehr geehrte(r) " . $bestellung['kunde_name'] . ",\n\n";
                            echo "vielen Dank für Ihre Bestellung #" . $bestellung['id'] . " bei DrumShop.\n\n";

                            switch ($bestellung['status']) {
                                case 'neu':
                                    echo "Wir haben Ihre Bestellung erhalten und werden sie in Kürze bearbeiten.";
                                    break;
                                case 'bearbeitet':
                                    echo "Ihre Bestellung wird aktuell bearbeitet und für den Versand vorbereitet.";
                                    break;
                                case 'versandt':
                                    echo "Ihre Bestellung wurde an Sie versandt.";
                                    if (!empty($bestellung['sendungsnummer'])) {
                                        echo "\nSendungsnummer: " . $bestellung['sendungsnummer'];
                                    }
                                    break;
                                case 'geliefert':
                                    echo "Ihre Bestellung wurde geliefert. Wir hoffen, Sie sind mit Ihren Produkten zufrieden.";
                                    break;
                                case 'storniert':
                                    echo "Ihre Bestellung wurde storniert.";
                                    break;
                            }

                            echo "\n\nBestellübersicht:";
                            echo "\n------------------------------------------";
                            foreach ($positionen as $position) {
                                echo "\n" . $position['menge'] . "x " . $position['produkt_name'] . ": " . number_format($position['zwischensumme'], 2, ',', '.') . " €";
                            }
                            echo "\n------------------------------------------";
                            echo "\nGesamtbetrag: " . number_format($bestellung['gesamtpreis'], 2, ',', '.') . " €";
                            echo "\n\nBei Fragen zu Ihrer Bestellung antworten Sie einfach auf diese E-Mail.";
                            echo "\n\nMit freundlichen Grüßen\nIhr DrumShop-Team";
                            ?></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('admin/bestellungen/detail/' . $bestellung['id']) ?>" class="btn btn-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">E-Mail senden</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">E-Mail-Vorlagen</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-primary w-100 email-template" data-template="bestaetigung">
                    Bestellbestätigung
                </button>
            </div>
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-primary w-100 email-template" data-template="versand">
                    Versandbestätigung
                </button>
            </div>
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-primary w-100 email-template" data-template="rueckfrage">
                    Rückfrage zur Bestellung
                </button>
            </div>
            <div class="col-md-3 mb-2">
                <button type="button" class="btn btn-outline-primary w-100 email-template" data-template="stornierung">
                    Stornierungsbestätigung
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // E-Mail-Vorlagen
        const vorlagen = {
            bestaetigung: {
                betreff: "Bestätigung Ihrer Bestellung #<?= $bestellung['id'] ?> bei DrumShop",
                nachricht: "Sehr geehrte(r) <?= $bestellung['kunde_name'] ?>,\n\n" +
                    "vielen Dank für Ihre Bestellung #<?= $bestellung['id'] ?> bei DrumShop.\n\n" +
                    "Wir haben Ihre Bestellung erhalten und werden Sie schnellstmöglich bearbeiten.\n\n" +
                    "Bestellübersicht:\n" +
                    "------------------------------------------\n" +
                    <?php
                    $positionen_text = "";
                    foreach ($positionen as $position) {
                        $positionen_text .= $position['menge'] . "x " . $position['produkt_name'] . ": " . number_format($position['zwischensumme'], 2, ',', '.') . " €\n";
                    }
                    echo json_encode($positionen_text);
                    ?> +
                        "------------------------------------------\n" +
                    "Gesamtbetrag: <?= number_format($bestellung['gesamtpreis'], 2, ',', '.') ?> €\n\n" +
                    "Sie können den Status Ihrer Bestellung jederzeit auf unserer Website verfolgen.\n\n" +
                    "Bei Fragen zu Ihrer Bestellung antworten Sie einfach auf diese E-Mail.\n\n" +
                    "Mit freundlichen Grüßen\n" +
                    "Ihr DrumShop-Team"
            },
            versand: {
                betreff: "Ihre Bestellung #<?= $bestellung['id'] ?> wurde versandt",
                nachricht: "Sehr geehrte(r) <?= $bestellung['kunde_name'] ?>,\n\n" +
                    "wir haben Ihre Bestellung #<?= $bestellung['id'] ?> heute an Sie versandt.\n\n" +
                    <?= !empty($bestellung['sendungsnummer']) ? '"Sendungsnummer: ' . $bestellung['sendungsnummer'] . '\n\n"' : '""' ?> +
                    "Bestellübersicht:\n" +
                    "------------------------------------------\n" +
                    <?= json_encode($positionen_text) ?> +
                    "------------------------------------------\n" +
                    "Gesamtbetrag: <?= number_format($bestellung['gesamtpreis'], 2, ',', '.') ?> €\n\n" +
                    "Wir wünschen Ihnen viel Freude mit Ihren neuen Produkten!\n\n" +
                    "Mit freundlichen Grüßen\n" +
                    "Ihr DrumShop-Team"
            },
            rueckfrage: {
                betreff: "Rückfrage zu Ihrer Bestellung #<?= $bestellung['id'] ?>",
                nachricht: "Sehr geehrte(r) <?= $bestellung['kunde_name'] ?>,\n\n" +
                    "vielen Dank für Ihre Bestellung #<?= $bestellung['id'] ?> bei DrumShop.\n\n" +
                    "Bezüglich Ihrer Bestellung haben wir eine kurze Rückfrage:\n" +
                    "[Hier Ihre Frage einfügen]\n\n" +
                    "Wir freuen uns auf Ihre Antwort, damit wir Ihre Bestellung schnellstmöglich bearbeiten können.\n\n" +
                    "Mit freundlichen Grüßen\n" +
                    "Ihr DrumShop-Team"
            },
            stornierung: {
                betreff: "Ihre Bestellung #<?= $bestellung['id'] ?> wurde storniert",
                nachricht: "Sehr geehrte(r) <?= $bestellung['kunde_name'] ?>,\n\n" +
                    "Ihre Bestellung #<?= $bestellung['id'] ?> wurde storniert.\n\n" +
                    <?= $bestellung['zahlungsstatus'] == 'bezahlt' ? '"Der Kaufbetrag wird in Kürze auf Ihrem Konto gutgeschrieben.\n\n"' : '""' ?> +
                    "Bei Fragen zu dieser Stornierung kontaktieren Sie uns bitte unter info@drumshop.de\n\n" +
                    "Mit freundlichen Grüßen\n" +
                    "Ihr DrumShop-Team"
            }
        };

        // Event-Handler für Vorlagen-Buttons
        document.querySelectorAll('.email-template').forEach(button => {
            button.addEventListener('click', function() {
                const templateName = this.getAttribute('data-template');
                const template = vorlagen[templateName];

                if (template) {
                    document.getElementById('betreff').value = template.betreff;
                    document.getElementById('nachricht').value = template.nachricht;
                }
            });
        });
    });
</script>