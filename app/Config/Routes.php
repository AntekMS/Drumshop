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
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// Produkt-Routen
$routes->get('produkte', 'Produkt::index');
$routes->get('produkte/detail/(:num)', 'Produkt::detail/$1');
$routes->get('produkte/kategorie/(:num)', 'Produkt::kategorie/$1');

// Warenkorb-Routen
$routes->get('warenkorb', 'Warenkorb::index');
$routes->post('warenkorb/hinzufuegen', 'Warenkorb::hinzufuegen');
$routes->post('warenkorb/aktualisieren', 'Warenkorb::aktualisieren');
$routes->get('warenkorb/entfernen/(:num)', 'Warenkorb::entfernen/$1');

// Checkout-Routen
$routes->get('checkout', 'Checkout::index');
$routes->post('checkout/bestellen', 'Checkout::bestellen');
$routes->get('checkout/abschluss/(:num)', 'Checkout::abschluss/$1');

// PayPal-Zahlungsrouten
$routes->get('zahlung/paypal/createOrder', 'Zahlung\PayPal::createOrder');
$routes->get('zahlung/paypal/capture', 'Zahlung\PayPal::capture');
$routes->get('zahlung/paypal/cancel', 'Zahlung\PayPal::cancel');
$routes->post('zahlung/paypal/webhook', 'Zahlung\PayPal::webhookHandler');

// Admin-Routen
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
    $routes->post('produkte/massenAktion', 'Admin\Produkt::massenAktion');

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
    $routes->get('bestellungen/bearbeiten/(:num)', 'Admin\Bestellung::bearbeiten/$1');
    $routes->post('bestellungen/aktualisieren/(:num)', 'Admin\Bestellung::aktualisieren/$1');
    $routes->post('bestellungen/statusAendern/(:num)', 'Admin\Bestellung::statusAendern/$1');
    $routes->get('bestellungen/stornieren/(:num)', 'Admin\Bestellung::stornierungBestaetigen/$1');
    $routes->post('bestellungen/stornierungDurchfuehren', 'Admin\Bestellung::stornierungDurchfuehren');
    $routes->get('bestellungen/email/(:num)', 'Admin\Bestellung::email/$1');
    $routes->post('bestellungen/emailSenden/(:num)', 'Admin\Bestellung::emailSenden/$1');
    $routes->get('bestellungen/rechnung/(:num)', 'Admin\Bestellung::rechnung/$1');
    $routes->get('bestellungen/loeschen/(:num)', 'Admin\Bestellung::loeschen/$1');
});

// API-Routen
$routes->group('api', ['namespace' => 'App\Controllers\API'], function($routes) {
    // Produkt-API
    $routes->get('produkte', 'Produkt::index');
    $routes->get('produkte/(:num)', 'Produkt::show/$1');
    $routes->post('produkte', 'Produkt::create');
    $routes->put('produkte/(:num)', 'Produkt::update/$1');
    $routes->delete('produkte/(:num)', 'Produkt::delete/$1');

    // Bestellung-API
    $routes->get('bestellungen', 'Bestellung::index');
    $routes->get('bestellungen/(:num)', 'Bestellung::detail/$1');
    $routes->post('bestellungen', 'Bestellung::erstellen');
    $routes->get('bestellungen/kunde/(:segment)', 'Bestellung::kundenbestellungen/$1');
    $routes->get('bestellungen/status/(:num)', 'Bestellung::status/$1');
    $routes->put('bestellungen/status/(:num)', 'Bestellung::statusAendern/$1');
    $routes->put('bestellungen/stornieren/(:num)', 'Bestellung::stornieren/$1');
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