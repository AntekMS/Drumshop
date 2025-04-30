<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// Testroute zum Überprüfen der Routing-Funktion
$routes->get('/test', function() {
    echo "Die Routing-Funktion funktioniert!";
    return;
});

// Produkt-Routen (Frontend)
$routes->group('produkte', function($routes) {
    $routes->get('/', 'Produkt::index');
    $routes->get('kategorie/(:num)', 'Produkt::kategorie/$1');
    $routes->get('detail/(:num)', 'Produkt::detail/$1');
});

// Warenkorb-Routen
$routes->group('warenkorb', function($routes) {
    $routes->get('/', 'Warenkorb::index');
    $routes->post('hinzufuegen', 'Warenkorb::hinzufuegen');
    $routes->post('aktualisieren', 'Warenkorb::aktualisieren');
    $routes->get('entfernen/(:num)', 'Warenkorb::entfernen/$1');
});

// Checkout-Routen
$routes->group('checkout', function($routes) {
    $routes->get('/', 'Checkout::index');
    $routes->post('bestellen', 'Checkout::bestellen');
    $routes->get('abschluss/(:num)', 'Checkout::abschluss/$1');
});

// PayPal-Zahlungsrouten
$routes->group('zahlung', function($routes) {
    $routes->group('paypal', function($routes) {
        $routes->get('createOrder', 'Zahlung\PayPal::createOrder');
        $routes->get('capture', 'Zahlung\PayPal::capture');
        $routes->post('webhookHandler', 'Zahlung\PayPal::webhookHandler');
    });
});

/// Admin-Bereich
$routes->group('admin', function($routes) {
    // Dashboard
    $routes->get('', 'Admin\Dashboard::index');

    // Produkte
    $routes->get('produkte', 'Admin\Produkt::index');
    $routes->get('produkte/neu', 'Admin\Produkt::neu');
    $routes->post('produkte/speichern', 'Admin\Produkt::speichern');
    $routes->get('produkte/bearbeiten/(:num)', 'Admin\Produkt::bearbeiten/$1');
    $routes->post('produkte/aktualisieren/(:num)', 'Admin\Produkt::aktualisieren/$1');
    $routes->get('produkte/loeschen/(:num)', 'Admin\Produkt::loeschen/$1');

    // Kategorien
    $routes->get('kategorien', 'Admin\Kategorie::index');
    $routes->get('kategorien/neu', 'Admin\Kategorie::neu');
    $routes->post('kategorien/speichern', 'Admin\Kategorie::speichern');
    $routes->get('kategorien/bearbeiten/(:num)', 'Admin\Kategorie::bearbeiten/$1');
    $routes->post('kategorien/aktualisieren/(:num)', 'Admin\Kategorie::aktualisieren/$1');
    $routes->get('kategorien/loeschen/(:num)', 'Admin\Kategorie::loeschen/$1');

    // Bestellungen
    $routes->get('bestellungen', 'Admin\Bestellung::index');
    $routes->get('bestellungen/detail/(:num)', 'Admin\Bestellung::detail/$1');
    $routes->post('bestellungen/statusAendern/(:num)', 'Admin\Bestellung::statusAendern/$1');

    // Bestellungen - Bearbeiten
    $routes->get('bestellungen/bearbeiten/(:num)', 'Admin\Bestellung::bearbeiten/$1');
    $routes->post('bestellungen/aktualisieren/(:num)', 'Admin\Bestellung::aktualisieren/$1');

    // Bestellungen - Stornieren
    $routes->get('bestellungen/stornieren/(:num)', 'Admin\Bestellung::stornierungBestaetigen/$1');
    $routes->post('bestellungen/stornierungDurchfuehren', 'Admin\Bestellung::stornierungDurchfuehren');

    // Bestellungen - Löschen
    $routes->get('bestellungen/loeschen/(:num)', 'Admin\Bestellung::loeschen/$1');

    // Bestellungen - E-Mail senden
    $routes->get('bestellungen/email/(:num)', 'Admin\Bestellung::email/$1');
    $routes->post('bestellungen/emailSenden/(:num)', 'Admin\Bestellung::emailSenden/$1');

    // Bestellungen - Rechnung
    $routes->get('bestellungen/rechnung/(:num)', 'Admin\Bestellung::rechnung/$1');
});

// API-Routen
$routes->group('api', function($routes) {
    // Produkt-API
    $routes->get('produkte', 'API\Produkt::index');
    $routes->get('produkte/(:num)', 'API\Produkt::detail/$1');
    $routes->get('produkte/hervorgehoben', 'API\Produkt::hervorgehoben');
    $routes->get('produkte/neu', 'API\Produkt::neu');
    $routes->get('produkte/kategorie/(:num)', 'API\Produkt::kategorie/$1');
    $routes->get('produkte/suche', 'API\Produkt::suche');
    $routes->get('produkte/lagerbestand/(:num)', 'API\Produkt::lagerbestand/$1');
    $routes->post('produkte', 'API\Produkt::erstellen');
    $routes->put('produkte/(:num)', 'API\Produkt::aktualisieren/$1');
    $routes->delete('produkte/(:num)', 'API\Produkt::loeschen/$1');

    // Bestellungs-API
    $routes->get('bestellungen', 'API\Bestellung::index');
    $routes->get('bestellungen/(:num)', 'API\Bestellung::detail/$1');
    $routes->get('bestellungen/kunde/(:segment)', 'API\Bestellung::kundenbestellungen/$1');
    $routes->get('bestellungen/status/(:num)', 'API\Bestellung::status/$1');
    $routes->post('bestellungen', 'API\Bestellung::erstellen');
    $routes->put('bestellungen/status/(:num)', 'API\Bestellung::statusAendern/$1');
    $routes->put('bestellungen/stornieren/(:num)', 'API\Bestellung::stornieren/$1');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}