<?php
/**
 * Footer Template
 *
 * @package DrumShop
 */
?>
</main>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>DrumShop</h5>
                <p>Ihr Spezialist für Schlagzeug und Percussion.</p>
                <a href="<?= base_url('ueber-uns') ?>" class="text-white">Mehr über uns erfahren</a>
            </div>
            <div class="col-md-4">
                <h5>Kontakt</h5>
                <address>
                    Bildungscampus 4<br>
                    74076 Heilbronn<br>
                    <a href="mailto:info@drumshop.de" class="text-white">info@drumshop.de</a><br>
                    <a href="tel:++49 7131 1237 0" class="text-white">+49 7131 1237 0</a>
                </address>
            </div>
            <div class="col-md-4">
                <h5>Rechtliches</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= base_url('impressum') ?>" class="text-white">Impressum</a></li>
                    <li><a href="<?= base_url('agb') ?>" class="text-white">AGB</a></li>
                    <li><a href="<?= base_url('datenschutz') ?>" class="text-white">Datenschutz</a></li>
                </ul>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> DrumShop. Alle Rechte vorbehalten.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>