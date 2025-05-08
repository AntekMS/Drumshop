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
<!-- Am Ende Ihrer Seite, vor dem schließenden </body>-Tag -->
<div id="cookie-banner" style="position: fixed; bottom: 0; left: 0; right: 0; background-color: rgba(0, 0, 0, 0.85); color: white; padding: 20px; z-index: 9999; display: none; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
        <p style="margin: 0; flex: 1; min-width: 300px;">Diese Website verwendet Cookies, um Ihnen die bestmögliche Erfahrung auf unserer Website zu bieten. Durch die weitere Nutzung der Website stimmen Sie der Verwendung von Cookies zu.</p>
        <button id="accept-cookies" style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">Cookies akzeptieren</button>
    </div>
</div>

<script>
    // Sofort ausgeführte Funktion
    (function() {
        // Funktion zum Setzen eines Cookies
        function setCookie(name, value, days) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        // Funktion zum Abrufen eines Cookies
        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        // Das Banner-Element
        const cookieBanner = document.getElementById('cookie-banner');
        const acceptCookiesButton = document.getElementById('accept-cookies');

        // Prüfen, ob der Benutzer bereits zugestimmt hat
        if (!getCookie('cookiesAccepted')) {
            // Wenn nicht, das Banner anzeigen
            cookieBanner.style.display = 'block';
            console.log("Cookie-Banner wird angezeigt");
        } else {
            console.log("Cookie wurde bereits akzeptiert");
        }

        // Event-Listener für den Accept-Button
        acceptCookiesButton.addEventListener('click', function() {
            // Cookie setzen (gültig für 365 Tage)
            setCookie('cookiesAccepted', 'true', 365);

            // Banner ausblenden
            cookieBanner.style.display = 'none';

            console.log('Cookies wurden akzeptiert!');
        });
    })(); // Sofort ausführen
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>


</body>
</html>