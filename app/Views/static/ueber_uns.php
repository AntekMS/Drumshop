<?php
/**
 * Über uns View
 *
 * @package DrumShop
 */
?>
<div class="container py-5">
    <!-- Breadcrumb-Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Über uns</li>
        </ol>
    </nav>

    <h1 class="mb-4">Über uns</h1>

    <div class="row mb-5">
        <div class="col-md-6">
            <img src="<?= base_url('assets/images/store.png') ?>" alt="DrumShop Ladengeschäft" class="img-fluid rounded mb-3">
        </div>
        <div class="col-md-6">
            <h2 class="h3 mb-3">Willkommen beim DrumShop</h2>
            <p>Seit anfang 2025 sind wir Ihr verlässlicher Partner für alles rund um Schlagzeug und Percussion. Was als kleine Werkstatt begann, hat sich in kürzester Zeit zu einem der führenden Fachhändler in Deutschland entwickelt.</p>
            <p>Unser Ziel ist es, Ihnen nicht nur hochwertige Instrumente zu bieten, sondern auch eine kompetente Beratung und einen erstklassigen Service.</p>
            <p>Ob Anfänger oder Profi, bei uns finden Sie genau das richtige Equipment für Ihre Bedürfnisse.</p>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h2 class="h3 mb-3">Unsere Geschichte</h2>
                    <p>DrumShop wurde 2025 von Julius Walter gegründet, einem leidenschaftlichen Schlagzeuger mit dem Traum, anderen Musikern qualitativ hochwertige Instrumente zu fairen Preisen anzubieten.</p>
                    <p>Was in einer kleinen Garage begann, ist heute ein moderner Online-Shop mit einem großen Ladengeschäft in Heilbronx, in dem regelmäßig Workshops und Drumclinics stattfinden.</p>
                    <p>Über die Jahre haben wir uns einen Namen für exzellente Produktqualität und hervorragenden Kundenservice gemacht.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-drum fa-3x text-primary mb-3"></i>
                    <h3 class="h4">Große Auswahl</h3>
                    <p>Wir führen über 2.000 Produkte von mehr als 50 renommierten Herstellern. Von Drumsets über Becken bis hin zu Zubehör und Ersatzteilen - bei uns finden Sie alles.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h3 class="h4">Expertenteam</h3>
                    <p>Unser Team besteht aus erfahrenen Musikern, die Ihr Handwerk verstehen. Wir bieten individuelle Beratung und helfen Ihnen, das perfekte Instrument zu finden.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-tools fa-3x text-primary mb-3"></i>
                    <h3 class="h4">Service & Reparatur</h3>
                    <p>Unsere hauseigene Werkstatt bietet professionelle Reparatur- und Wartungsservices. Wir kümmern uns um Ihr Equipment, damit Sie sich aufs Spielen konzentrieren können.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <h2 class="h3 mb-4">Unser Team</h2>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <img src="<?= base_url('assets/images/team/walter_julius.jpg') ?>" class="card-img-top" alt="Julius Walter">
                <div class="card-body">
                    <h3 class="h5">Julius Walter</h3>
                    <p class="mb-1">Geschäftsführer & Gründer</p>
                    <p class="text-muted small">Seit 2025 dabei</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <img src="<?= base_url('assets/images/team/sobkowiak_antek.jpg') ?>" class="card-img-top" alt="Antek Sobkowiak">
                <div class="card-body">
                    <h3 class="h5">Antek Sobkowiak</h3>
                    <p class="mb-1">IT & Mitgründer</p>
                    <p class="text-muted small">Seit 2025 dabei</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <img src="<?= base_url('assets/images/team/parlak_dennis.jpg') ?>" class="card-img-top" alt="Dennis Parlak">
                <div class="card-body">
                    <h3 class="h5">Dennis Parlak</h3>
                    <p class="mb-1">Kundenservice & Mitgründer</p>
                    <p class="text-muted small">Seit 2025 dabei</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="h3 mb-3">Besuchen Sie uns</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="h5">Anschrift</h4>
                            <address>
                                DrumShop GmbH<br>
                                Bildungscampus 4<br>
                                74076 Heilbronn<br>
                                Deutschland
                            </address>

                            <h4 class="h5 mt-4">Öffnungszeiten</h4>
                            <p>Montag - Freitag: 10:00 - 19:00 Uhr<br>
                                Samstag: 10:00 - 16:00 Uhr<br>
                                Sonntag: geschlossen</p>

                            <h4 class="h5 mt-4">Kontakt</h4>
                            <p>
                                Telefon: <a href="tel:+49 7131 1237 0">+49 7131 1237 0</a><br>
                                E-Mail: <a href="mailto:info@drumshop.de">info@drumshop.de</a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="ratio ratio-16x9">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2652.0100161081737!2d9.216280515893023!3d49.14708987931007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47982f44671ce9c1%3A0xf81801b6c8b4ea05!2sDuale%20Hochschule%20Baden-W%C3%BCrttemberg%20Heilbronn!5e0!3m2!1sde!2sde!4v1714997780000!5m2!1sde!2sde" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>