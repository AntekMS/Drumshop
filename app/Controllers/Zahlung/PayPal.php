<?php

namespace App\Controllers\Zahlung;

use App\Controllers\BaseController;
use App\Libraries\PayPalAPI;

class PayPal extends BaseController
{
    public function createOrder()
    {
        $request = $this->request;
        $session = session();

        // Daten aus Session holen
        $bestellung_id = $session->get('bestellung_id');

        if (!$bestellung_id) {
            return redirect()->to('/checkout')->with('error', 'Keine Bestellung gefunden');
        }

        $bestellungModel = new \App\Models\BestellungModel();
        $bestellungsdaten = $bestellungModel->find($bestellung_id);

        if (!$bestellungsdaten) {
            return redirect()->to('/checkout')->with('error', 'Bestellung nicht gefunden');
        }

        // PayPal API aufrufen
        $paypal = new PayPalAPI();

        $order_data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => 'drumshop_' . $bestellung_id,
                    'description' => 'DrumShop Bestellung #' . $bestellung_id,
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value' => number_format($bestellungsdaten['gesamtpreis'], 2, '.', '')
                    ]
                ]
            ],
            'application_context' => [
                'brand_name' => 'DrumShop',
                'landing_page' => 'BILLING',
                'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                'user_action' => 'PAY_NOW',
                'return_url' => base_url('zahlung/paypal/capture'),
                'cancel_url' => base_url('checkout')
            ]
        ];

        try {
            $response = $paypal->createOrder($order_data);

            // PayPal Order ID in Session speichern
            $session->set('paypal_order_id', $response['id']);

            // Zu PayPal Seite weiterleiten
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->to($link['href']);
                }
            }

            // Wenn kein Approve-Link gefunden wurde
            return redirect()->to('/checkout')->with('error', 'Fehler beim Erstellen der PayPal-Zahlung');

        } catch (\Exception $e) {
            log_message('error', 'PayPal Create Order Error: ' . $e->getMessage());
            return redirect()->to('/checkout')->with('error', 'Fehler bei der Zahlung: ' . $e->getMessage());
        }
    }

    public function capture()
    {
        $request = $this->request;
        $session = session();

        $token = $request->getGet('token');
        $paypal_order_id = $session->get('paypal_order_id');
        $bestellung_id = $session->get('bestellung_id');

        if (empty($token) || empty($paypal_order_id) || empty($bestellung_id)) {
            return redirect()->to('/checkout')->with('error', 'Ungültige Zahlungsinformationen');
        }

        // PayPal API aufrufen
        $paypal = new PayPalAPI();

        try {
            $response = $paypal->captureOrder($paypal_order_id);

            // Bestellung aktualisieren
            $bestellungModel = new \App\Models\BestellungModel();
            $bestellungModel->update($bestellung_id, [
                'zahlungsstatus' => 'bezahlt',
                'status' => 'bearbeitet'
            ]);

            // Session-Daten löschen
            $session->remove('paypal_order_id');
            $session->remove('bestellung_id');

            // Weiterleitung zur Bestellbestätigung
            return redirect()->to('/checkout/abschluss/' . $bestellung_id)
                ->with('success', 'Zahlung erfolgreich!');

        } catch (\Exception $e) {
            log_message('error', 'PayPal Capture Error: ' . $e->getMessage());
            return redirect()->to('/checkout')->with('error', 'Fehler bei der Zahlungsabwicklung: ' . $e->getMessage());
        }
    }

    public function webhookHandler()
    {
        $request = $this->request;
        $payload = $request->getJSON(true);

        // PayPal Webhook verifizieren
        // TODO: Implementieren Sie die Webhook-Signaturüberprüfung

        // Bestellung aktualisieren basierend auf Webhook-Ereignis
        if (isset($payload['event_type'])) {
            $event_type = $payload['event_type'];
            $resource = $payload['resource'] ?? null;

            if ($resource && isset($resource['custom_id'])) {
                $bestellung_id = $resource['custom_id'];
                $bestellungModel = new \App\Models\BestellungModel();

                switch ($event_type) {
                    case 'PAYMENT.CAPTURE.COMPLETED':
                        $bestellungModel->update($bestellung_id, [
                            'zahlungsstatus' => 'bezahlt',
                            'status' => 'bearbeitet'
                        ]);
                        break;

                    case 'PAYMENT.CAPTURE.REFUNDED':
                        $bestellungModel->update($bestellung_id, [
                            'zahlungsstatus' => 'zurückerstattet',
                            'status' => 'storniert'
                        ]);
                        break;
                }
            }
        }

        // Antwort an PayPal senden
        return $this->response->setJSON(['status' => 'success']);
    }
}