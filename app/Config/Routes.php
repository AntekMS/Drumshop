<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Produkt-Routen
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

// Admin-Bereich
$routes->group('admin', function($routes) {
    $routes->get('/', 'Admin\Dashboard::index');

    // Produkt-Verwaltung
    $routes->get('produkte', 'Admin\Produkt::index');
    $routes->get('produkte/neu', 'Admin\Produkt::neu');
    $routes->post('produkte/speichern', 'Admin\Produkt::speichern');
    $routes->get('produkte/bearbeiten/(:num)', 'Admin\Produkt::bearbeiten/$1');
    $routes->post('produkte/aktualisieren/(:num)', 'Admin\Produkt::aktualisieren/$1');
    $routes->get('produkte/loeschen/(:num)', 'Admin\Produkt::loeschen/$1');

    // Kategorie-Verwaltung
    $routes->get('kategorien', 'Admin\Kategorie::index');
    $routes->get('kategorien/neu', 'Admin\Kategorie::neu');
    $routes->post('kategorien/speichern', 'Admin\Kategorie::speichern');
    $routes->get('kategorien/bearbeiten/(:num)', 'Admin\Kategorie::bearbeiten/$1');
    $routes->post('kategorien/aktualisieren/(:num)', 'Admin\Kategorie::aktualisieren/$1');
    $routes->get('kategorien/loeschen/(:num)', 'Admin\Kategorie::loeschen/$1');

    // Bestellungen
    $routes->get('bestellungen', 'Admin\Bestellung::index');
    $routes->get('bestellungen/detail/(:num)', 'Admin\Bestellung::detail/$1');
    $routes->post('bestellungen/status/(:num)', 'Admin\Bestellung::statusAendern/$1');
});

// API-Routen
$routes->group('api', function($routes) {
    // Interne API-Routen
    $routes->get('produkte', 'API\Produkt::index');
    $routes->get('produkte/(:num)', 'API\Produkt::detail/$1');
    $routes->post('produkte', 'API\Produkt::erstellen');
    $routes->put('produkte/(:num)', 'API\Produkt::aktualisieren/$1');
    $routes->delete('produkte/(:num)', 'API\Produkt::loeschen/$1');

    $routes->get('bestellungen', 'API\Bestellung::index');
    $routes->post('bestellungen', 'API\Bestellung::erstellen');
    $routes->get('bestellungen/(:num)', 'API\Bestellung::detail/$1');
});