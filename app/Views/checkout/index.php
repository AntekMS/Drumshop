<?php
/**
 * Checkout View
 *
 * @package DrumShop
 */
?>
<h1 class="my-4">Checkout</h1>

<?php if (empty($positionen)) : ?>
    <div class="alert alert-info">
        Ihr Warenkorb ist leer. <a href="<?= base_url('produkte') ?>" class="alert-link">Stöbern Sie in unseren Produkten</a>, bevor Sie zur Kasse gehen.
    </div>
<?php else : ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Bestellinformationen</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('checkout/bestellen') ?>" method="post" id="checkoutForm">
                        <div class="mb-3">
                            <label for="kunde_name" class="form-label">Name*</label>
                            <input type="text" class="form-control" id="kunde_name" name="kunde_name" required value="<?= old('kunde_name') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="kunde_email" class="form-label">E-Mail*</label>
                            <input type="email" class="form-control" id="kunde_email" name="kunde_email" required value="<?= old('kunde_email') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="lieferadresse" class="form-label">Lieferadresse*</label>
                            <textarea class="form-control" id="lieferadresse" name="lieferadresse" rows="3" required><?= old('lieferadresse') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="zahlungsmethode" class="form-label">Zahlungsmethode*</label>

                            <div class="payment-methods">
                                <!-- PayPal Option -->
                                <div class="form-check payment-method">
                                    <input class="form-check-input" type="radio" name="zahlungsmethode" id="zahlungPayPal" value="paypal" checked>
                                    <label class="form-check-label d-flex align-items-center" for="zahlungPayPal">
                                        <span class="paypal-logo">
                                            <span class="paypal-blue">Pay</span><span class="paypal-light-blue">Pal</span>
                                        </span>
                                        <span class="ms-2 badge bg-success">Empfohlen</span>
                                    </label>
                                    <div class="payment-description mt-1 ms-4 text-muted small">
                                        Bezahlen Sie sicher und schnell mit Ihrem PayPal-Konto oder Ihrer Kreditkarte über PayPal.
                                    </div>
                                </div>

                                <!-- Kreditkarte Option -->
                                <div class="form-check payment-method mt-3">
                                    <input class="form-check-input" type="radio" name="zahlungsmethode" id="zahlungKreditkarte" value="kreditkarte">
                                    <label class="form-check-label d-flex align-items-center" for="zahlungKreditkarte">
                                        <span class="me-2"><i class="fas fa-credit-card fa-lg" style="color: #6c757d;"></i> Kreditkarte</span>
                                    </label>
                                </div>

                                <!-- Kreditkarten-Felder (werden dynamisch angezeigt) -->
                                <div id="kreditkartenDaten" class="mt-2 mb-3 ms-4 p-3 border rounded d-none">
                                    <div class="mb-3">
                                        <label for="kreditkarte_nummer" class="form-label">Kreditkartennummer*</label>
                                        <input type="text" class="form-control" id="kreditkarte_nummer" name="kreditkarte_nummer" placeholder="1234 5678 9012 3456">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="kreditkarte_ablauf" class="form-label">Gültig bis*</label>
                                            <input type="text" class="form-control" id="kreditkarte_ablauf" name="kreditkarte_ablauf" placeholder="MM/JJ">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="kreditkarte_cvv" class="form-label">CVV*</label>
                                            <input type="text" class="form-control" id="kreditkarte_cvv" name="kreditkarte_cvv" placeholder="123">
                                        </div>
                                    </div>
                                </div>

                                <!-- Rechnung Option -->
                                <div class="form-check payment-method mt-3">
                                    <input class="form-check-input" type="radio" name="zahlungsmethode" id="zahlungRechnung" value="rechnung">
                                    <label class="form-check-label" for="zahlungRechnung">
                                        <span class="me-2"><i class="fas fa-file-invoice fa-lg" style="color: #6c757d;"></i> Rechnung</span>
                                    </label>
                                    <div class="payment-description mt-1 ms-4 text-muted small">
                                        Sie erhalten eine Rechnung mit Ihrer Bestellung und haben 14 Tage Zeit zur Überweisung.
                                    </div>
                                </div>

                                <!-- Vorkasse Option -->
                                <div class="form-check payment-method mt-3">
                                    <input class="form-check-input" type="radio" name="zahlungsmethode" id="zahlungVorkasse" value="vorkasse">
                                    <label class="form-check-label" for="zahlungVorkasse">
                                        <span class="me-2"><i class="fas fa-university fa-lg" style="color: #6c757d;"></i> Vorkasse</span>
                                    </label>
                                    <div class="payment-description mt-1 ms-4 text-muted small">
                                        Wir versenden die Ware nach Zahlungseingang. Sie erhalten die Kontodaten per E-Mail.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="anmerkungen" class="form-label">Anmerkungen</label>
                            <textarea class="form-control" id="anmerkungen" name="anmerkungen" rows="3"><?= old('anmerkungen') ?></textarea>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agb" name="agb" required>
                            <label class="form-check-label" for="agb">Ich habe die <a href="#" target="_blank">AGB</a> gelesen und akzeptiere diese*</label>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mt-3">
                            <i class="fas fa-check"></i> Jetzt kostenpflichtig bestellen
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Bestellübersicht</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($positionen as $position) : ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary rounded-pill me-2"><?= $position['menge'] ?>x</span>
                                    <?= $position['produkt_name'] ?>
                                </div>
                                <span><?= number_format($position['preis'] * $position['menge'], 2, ',', '.') ?> €</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <strong>Gesamtsumme:</strong>
                        <strong><?= number_format($gesamtpreis, 2, ',', '.') ?> €</strong>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Sicher bezahlen</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="paypal-logo-lg mb-2">
                            <span class="paypal-blue">Pay</span><span class="paypal-light-blue">Pal</span>
                        </div>
                        <p class="small">Bezahlen Sie schnell und sicher mit PayPal - Ihr Käuferschutz ist inklusive!</p>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1"><i class="fas fa-lock text-success"></i> <strong>Sichere Datenübertragung</strong></p>
                        <p class="small mb-0">Ihre Daten werden verschlüsselt übertragen.</p>
                    </div>

                    <hr>

                    <p class="mb-0"><i class="fas fa-truck"></i> Versand innerhalb von 1-3 Werktagen</p>
                    <p class="mb-0"><i class="fas fa-euro-sign"></i> Alle Preise inkl. MwSt.</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    /* Allgemeine Stile für Zahlungsmethoden */
    .payment-method {
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }

    .payment-method:hover, .payment-method:has(.form-check-input:checked) {
        background-color: #f8f9fa;
        border-color: #0d6efd;
    }

    .form-check-input:checked ~ .form-check-label {
        font-weight: 600;
    }

    /* PayPal-Logo Stile */
    .paypal-logo {
        font-family: Arial, sans-serif;
        font-weight: bold;
        font-size: 1.5rem;
        line-height: 1;
    }

    .paypal-logo-lg {
        font-family: Arial, sans-serif;
        font-weight: bold;
        font-size: 2rem;
        line-height: 1;
    }

    .paypal-blue {
        color: #003087;
    }

    .paypal-light-blue {
        color: #009cde;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const zahlungsmethodeInputs = document.querySelectorAll('input[name="zahlungsmethode"]');
        const kreditkartenDaten = document.getElementById('kreditkartenDaten');

        zahlungsmethodeInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                if (this.value === 'kreditkarte') {
                    kreditkartenDaten.classList.remove('d-none');
                    document.getElementById('kreditkarte_nummer').setAttribute('required', 'required');
                    document.getElementById('kreditkarte_ablauf').setAttribute('required', 'required');
                    document.getElementById('kreditkarte_cvv').setAttribute('required', 'required');
                } else {
                    kreditkartenDaten.classList.add('d-none');
                    document.getElementById('kreditkarte_nummer').removeAttribute('required');
                    document.getElementById('kreditkarte_ablauf').removeAttribute('required');
                    document.getElementById('kreditkarte_cvv').removeAttribute('required');
                }
            });
        });

        // Überprüfen der AGB-Checkbox bei Formularabsendung
        const checkoutForm = document.getElementById('checkoutForm');
        const agbCheckbox = document.getElementById('agb');

        if (checkoutForm && agbCheckbox) {
            checkoutForm.addEventListener('submit', function(e) {
                if (!agbCheckbox.checked) {
                    e.preventDefault();
                    alert('Bitte akzeptieren Sie die AGB, um fortzufahren.');
                    return false;
                }
            });
        }
    });
</script>