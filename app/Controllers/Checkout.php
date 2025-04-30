<?php

namespace App\Controllers;

class Checkout extends BaseController
{
    /**
     * Zeigt die Checkout-Seite mit Warenkorbinhalten an
     */
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

    /**
     * Verarbeitet die Bestellung und leitet je nach Zahlungsmethode weiter
     */
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

        // Validierung der Eingaben
        $validation = \Config\Services::validation();
        $validation->setRules([
            'kunde_name' => 'required|min_length[3]',
            'kunde_email' => 'required|valid_email',
            'lieferadresse' => 'required|min_length[10]',
            'zahlungsmethode' => 'required|in_list[paypal,kreditkarte,rechnung,vorkasse]'
        ]);

        if (!$validation->run($bestelldaten)) {
            return redirect()->back()->withInput()->with('error', $validation->getErrors());
        }

        // Bestellung erstellen
        $bestellung_id = $bestellungModel->erstelleBestellung($bestelldaten, $positionen);

        // Bestellungs-ID in Session speichern für PayPal
        $session->set('bestellung_id', $bestellung_id);

        // Weiterleitung je nach Zahlungsmethode
        switch ($request->getPost('zahlungsmethode')) {
            case 'paypal':
                // Bei PayPal-Zahlung zur PayPal API weiterleiten
                return redirect()->to('zahlung/paypal/createOrder');

            case 'kreditkarte':
                // Kreditkartendaten verarbeiten
                $kk_nummer = $request->getPost('kreditkarte_nummer');
                $kk_ablauf = $request->getPost('kreditkarte_ablauf');
                $kk_cvv = $request->getPost('kreditkarte_cvv');

                // Hier würde normalerweise ein Payment Gateway für Kreditkarten angebunden
                // Für dieses Demoprojekt simulieren wir erfolgreiche Zahlung
                $bestellungModel->update($bestellung_id, [
                    'zahlungsstatus' => 'bezahlt',
                    'status' => 'bearbeitet'
                ]);

                // Warenkorb leeren
                $db = \Config\Database::connect();
                $db->table('warenkorb_positionen')
                    ->where('warenkorb_id', $warenkorb['id'])
                    ->delete();

                // Zur Bestellbestätigung weiterleiten
                return redirect()->to('checkout/abschluss/' . $bestellung_id)
                    ->with('success', 'Zahlung erfolgreich! Vielen Dank für Ihre Bestellung.');

            case 'rechnung':
            case 'vorkasse':
            default:
                // Warenkorb leeren
                $db = \Config\Database::connect();
                $db->table('warenkorb_positionen')
                    ->where('warenkorb_id', $warenkorb['id'])
                    ->delete();

                // Bei Rechnungs- oder Vorkassenzahlung direkt zur Bestellbestätigung
                return redirect()->to('checkout/abschluss/' . $bestellung_id)
                    ->with('success', 'Vielen Dank für Ihre Bestellung! Die Zahlungsinformationen wurden Ihnen per E-Mail zugesendet.');
        }
    }

    /**
     * Zeigt die Bestellbestätigung an
     */
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

    /**
     * Prüft ob die eingegebenen Kreditkartendaten gültig sind
     * (Einfache Demo-Implementation für das Abschlussprojekt)
     */
    private function validateCreditCard($number, $expiry, $cvv)
    {
        // Nummer prüfen (einfache Luhn-Algorithmus-Prüfung)
        $number = preg_replace('/\D/', '', $number); // Entferne alle Nicht-Ziffern

        if (strlen($number) < 13 || strlen($number) > 19) {
            return false;
        }

        // Prüfsumme berechnen
        $sum = 0;
        $length = strlen($number);
        for ($i = 0; $i < $length; $i++) {
            $digit = (int) $number[$length - 1 - $i];
            if ($i % 2 == 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        if ($sum % 10 != 0) {
            return false;
        }

        // Ablaufdatum prüfen (MM/YY Format)
        $expiry = preg_replace('/\D/', '', $expiry);
        if (strlen($expiry) != 4) {
            return false;
        }

        $month = (int) substr($expiry, 0, 2);
        $year = (int) ('20' . substr($expiry, 2, 2));

        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth) || $month < 1 || $month > 12) {
            return false;
        }

        // CVV prüfen
        $cvv = preg_replace('/\D/', '', $cvv);
        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            return false;
        }

        return true;
    }
}