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
                            <input type="text" class="form-control" id="kunde_name" name="kunde_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="kunde_email" class="form-label">E-Mail*</label>
                            <input type="email" class="form-control" id="kunde_email" name="kunde_email" required>
                        </div>

                        <div class="mb-3">
                            <label for="lieferadresse" class="form-label">Lieferadresse*</label>
                            <textarea class="form-control" id="lieferadresse" name="lieferadresse" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="zahlungsmethode" class="form-label">Zahlungsmethode*</label>
                            <select class="form-select" id="zahlungsmethode" name="zahlungsmethode" required>
                                <option value="">Bitte wählen</option>
                                <option value="paypal">PayPal</option>
                                <option value="kreditkarte">Kreditkarte</option>
                                <option value="rechnung">Rechnung</option>
                                <option value="vorkasse">Vorkasse</option>
                            </select>
                        </div>

                        <div id="kreditkartenDaten" class="d-none">
                            <div class="mb-3">
                                <label for="kreditkarte_nummer" class="form-label">Kreditkartennummer*</label>
                                <input type="text" class="form-control" id="kreditkarte_nummer" name="kreditkarte_nummer">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="kreditkarte_ablauf" class="form-label">Gültig bis*</label>
                                    <input type="text" class="form-control" id="kreditkarte_ablauf" name="kreditkarte_ablauf" placeholder="MM/JJ">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="kreditkarte_cvv" class="form-label">CVV*</label>
                                    <input type="text" class="form-control" id="kreditkarte_cvv" name="kreditkarte_cvv">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="anmerkungen" class="form-label">Anmerkungen</label>
                            <textarea class="form-control" id="anmerkungen" name="anmerkungen" rows="3"></textarea>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agb" name="agb" required>
                            <label class="form-check-label" for="agb">Ich habe die AGB gelesen und akzeptiere diese*</label>
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
                    <h5 class="mb-0">Versandinfo</h5>
                </div>
                <div class="card-body">
                    <p>Alle Preise inkl. MwSt. zzgl. Versandkosten.</p>
                    <p>Lieferzeit: 2-4 Werktage</p>
                    <p>Ab 100 € versandkostenfrei innerhalb Deutschlands.</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const zahlungsmethode = document.getElementById('zahlungsmethode');
        const kreditkartenDaten = document.getElementById('kreditkartenDaten');

        zahlungsmethode.addEventListener('change', function() {
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
</script>