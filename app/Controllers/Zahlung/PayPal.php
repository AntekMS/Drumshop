<?php

namespace App\Controllers\Zahlung;

use App\Controllers\BaseController;
use App\Libraries\PayPalAPI;
use Config\PayPal as PayPalConfig;

class PayPal extends BaseController
{
    protected $paypal;
    protected $config;

    public function __construct()
    {
        $this->paypal = new PayPalAPI();
        $this->config = new PayPalConfig();
    }

    /**
     * Erstellt eine neue PayPal-Bestellung und leitet zur Zahlungsseite weiter
     */
    public function createOrder()
    {
        $session = session();
        $bestellung_id = $session->get('bestellung_id');

        if (!$bestellung_id) {
            return redirect()->to('/checkout')->with('error', 'Keine Bestellung gefunden');
        }

        $bestellungModel = new \App\Models\BestellungModel();
        $bestellungsdaten = $bestellungModel->find($bestellung_id);

        if (!$bestellungsdaten) {
            return redirect()->to('/checkout')->with('error', 'Bestellung nicht gefunden');
        }

        // Gesamtpreis validieren und formatieren
        $gesamtpreis = $bestellungsdaten['gesamtpreis'];
        if (!is_numeric($gesamtpreis) || $gesamtpreis <= 0) {
            log_message('error', 'PayPal: Ungültiger Bestellwert: ' . $gesamtpreis);
            return redirect()->to('/checkout')->with('error', 'Ungültiger Bestellwert');
        }

        // Auf zwei Dezimalstellen runden und als String formatieren
        $gesamtpreis_formatiert = number_format((float)$gesamtpreis, 2, '.', '');

        // Sicherstellen, dass das Format korrekt ist
        if (!preg_match('/^\d+\.\d{2}$/', $gesamtpreis_formatiert)) {
            log_message('error', 'PayPal: Falsch formatierter Bestellwert: ' . $gesamtpreis_formatiert);
            return redirect()->to('/checkout')->with('error', 'Fehler bei der Preisformatierung');
        }

        // PayPal Order erstellen - Vereinfachte Version mit minimalen Pflichtfeldern
        $order_data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => 'order_' . $bestellung_id,
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value' => $gesamtpreis_formatiert
                    ]
                ]
            ],
            'application_context' => [
                'brand_name' => 'DrumShop',
                'landing_page' => 'BILLING',
                'user_action' => 'PAY_NOW',
                'return_url' => base_url('zahlung/paypal/capture'),
                'cancel_url' => base_url('zahlung/paypal/cancel')
            ]
        ];

        try {
            $response = $this->paypal->createOrder($order_data);

            // Debug-Ausgabe für Fehlersuche
            log_message('debug', 'PayPal createOrder Response: ' . json_encode($response));

            // PayPal Order ID in Session speichern
            if (isset($response['id'])) {
                $session->set('paypal_order_id', $response['id']);
            } else {
                log_message('error', 'PayPal Response enthält keine Order ID: ' . json_encode($response));
                return redirect()->to('/checkout')->with('error', 'Fehler bei der Zahlung: Keine Order-ID erhalten');
            }

            // Zu PayPal Seite weiterleiten
            $approveUrl = null;
            if (isset($response['links']) && is_array($response['links'])) {
                foreach ($response['links'] as $link) {
                    if (isset($link['rel']) && $link['rel'] === 'approve' && isset($link['href'])) {
                        $approveUrl = $link['href'];
                        break;
                    }
                }
            }

            if ($approveUrl) {
                return redirect()->to($approveUrl);
            } else {
                log_message('error', 'PayPal Response enthält keinen approve-Link: ' . json_encode($response));
                return redirect()->to('/checkout')->with('error', 'Fehler beim Erstellen der PayPal-Zahlung: Kein Weiterleitungslink erhalten');
            }

        } catch (\Exception $e) {
            log_message('error', 'PayPal Create Order Error: ' . $e->getMessage());
            return redirect()->to('/checkout')->with('error', 'Fehler bei der Zahlung: ' . $e->getMessage());
        }
    }

    /**
     * Verarbeitet die Zahlung nach der Genehmigung durch den Kunden
     */
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

        try {
            $response = $this->paypal->captureOrder($paypal_order_id);

            // Debug-Ausgabe für Fehlersuche
            log_message('debug', 'PayPal captureOrder Response: ' . json_encode($response));

            // Bestellung laden
            $bestellungModel = new \App\Models\BestellungModel();
            $bestellung = $bestellungModel->find($bestellung_id);

            if (!$bestellung) {
                log_message('error', 'Bestellung nicht gefunden: ID ' . $bestellung_id);
                return redirect()->to('/checkout')->with('error', 'Bestellung konnte nicht gefunden werden');
            }

            // Zahlungsstatus überprüfen
            $captureStatus = null;
            if (isset($response['purchase_units'][0]['payments']['captures'][0]['status'])) {
                $captureStatus = $response['purchase_units'][0]['payments']['captures'][0]['status'];
            }

            // Bestellung aktualisieren
            $updateData = [
                'zahlungsstatus' => ($captureStatus === 'COMPLETED') ? 'bezahlt' : 'in_bearbeitung',
                'status' => ($captureStatus === 'COMPLETED') ? 'bearbeitet' : 'neu'
            ];

            if (isset($response['id'])) {
                $anmerkung = ($bestellung['anmerkungen'] ?? '') . "\n\nPayPal TransaktionsID: " . $response['id'];
                $updateData['anmerkungen'] = $anmerkung;
            }

            $bestellungModel->update($bestellung_id, $updateData);

            // Warenkorb leeren
            $warenkorbModel = new \App\Models\WarenkorbModel();
            $warenkorb = $warenkorbModel->getWarenkorbBySession($session->get('session_id'));
            if ($warenkorb) {
                $db = \Config\Database::connect();
                $db->table('warenkorb_positionen')->where('warenkorb_id', $warenkorb['id'])->delete();
            }

            // Session-Daten löschen
            $session->remove('paypal_order_id');
            $session->remove('bestellung_id');

            // Weiterleitung zur Bestellbestätigung
            return redirect()->to('/checkout/abschluss/' . $bestellung_id)
                ->with('success', 'Zahlung erfolgreich! Vielen Dank für Ihre Bestellung.');

        } catch (\Exception $e) {
            log_message('error', 'PayPal Capture Error: ' . $e->getMessage());
            return redirect()->to('/checkout')->with('error', 'Fehler bei der Zahlungsabwicklung: ' . $e->getMessage());
        }
    }

    /**
     * Verarbeitet PayPal Webhook Benachrichtigungen
     */
    public function webhookHandler()
    {
        $request = $this->request;

        // Payload und Header auslesen
        $payload = $request->getJSON(true);
        $headers = $request->getHeaders();

        // Debug-Log der Webhook-Daten
        log_message('debug', 'PayPal Webhook erhalten: ' . json_encode($payload));

        // Webhook-Signatur überprüfen (nur in Produktivumgebung)
        if ($this->config->mode === 'production') {
            if (!$this->paypal->verifyWebhookSignature(json_encode($payload), $headers)) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid signature']);
            }
        }

        // Ereignistyp verarbeiten
        if (isset($payload['event_type'])) {
            $event_type = $payload['event_type'];
            $resource = $payload['resource'] ?? null;

            if ($resource) {
                // Bestellungs-ID aus dem Ressourcenobjekt extrahieren
                $bestellung_id = null;

                // Bestellreferenz finden basierend auf dem Ereignistyp
                if (isset($resource['custom_id'])) {
                    // Direkt aus custom_id
                    $bestellung_id = $resource['custom_id'];
                } elseif (isset($resource['purchase_units'][0]['reference_id'])) {
                    // Aus reference_id für Order-Ereignisse
                    $reference_id = $resource['purchase_units'][0]['reference_id'];
                    // Format "order_123" extrahieren
                    if (preg_match('/^order_(\d+)$/', $reference_id, $matches)) {
                        $bestellung_id = $matches[1];
                    }
                }

                if ($bestellung_id) {
                    $bestellungModel = new \App\Models\BestellungModel();

                    switch ($event_type) {
                        case 'PAYMENT.CAPTURE.COMPLETED':
                            $bestellungModel->update($bestellung_id, [
                                'zahlungsstatus' => 'bezahlt',
                                'status' => 'bearbeitet'
                            ]);
                            break;

                        case 'PAYMENT.CAPTURE.DENIED':
                        case 'PAYMENT.CAPTURE.DECLINED':
                            $bestellungModel->update($bestellung_id, [
                                'zahlungsstatus' => 'abgelehnt',
                                'status' => 'storniert'
                            ]);
                            break;

                        case 'PAYMENT.CAPTURE.REFUNDED':
                            $bestellungModel->update($bestellung_id, [
                                'zahlungsstatus' => 'zurückerstattet',
                                'status' => 'storniert'
                            ]);
                            break;

                        case 'PAYMENT.CAPTURE.PENDING':
                            $bestellungModel->update($bestellung_id, [
                                'zahlungsstatus' => 'ausstehend'
                            ]);
                            break;
                    }

                    // Ereignis protokollieren
                    log_message('info', 'PayPal Webhook verarbeitet: ' . $event_type . ' für Bestellung #' . $bestellung_id);
                }
            }
        }

        // HTTP 200 an PayPal zurücksenden
        return $this->response->setJSON(['status' => 'success']);
    }

    /**
     * Zeigt eine Bestätigungsseite nach abgebrochener PayPal-Zahlung
     */
    public function cancel()
    {
        return redirect()->to('/checkout')
            ->with('error', 'Die PayPal-Zahlung wurde abgebrochen. Sie können eine andere Zahlungsmethode wählen oder es später erneut versuchen.');
    }
}