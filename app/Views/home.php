<?php
/**
 * Home Page - Überarbeitete Version
 *
 * @package DrumShop
 */

// Überprüfen, ob die Variablen existieren, ansonsten leere Arrays verwenden
$hervorgehobeneProdukte = isset($hervorgehobeneProdukte) ? $hervorgehobeneProdukte : [];
$kategorien = isset($kategorien) ? $kategorien : [];
$neueProdukte = isset($neueProdukte) ? $neueProdukte : [];
?>
<!-- Hero-Bereich mit Video-Hintergrund -->
<div class="hero-section position-relative overflow-hidden mb-5">
    <div class="hero-background">
        <video autoplay muted loop class="hero-video">
            <source src="<?= base_url('assets/videos/drums-background.mp4') ?>" type="video/mp4">
            <!-- Fallback-Bild für Browser, die kein Video unterstützen -->
            <img src="<?= base_url('assets/images/no-image.jpg') ?>" alt="Drums Background">
        </video>
        <div class="video-overlay"></div>
    </div>
    <div class="container hero-content text-center text-white py-5">
        <h1 class="display-3 fw-bold mb-3 text-shadow">Willkommen im DrumShop</h1>
        <p class="lead fs-4 mb-4 text-shadow">Ihr Spezialist für hochwertige Schlagzeuge und Percussion</p>
        <div class="hero-buttons">
            <a class="btn btn-primary btn-lg me-2 shadow" href="<?= base_url('produkte') ?>">
                <i class="fas fa-drum me-2"></i>Produkte entdecken
            </a>
            <a class="btn btn-outline-light btn-lg shadow" href="<?= base_url('ueber-uns') ?>">
                <i class="fas fa-info-circle me-2"></i>Über uns
            </a>
        </div>
    </div>
    <div class="hero-wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 200">
            <path fill="#ffffff" fill-opacity="1" d="M0,192L60,170.7C120,149,240,107,360,112C480,117,600,171,720,176C840,181,960,139,1080,117.3C1200,96,1320,96,1380,96L1440,96L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
        </svg>
    </div>
</div>

<!-- Angebots-Vorteile -->
<section class="advantages py-3 mb-5">
    <div class="container">
        <div class="row g-4 text-center">
            <?php
            // Definieren der Vorteile einmalig
            $advantages = [
                [
                    'icon' => 'fa-truck',
                    'title' => 'Kostenfreier Versand',
                    'text' => 'ab 50€ Bestellwert innerhalb Deutschlands'
                ],
                [
                    'icon' => 'fa-undo-alt',
                    'title' => '30 Tage Rückgaberecht',
                    'text' => 'Problemlose Rücksendung, wenn Sie nicht zufrieden sind'
                ],
                [
                    'icon' => 'fa-headset',
                    'title' => 'Fachberatung',
                    'text' => 'Erfahrene Drummer stehen Ihnen jederzeit zur Verfügung'
                ],
                [
                    'icon' => 'fa-shield-alt',
                    'title' => 'Sicheres Einkaufen',
                    'text' => 'Sichere Bezahlung und SSL-verschlüsselter Shop'
                ]
            ];

            // Schleife durch die Vorteile
            foreach ($advantages as $advantage): ?>
                <div class="col-md-3">
                    <div class="advantage-card p-3 h-100 rounded shadow-sm">
                        <i class="fas <?= $advantage['icon'] ?> fs-2 text-primary mb-3"></i>
                        <h5><?= $advantage['title'] ?></h5>
                        <p class="mb-0 text-muted small"><?= $advantage['text'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Hervorgehobene Produkte -->
<?php if (!empty($hervorgehobeneProdukte)): ?>
    <section class="featured-products mb-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-6 fw-bold position-relative d-inline-block pb-3 border-bottom border-primary">Ausgewählte Produkte</h2>
                <p class="text-muted mt-3">Entdecken Sie unsere Bestseller und aktuellen Empfehlungen</p>
            </div>

            <div class="row g-4">
                <?php foreach ($hervorgehobeneProdukte as $produkt) : ?>
                    <div class="col-md-3">
                        <div class="product-card card h-100 shadow-sm border-0 position-relative">
                            <?php if ($produkt['bestand'] <= 5 && $produkt['bestand'] > 0): ?>
                                <div class="product-badge badge bg-warning position-absolute top-0 end-0 m-2">Nur noch <?= $produkt['bestand'] ?> auf Lager</div>
                            <?php elseif ($produkt['bestand'] <= 0): ?>
                                <div class="product-badge badge bg-danger position-absolute top-0 end-0 m-2">Nicht verfügbar</div>
                            <?php endif; ?>

                            <div class="product-img-container">
                                <?php if (!empty($produkt['bild_url'])) : ?>
                                    <img src="<?= base_url($produkt['bild_url']) ?>" class="card-img-top product-img" alt="<?= $produkt['name'] ?>">
                                <?php else : ?>
                                    <img src="<?= base_url('assets/images/no-image.jpg') ?>" class="card-img-top product-img" alt="Kein Bild verfügbar">
                                <?php endif; ?>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title product-title"><?= $produkt['name'] ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?= substr($produkt['beschreibung'], 0, 100) ?>...</p>
                                <div class="product-price mb-3 d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold text-primary"><?= number_format($produkt['preis'], 2, ',', '.') ?> €</span>
                                    <div>
                                        <?php if ($produkt['bestand'] <= 0): ?>
                                            <span class="badge bg-danger">Nicht verfügbar</span>
                                        <?php elseif ($produkt['bestand'] <= 5): ?>
                                            <span class="badge bg-warning">Nur noch <?= $produkt['bestand'] ?> auf Lager</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="product-actions d-flex">
                                    <a href="<?= base_url('produkte/detail/' . $produkt['id']) ?>" class="btn btn-outline-primary flex-grow-1 me-2">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </a>
                                    <?php if ($produkt['bestand'] > 0) : ?>
                                        <form action="<?= base_url('warenkorb/hinzufuegen') ?>" method="post" class="d-inline">
                                            <input type="hidden" name="produkt_id" value="<?= $produkt['id'] ?>">
                                            <input type="hidden" name="menge" value="1">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </form>
                                    <?php else : ?>
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-4">
                <a href="<?= base_url('produkte') ?>" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-th-list me-2"></i>Alle Produkte ansehen
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Kategorien im 2x2 Grid -->
<?php if (!empty($kategorien)): ?>
    <section class="categories py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-6 fw-bold position-relative d-inline-block pb-3 border-bottom border-primary">Unsere Kategorien</h2>
                <p class="text-muted mt-3">Durchstöbern Sie unser umfangreiches Sortiment nach Kategorien</p>
            </div>

            <div class="row g-4">
                <?php
                // Zeige maximal 4 Kategorien an
                $display_kategorien = array_slice($kategorien, 0, 4);

                foreach ($display_kategorien as $kategorie) : ?>
                    <div class="col-md-6">
                        <div class="category-card card h-100 shadow border-0 overflow-hidden">
                            <a href="<?= base_url('produkte/kategorie/' . $kategorie['id']) ?>" class="category-img-link">
                                <div class="category-img-container position-relative">
                                    <?php if (!empty($kategorie['bild_url'])) : ?>
                                        <img src="<?= base_url($kategorie['bild_url']) ?>" class="card-img-top category-img" alt="<?= $kategorie['name'] ?>">
                                    <?php else : ?>
                                        <img src="<?= base_url('assets/images/no-image.jpg') ?>" class="card-img-top category-img" alt="Kein Bild verfügbar">
                                    <?php endif; ?>
                                    <div class="category-overlay"></div>
                                    <div class="category-hover-info">
                                    <span class="btn btn-light btn-sm rounded-circle">
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                    </div>
                                </div>
                            </a>
                            <div class="card-body text-center">
                                <h3 class="card-title h5 fw-bold"><?= $kategorie['name'] ?></h3>
                                <p class="card-text text-muted"><?= substr($kategorie['beschreibung'] ?? '', 0, 100) ?>...</p>
                                <a href="<?= base_url('produkte/kategorie/' . $kategorie['id']) ?>" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-1"></i> Ansehen
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Newsletter-Anmeldung mit modernem Design -->
<section class="newsletter py-5" style="background: linear-gradient(135deg, #0d6efd, #0a58ca);">
    <div class="container">
        <!-- Erfolgsmeldung (versteckt, wird per JavaScript eingeblendet) -->
        <div id="newsletter-success" class="alert alert-light mb-4 d-none text-center">
            <i class="fas fa-check-circle me-2 text-success"></i>Vielen Dank! Sie haben den Newsletter erfolgreich abonniert.
        </div>

        <div class="row align-items-center justify-content-center">
            <div class="col-md-5">
                <div class="newsletter-text text-white">
                    <h3 class="fw-bold mb-2"><i class="fas fa-envelope-open-text me-2"></i>Newsletter abonnieren</h3>
                    <p class="mb-0 text-white-50">Erhalten Sie exklusive Angebote und Neuigkeiten direkt in Ihr Postfach</p>
                </div>
            </div>
            <div class="col-md-5">
                <form id="newsletter-form" class="newsletter-form">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Ihre E-Mail-Adresse" required>
                        <button type="submit" class="btn btn-light fw-bold text-primary px-4">
                            <i class="fas fa-paper-plane me-2"></i>Anmelden
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    // Newsletter-Formular Erfolgsanzeige
    document.addEventListener('DOMContentLoaded', function() {
        const newsletterForm = document.getElementById('newsletter-form');
        const successMessage = document.getElementById('newsletter-success');

        if(newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Hier könnte ein AJAX-Call zum Backend erfolgen

                // Formular ausblenden
                this.style.display = 'none';

                // Erfolgsmeldung anzeigen
                successMessage.classList.remove('d-none');

                // Nach 5 Sekunden Formular wieder anzeigen für weitere Anmeldungen
                setTimeout(function() {
                    newsletterForm.style.display = 'block';
                    successMessage.classList.add('d-none');
                    newsletterForm.reset();
                }, 5000);
            });
        }
    });
</script>

<!-- Neue Produkte -->
<?php if (!empty($neueProdukte)): ?>
    <section class="new-products py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-6 fw-bold position-relative d-inline-block pb-3 border-bottom border-primary">Neue Produkte</h2>
                <p class="text-muted mt-3">Die neuesten Zugänge in unserem Sortiment</p>
            </div>

            <div class="row g-4">
                <?php foreach ($neueProdukte as $produkt) : ?>
                    <div class="col-md-3">
                        <div class="product-card card h-100 shadow-sm border-0 position-relative product-hover">
                            <?php if ($produkt['bestand'] <= 5 && $produkt['bestand'] > 0): ?>
                                <div class="product-badge badge bg-warning position-absolute top-0 end-0 m-2">Nur noch <?= $produkt['bestand'] ?> auf Lager</div>
                            <?php elseif ($produkt['bestand'] <= 0): ?>
                                <div class="product-badge badge bg-danger position-absolute top-0 end-0 m-2">Nicht verfügbar</div>
                            <?php endif; ?>

                            <div class="product-img-container">
                                <?php if (!empty($produkt['bild_url'])) : ?>
                                    <img src="<?= base_url($produkt['bild_url']) ?>" class="card-img-top product-img" alt="<?= $produkt['name'] ?>">
                                <?php else : ?>
                                    <img src="<?= base_url('assets/images/no-image.jpg') ?>" class="card-img-top product-img" alt="Kein Bild verfügbar">
                                <?php endif; ?>

                                <div class="product-actions-hover">
                                    <a href="<?= base_url('produkte/detail/' . $produkt['id']) ?>" class="btn btn-light btn-sm rounded-circle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($produkt['bestand'] > 0) : ?>
                                        <form action="<?= base_url('warenkorb/hinzufuegen') ?>" method="post" class="d-inline">
                                            <input type="hidden" name="produkt_id" value="<?= $produkt['id'] ?>">
                                            <input type="hidden" name="menge" value="1">
                                            <button type="submit" class="btn btn-light btn-sm rounded-circle">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title product-title"><?= $produkt['name'] ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?= substr($produkt['beschreibung'], 0, 100) ?>...</p>
                                <div class="product-price mb-3 d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold text-primary"><?= number_format($produkt['preis'], 2, ',', '.') ?> €</span>
                                    <div>
                                        <?php if ($produkt['bestand'] <= 0): ?>
                                            <span class="badge bg-danger">Nicht verfügbar</span>
                                        <?php elseif ($produkt['bestand'] <= 5): ?>
                                            <span class="badge bg-warning">Nur noch <?= $produkt['bestand'] ?> auf Lager</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="product-actions d-flex">
                                    <a href="<?= base_url('produkte/detail/' . $produkt['id']) ?>" class="btn btn-outline-primary flex-grow-1 me-2">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </a>
                                    <?php if ($produkt['bestand'] > 0) : ?>
                                        <form action="<?= base_url('warenkorb/hinzufuegen') ?>" method="post" class="d-inline">
                                            <input type="hidden" name="produkt_id" value="<?= $produkt['id'] ?>">
                                            <input type="hidden" name="menge" value="1">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </form>
                                    <?php else : ?>
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Kundenbewertungen-Teaser -->
<section class="testimonials py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="display-6 fw-bold position-relative d-inline-block pb-3 border-bottom border-primary">Das sagen unsere Kunden</h2>
        </div>

        <div class="row">
            <?php
            // Definieren der Testimonials einmalig
            $testimonials = [
                [
                    'rating' => 5,
                    'date' => '03.05.2025',
                    'text' => '"Die Beratung war super und die Lieferung kam schneller als erwartet. Meine neue Snare klingt fantastisch! Werde definitiv wieder hier einkaufen."',
                    'name' => 'Max M.',
                    'role' => 'Hobbymusiker'
                ],
                [
                    'rating' => 5,
                    'date' => '29.04.2025',
                    'text' => '"Als professioneller Drummer bin ich begeistert von der Qualität und dem Service. Die persönliche Beratung hat mir geholfen, genau das richtige Set für meine Tour zu finden."',
                    'name' => 'Sarah K.',
                    'role' => 'Profi-Musikerin'
                ],
                [
                    'rating' => 4.5,
                    'date' => '01.05.2025',
                    'text' => '"Für meinen Sohn habe ich hier sein erstes Schlagzeug gekauft. Tolle Auswahl für Anfänger und sehr geduldige Beratung. Er ist begeistert und übt jeden Tag!"',
                    'name' => 'Thomas B.',
                    'role' => 'Zufriedener Elternteil'
                ]
            ];

            // Schleife durch die Testimonials
            foreach ($testimonials as $testimonial): ?>
                <div class="col-lg-4 mb-4">
                    <div class="testimonial-card card h-100 border-0 shadow-sm p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="testimonial-stars text-warning me-2">
                                <?php
                                // Sterne entsprechend der Bewertung anzeigen
                                $full_stars = floor($testimonial['rating']);
                                $half_star = $testimonial['rating'] - $full_stars > 0;

                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $full_stars) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif ($half_star && $i == $full_stars + 1) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <div class="testimonial-date text-muted small"><?= $testimonial['date'] ?></div>
                        </div>
                        <p class="testimonial-text fst-italic mb-4"><?= $testimonial['text'] ?></p>
                        <div class="testimonial-author">
                            <p class="fw-bold mb-0"><?= $testimonial['name'] ?></p>
                            <p class="text-muted small"><?= $testimonial['role'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CSS-Styles für die neue Seite -->
<style>
    /* Hero-Bereich */
    .hero-section {
        position: relative;
        padding: 8rem 0;
        margin-top: -1.5rem;
    }

    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    .hero-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .text-shadow {
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .hero-wave {
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        line-height: 0;
    }

    /* Produkt-Styles */
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .product-img-container {
        position: relative;
        overflow: hidden;
        height: 300px; /* Erhöhte Höhe für die 800x400 Bilder */
        background-color: #f8f8f8; /* Heller Hintergrund für die Bilder */
    }

    .product-img {
        width: 100%;
        height: 100%;
        object-fit: contain; /* Behält das Seitenverhältnis bei */
        transition: transform 0.5s ease;
        padding: 10px;
    }

    .product-hover:hover .product-img {
        transform: scale(1.05);
    }

    .product-actions-hover {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.2);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-hover:hover .product-actions-hover {
        opacity: 1;
    }

    /* Kategorie-Styles */
    .category-img-container {
        height: 300px; /* Erhöhte Höhe für die Kategorie-Bilder */
        overflow: hidden;
        position: relative;
    }

    .category-img {
        width: 100%;
        height: 100%;
        object-fit: contain; /* Statt cover, damit das Bild nicht abgeschnitten wird */
        padding: 10px;
        background-color: #f8f8f8;
        transition: transform 0.5s ease;
    }

    .category-card:hover .category-img {
        transform: scale(1.05);
    }

    .category-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.05), rgba(0,0,0,0.2));
    }

    .category-img-link {
        text-decoration: none;
        color: inherit;
        display: block;
        position: relative;
    }

    .category-hover-info {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 2;
    }

    .category-img-link:hover .category-hover-info {
        opacity: 1;
    }

    /* Sektionsheader */
    .section-header .border-bottom {
        border-width: 3px !important;
    }
</style>