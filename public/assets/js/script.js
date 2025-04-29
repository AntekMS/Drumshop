/**
 * DrumShop - Hauptskript für Frontend
 * Speicherort: public/assets/js/script.js
 */

document.addEventListener('DOMContentLoaded', function() {

    // Initialisiere Bootstrap-Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialisiere Bootstrap-Popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Produktbilder-Hover-Effekt
    const productCards = document.querySelectorAll('.card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.querySelector('.card-img-top')?.classList.add('animated');
        });
        card.addEventListener('mouseleave', function() {
            this.querySelector('.card-img-top')?.classList.remove('animated');
        });
    });

    // Warenkorb-Menge anpassen
    const quantityInputs = document.querySelectorAll('input[name^="positionen"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (parseInt(this.value) < 0) {
                this.value = 0;
            }
        });
    });

    // Produktfilter-Toggle für mobile Ansicht
    const filterToggleBtn = document.getElementById('filterToggle');
    const filterContainer = document.getElementById('filterContainer');

    if (filterToggleBtn && filterContainer) {
        filterToggleBtn.addEventListener('click', function() {
            filterContainer.classList.toggle('d-none');
            this.textContent = filterContainer.classList.contains('d-none')
                ? 'Filter anzeigen'
                : 'Filter ausblenden';
        });
    }

    // Checkout-Formular: Zahlungsmethode
    const zahlungsmethode = document.getElementById('zahlungsmethode');
    const kreditkartenDaten = document.getElementById('kreditkartenDaten');

    if (zahlungsmethode && kreditkartenDaten) {
        zahlungsmethode.addEventListener('change', function() {
            if (this.value === 'kreditkarte') {
                kreditkartenDaten.classList.remove('d-none');
                document.getElementById('kreditkarte_nummer')?.setAttribute('required', 'required');
                document.getElementById('kreditkarte_ablauf')?.setAttribute('required', 'required');
                document.getElementById('kreditkarte_cvv')?.setAttribute('required', 'required');
            } else {
                kreditkartenDaten.classList.add('d-none');
                document.getElementById('kreditkarte_nummer')?.removeAttribute('required');
                document.getElementById('kreditkarte_ablauf')?.removeAttribute('required');
                document.getElementById('kreditkarte_cvv')?.removeAttribute('required');
            }
        });
    }

    // Checkout-Formular: AGB-Checkbox
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

    // Produktbild-Galerie auf Detailseite
    const mainImage = document.getElementById('mainProductImage');
    const thumbnails = document.querySelectorAll('.product-thumbnail');

    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Aktiven Thumbnail markieren
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Hauptbild austauschen
                const newSrc = this.getAttribute('data-img-src');
                if (newSrc) {
                    mainImage.src = newSrc;
                    mainImage.classList.add('fade');
                    setTimeout(() => {
                        mainImage.classList.remove('fade');
                    }, 300);
                }
            });
        });
    }

    // Lazy Loading für Bilder
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    if ('loading' in HTMLImageElement.prototype) {
        // Browser unterstützt lazy loading nativ
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
        });
    } else {
        // Fallback für Browser ohne natives lazy loading
        const lazyImageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const lazyImage = entry.target;
                    lazyImage.src = lazyImage.dataset.src;
                    lazyImageObserver.unobserve(lazyImage);
                }
            });
        });

        lazyImages.forEach(image => {
            lazyImageObserver.observe(image);
        });
    }

    // Countdown für begrenzte Angebote
    const countdownElements = document.querySelectorAll('.offer-countdown');
    countdownElements.forEach(el => {
        const endTime = new Date(el.dataset.endtime).getTime();

        const countdownInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                clearInterval(countdownInterval);
                el.innerHTML = "Angebot abgelaufen";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            el.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }, 1000);
    });

    // Newsletter-Anmeldung
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            if (!email) {
                alert('Bitte geben Sie eine E-Mail-Adresse ein.');
                return;
            }

            // Hier würde normalerweise ein AJAX-Call an den Server folgen
            // Für Demo-Zwecke zeigen wir nur eine Erfolgsmeldung
            const successMsg = document.createElement('div');
            successMsg.className = 'alert alert-success mt-3';
            successMsg.textContent = 'Vielen Dank für Ihre Anmeldung!';

            this.insertAdjacentElement('afterend', successMsg);
            this.reset();

            setTimeout(() => {
                successMsg.remove();
            }, 5000);
        });
    }
});