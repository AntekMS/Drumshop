<?php

namespace App\Controllers;

class Checkout extends BaseController
{
    public function index()
    {
        $session = session();
        $warenkorbModel = new \App\Models\WarenkorbModel();

        if (!$session->has('session_id')) {
            $session->set('session_id', session_id());
        }

        $warenkorb = $warenkorbModel->getWarenkorbBySession($session->get('session_id'));

        if (!$warenkorb) {
            $data = [
                'title' => 'Checkout',
                'warenkorb' => null,
                'positionen' => [],
                'gesamtpreis' => 0
            ];
        } else {
            $positionen = $warenkorbModel->getWarenkorbPositionen($warenkorb['id']);
            $gesamtpreis = 0;

            foreach ($positionen as $position) {
                $gesamtpreis += $position['preis'] * $position['menge'];
            }

            $data = [
                'title' => 'Checkout',
                'warenkorb' => $warenkorb,
                'positionen' => $positionen,
                'gesamtpreis' => $gesamtpreis
            ];
        }

        return view('templates/header', $data)
            . view('checkout/index', $data)
            . view('templates/footer');
    }

    public function bestellen()
    {
        $session = session();
        $request = $this->request;
        $warenkorbModel = new \App\Models\WarenkorbModel();
        $bestellungModel = new \App\Models\BestellungModel();

        // Warenkorb prüfen
        $warenkorb = $warenkorbModel->getWarenkorbBySession($session->get('session_id'));

        if (!$warenkorb) {
            return redirect()->to('produkte')->with('error', 'Ihr Warenkorb ist leer');
        }

        $positionen = $warenkorbModel->getWarenkorbPositionen($warenkorb['id']);

        if (empty($positionen)) {
            return redirect()->to('produkte')->with('error', 'Ihr Warenkorb ist leer');
        }

        // Gesamtpreis berechnen
        $gesamtpreis = 0;
        foreach ($positionen as $position) {
            $gesamtpreis += $position['preis'] * $position['menge'];
        }

        // Bestelldaten aus Formular holen
        $bestelldaten = [
            'kunde_name' => $request->getPost('kunde_name'),
            'kunde_email' => $request->getPost('kunde_email'),
            'gesamtpreis' => $gesamtpreis,
            'lieferadresse' => $request->getPost('lieferadresse'),
            'zahlungsmethode' => $request->getPost('zahlungsmethode'),
            'zahlungsstatus' => 'ausstehend',
            'status' => 'neu',
            'anmerkungen' => $request->getPost('anmerkungen') ?? ''
        ];

        // Bestellung erstellen
        $bestellung_id = $bestellungModel->erstelleBestellung($bestelldaten, $positionen);

        // PayPal Zahlung?
        if ($request->getPost('zahlungsmethode') == 'paypal') {
            $session->set('bestellung_id', $bestellung_id);
            return redirect()->to('zahlung/paypal/createOrder');
        }

        // Warenkorb leeren (wird in erstelleBestellung nicht gemacht)
        $db = \Config\Database::connect();
        $db->table('warenkorb_positionen')
            ->where('warenkorb_id', $warenkorb['id'])
            ->delete();

        // Weiterleitung zur Bestellbestätigung
        return redirect()->to('checkout/abschluss/' . $bestellung_id)
            ->with('success', 'Vielen Dank für Ihre Bestellung!');
    }

    public function abschluss($bestellung_id)
    {
        $bestellungModel = new \App\Models\BestellungModel();

        $bestellung = $bestellungModel->find($bestellung_id);

        if (!$bestellung) {
            return redirect()->to('produkte')->with('error', 'Bestellung nicht gefunden');
        }

        $positionen = $bestellungModel->getBestellpositionen($bestellung_id);

        $data = [
            'title' => 'Bestellung abgeschlossen',
            'bestellung' => $bestellung,
            'positionen' => $positionen
        ];

        return view('templates/header', $data)
            . view('checkout/abschluss', $data)
            . view('templates/footer');
    }
}