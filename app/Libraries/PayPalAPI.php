<?php

namespace App\Libraries;

use Config\PayPal as PayPalConfig;

/**
 * PayPal API Bibliothek für DrumShop
 *
 * Diese Klasse stellt eine Schnittstelle zur PayPal REST API her
 * und ermöglicht die Erstellung und Abwicklung von Zahlungen.
 */
class PayPalAPI
{
    /**
     * PayPal Konfiguration
     * @var PayPalConfig
     */
    protected $config;

    /**
     * OAuth Token für API-Zugriff
     * @var string
     */
    protected $token;

    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->config = new PayPalConfig();
    }

    /**
     * Holt ein OAuth-Token von PayPal
     *
     * @return string Access Token
     * @throws \Exception bei Fehler
     */
    protected function getAccessToken()
    {
        $clientId = $this->config->getClientId();
        $clientSecret = $this->config->getClientSecret();

        if (empty($clientId) || empty($clientSecret)) {
            log_message('error', 'PayPal API: Client-ID oder Client-Secret nicht konfiguriert');
            throw new \Exception('PayPal API Zugangsdaten nicht konfiguriert. Bitte konfigurieren Sie Client-ID und Client-Secret in der PayPal-Konfigurationsdatei.');
        }

        // Debug-Log
        log_message('debug', 'PayPal API: Token-Anfrage an ' . $this->config->getBaseUrl() . 'v1/oauth2/token');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->config->getBaseUrl() . 'v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $clientSecret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: de_DE'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = 'cURL Fehler: ' . curl_error($ch);
            log_message('error', 'PayPal API: ' . $error);
            curl_close($ch);
            throw new \Exception($error);
        }

        curl_close($ch);

        // Debug-Log
        log_message('debug', 'PayPal API: Token-Antwort (HTTP ' . $httpCode . '): ' . $response);

        $data = json_decode($response, true);

        if ($httpCode != 200 || !is_array($data) || isset($data['error'])) {
            $error = 'PayPal API Fehler: ' . ($data['error_description'] ?? $data['error'] ?? 'Unbekannter Fehler (HTTP ' . $httpCode . ')');
            log_message('error', 'PayPal API: ' . $error);
            throw new \Exception($error);
        }

        if (!isset($data['access_token'])) {
            $error = 'PayPal API Fehler: Kein Access Token in der Antwort';
            log_message('error', 'PayPal API: ' . $error);
            throw new \Exception($error);
        }

        log_message('info', 'PayPal API: Access Token erfolgreich erhalten');
        return $data['access_token'];
    }

    /**
     * Erstellt eine neue Bestellung/Zahlung bei PayPal
     *
     * @param array $order_data Bestelldaten
     * @return array PayPal Antwort
     * @throws \Exception bei Fehler
     */
    public function createOrder($order_data)
    {
        try {
            if (!$this->token) {
                $this->token = $this->getAccessToken();
            }

            // Debug-Log
            log_message('debug', 'PayPal API: createOrder-Anfrage an ' . $this->config->getBaseUrl() . 'v2/checkout/orders');
            log_message('debug', 'PayPal API: createOrder-Daten: ' . json_encode($order_data));

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->config->getBaseUrl() . 'v2/checkout/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
                'PayPal-Request-Id: ' . uniqid('drumshop_order_', true)
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $error = 'cURL Fehler: ' . curl_error($ch);
                log_message('error', 'PayPal API: ' . $error);
                curl_close($ch);
                throw new \Exception($error);
            }

            curl_close($ch);

            // Debug-Log
            log_message('debug', 'PayPal API: createOrder-Antwort (HTTP ' . $httpCode . '): ' . $response);

            $data = json_decode($response, true);

            if (!is_array($data)) {
                $error = 'PayPal API Fehler: Ungültige Antwort (kein JSON)';
                log_message('error', 'PayPal API: ' . $error . ' - Antwort: ' . $response);
                throw new \Exception($error);
            }

            if ($httpCode >= 400 || isset($data['error']) || isset($data['name'])) {
                $error = 'PayPal API Fehler: ' .
                    (isset($data['error_description']) ? $data['error_description'] :
                        (isset($data['message']) ? $data['message'] :
                            (isset($data['name']) ? $data['name'] . ': ' . ($data['details'][0]['description'] ?? 'Keine Details') :
                                (isset($data['error']) ? $data['error'] : 'Unbekannter Fehler (HTTP ' . $httpCode . ')'))));
                log_message('error', 'PayPal API: ' . $error . ' - Details: ' . $response);
                throw new \Exception($error);
            }

            if (!isset($data['id'])) {
                $error = 'PayPal API Fehler: Keine Order-ID in der Antwort';
                log_message('error', 'PayPal API: ' . $error . ' - Antwort: ' . $response);
                throw new \Exception($error);
            }

            // Prüfen auf approve-Link
            $approveUrl = null;
            if (isset($data['links']) && is_array($data['links'])) {
                foreach ($data['links'] as $link) {
                    if (isset($link['rel']) && $link['rel'] === 'approve' && isset($link['href'])) {
                        $approveUrl = $link['href'];
                        break;
                    }
                }

                if (!$approveUrl) {
                    $error = 'PayPal API Fehler: Kein approve-Link in der Antwort';
                    log_message('error', 'PayPal API: ' . $error . ' - Antwort: ' . $response);
                    throw new \Exception($error);
                }
            } else {
                $error = 'PayPal API Fehler: Keine Links in der Antwort';
                log_message('error', 'PayPal API: ' . $error . ' - Antwort: ' . $response);
                throw new \Exception($error);
            }

            log_message('info', 'PayPal API: Order erfolgreich erstellt: ' . $data['id']);
            return $data;

        } catch (\Exception $e) {
            // Token-Fehler abfangen und Token neu anfordern
            if (strpos($e->getMessage(), 'Access Token') !== false || strpos($e->getMessage(), 'token') !== false) {
                $this->token = null;
                // Eine Wiederholung versuchen
                log_message('info', 'PayPal API: Token-Fehler, versuche erneut mit neuem Token');
                return $this->createOrder($order_data);
            }

            // Andere Fehler weiterleiten
            throw $e;
        }
    }

    /**
     * Führt eine PayPal-Zahlung durch (Capture)
     *
     * @param string $order_id PayPal Order ID
     * @return array PayPal Antwort
     * @throws \Exception bei Fehler
     */
    public function captureOrder($order_id)
    {
        try {
            if (!$this->token) {
                $this->token = $this->getAccessToken();
            }

            // Debug-Log
            log_message('debug', 'PayPal API: captureOrder-Anfrage an ' . $this->config->getBaseUrl() . 'v2/checkout/orders/' . $order_id . '/capture');

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->config->getBaseUrl() . 'v2/checkout/orders/' . $order_id . '/capture');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
                'PayPal-Request-Id: ' . uniqid('drumshop_capture_', true)
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $error = 'cURL Fehler: ' . curl_error($ch);
                log_message('error', 'PayPal API: ' . $error);
                curl_close($ch);
                throw new \Exception($error);
            }

            curl_close($ch);

            // Debug-Log
            log_message('debug', 'PayPal API: captureOrder-Antwort (HTTP ' . $httpCode . '): ' . $response);

            $data = json_decode($response, true);

            if (!is_array($data)) {
                $error = 'PayPal API Fehler: Ungültige Antwort (kein JSON)';
                log_message('error', 'PayPal API: ' . $error . ' - Antwort: ' . $response);
                throw new \Exception($error);
            }

            if ($httpCode >= 400 || isset($data['error']) || isset($data['name'])) {
                $error = 'PayPal API Fehler: ' .
                    (isset($data['error_description']) ? $data['error_description'] :
                        (isset($data['message']) ? $data['message'] :
                            (isset($data['name']) ? $data['name'] . ': ' . ($data['details'][0]['description'] ?? 'Keine Details') :
                                (isset($data['error']) ? $data['error'] : 'Unbekannter Fehler (HTTP ' . $httpCode . ')'))));
                log_message('error', 'PayPal API: ' . $error . ' - Details: ' . $response);
                throw new \Exception($error);
            }

            if (!isset($data['id'])) {
                $error = 'PayPal API Fehler: Keine Order-ID in der Capture-Antwort';
                log_message('error', 'PayPal API: ' . $error . ' - Antwort: ' . $response);
                throw new \Exception($error);
            }

            log_message('info', 'PayPal API: Order erfolgreich erfasst (captured): ' . $order_id);
            return $data;

        } catch (\Exception $e) {
            // Token-Fehler abfangen und Token neu anfordern
            if (strpos($e->getMessage(), 'Access Token') !== false || strpos($e->getMessage(), 'token') !== false) {
                $this->token = null;
                // Eine Wiederholung versuchen
                log_message('info', 'PayPal API: Token-Fehler, versuche erneut mit neuem Token');
                return $this->captureOrder($order_id);
            }

            // Andere Fehler weiterleiten
            throw $e;
        }
    }

    /**
     * Ruft Details zu einer Bestellung ab
     *
     * @param string $order_id PayPal Order ID
     * @return array PayPal Antwort
     * @throws \Exception bei Fehler
     */
    public function getOrder($order_id)
    {
        try {
            if (!$this->token) {
                $this->token = $this->getAccessToken();
            }

            // Debug-Log
            log_message('debug', 'PayPal API: getOrder-Anfrage an ' . $this->config->getBaseUrl() . 'v2/checkout/orders/' . $order_id);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->config->getBaseUrl() . 'v2/checkout/orders/' . $order_id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $error = 'cURL Fehler: ' . curl_error($ch);
                log_message('error', 'PayPal API: ' . $error);
                curl_close($ch);
                throw new \Exception($error);
            }

            curl_close($ch);

            // Debug-Log
            log_message('debug', 'PayPal API: getOrder-Antwort (HTTP ' . $httpCode . '): ' . $response);

            $data = json_decode($response, true);

            if (!is_array($data)) {
                $error = 'PayPal API Fehler: Ungültige Antwort (kein JSON)';
                log_message('error', 'PayPal API: ' . $error . ' - Antwort: ' . $response);
                throw new \Exception($error);
            }

            if ($httpCode >= 400 || isset($data['error']) || isset($data['name'])) {
                $error = 'PayPal API Fehler: ' .
                    (isset($data['error_description']) ? $data['error_description'] :
                        (isset($data['message']) ? $data['message'] :
                            (isset($data['name']) ? $data['name'] . ': ' . ($data['details'][0]['description'] ?? 'Keine Details') :
                                (isset($data['error']) ? $data['error'] : 'Unbekannter Fehler (HTTP ' . $httpCode . ')'))));
                log_message('error', 'PayPal API: ' . $error . ' - Details: ' . $response);
                throw new \Exception($error);
            }

            log_message('info', 'PayPal API: Order-Details erfolgreich abgerufen: ' . $order_id);
            return $data;

        } catch (\Exception $e) {
            // Token-Fehler abfangen und Token neu anfordern
            if (strpos($e->getMessage(), 'Access Token') !== false || strpos($e->getMessage(), 'token') !== false) {
                $this->token = null;
                // Eine Wiederholung versuchen
                log_message('info', 'PayPal API: Token-Fehler, versuche erneut mit neuem Token');
                return $this->getOrder($order_id);
            }

            // Andere Fehler weiterleiten
            throw $e;
        }
    }

    /**
     * Erstattet eine Zahlung zurück
     *
     * @param string $capture_id Capture ID der PayPal Zahlung
     * @param array $refund_data Rückerstattungsdaten
     * @return array PayPal Antwort
     * @throws \Exception bei Fehler
     */
    public function refundPayment($capture_id, $refund_data)
    {
        try {
            if (!$this->token) {
                $this->token = $this->getAccessToken();
            }

            // Debug-Log
            log_message('debug', 'PayPal API: refundPayment-Anfrage an ' . $this->config->getBaseUrl() . 'v2/payments/captures/' . $capture_id . '/refund');
            log_message('debug', 'PayPal API: refundPayment-Daten: ' . json_encode($refund_data));

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->config->getBaseUrl() . 'v2/payments/captures/' . $capture_id . '/refund');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($refund_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
                'PayPal-Request-Id: ' . uniqid('drumshop_refund_', true)
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $error = 'cURL Fehler: ' . curl_error($ch);
                log_message('error', 'PayPal API: ' . $error);
                curl_close($ch);
                throw new \Exception($error);
            }

            curl_close($ch);

            // Debug-Log
            log_message('debug', 'PayPal API: refundPayment-Antwort (HTTP ' . $httpCode . '): ' . $response);

            $data = json_decode($response, true);

            if (!is_array($data)) {
                $error = 'PayPal API Fehler: Ungültige Antwort (kein JSON)';
                log_message('error', 'PayPal API: ' . $error . ' - Antwort: ' . $response);
                throw new \Exception($error);
            }

            if ($httpCode >= 400 || isset($data['error']) || isset($data['name'])) {
                $error = 'PayPal API Fehler: ' .
                    (isset($data['error_description']) ? $data['error_description'] :
                        (isset($data['message']) ? $data['message'] :
                            (isset($data['name']) ? $data['name'] . ': ' . ($data['details'][0]['description'] ?? 'Keine Details') :
                                (isset($data['error']) ? $data['error'] : 'Unbekannter Fehler (HTTP ' . $httpCode . ')'))));
                log_message('error', 'PayPal API: ' . $error . ' - Details: ' . $response);
                throw new \Exception($error);
            }

            log_message('info', 'PayPal API: Zahlung erfolgreich erstattet: ' . $capture_id);
            return $data;

        } catch (\Exception $e) {
            // Token-Fehler abfangen und Token neu anfordern
            if (strpos($e->getMessage(), 'Access Token') !== false || strpos($e->getMessage(), 'token') !== false) {
                $this->token = null;
                // Eine Wiederholung versuchen
                log_message('info', 'PayPal API: Token-Fehler, versuche erneut mit neuem Token');
                return $this->refundPayment($capture_id, $refund_data);
            }

            // Andere Fehler weiterleiten
            throw $e;
        }
    }

    /**
     * Überprüft die Signatur einer Webhook-Benachrichtigung
     *
     * @param string $payload Der JSON-Payload
     * @param array $headers Die HTTP-Header
     * @return bool True wenn die Signatur gültig ist
     */
    public function verifyWebhookSignature($payload, $headers)
    {
        try {
            if (!$this->token) {
                $this->token = $this->getAccessToken();
            }

            $webhook_id = $this->config->webhookId;

            if (empty($webhook_id)) {
                log_message('error', 'PayPal API: Webhook ID ist nicht konfiguriert');
                return false;
            }

            // Debug-Log
            log_message('debug', 'PayPal API: verifyWebhookSignature-Anfrage an ' . $this->config->getBaseUrl() . 'v1/notifications/verify-webhook-signature');

            $verification_data = [
                'auth_algo' => $headers['PAYPAL-AUTH-ALGO'] ?? '',
                'cert_url' => $headers['PAYPAL-CERT-URL'] ?? '',
                'transmission_id' => $headers['PAYPAL-TRANSMISSION-ID'] ?? '',
                'transmission_sig' => $headers['PAYPAL-TRANSMISSION-SIG'] ?? '',
                'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'] ?? '',
                'webhook_id' => $webhook_id,
                'webhook_event' => $payload
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->config->getBaseUrl() . 'v1/notifications/verify-webhook-signature');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($verification_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                log_message('error', 'PayPal API: cURL Fehler bei Webhook-Verifizierung: ' . curl_error($ch));
                curl_close($ch);
                return false;
            }

            curl_close($ch);

            // Debug-Log
            log_message('debug', 'PayPal API: verifyWebhookSignature-Antwort (HTTP ' . $httpCode . '): ' . $response);

            $data = json_decode($response, true);

            if (isset($data['verification_status']) && $data['verification_status'] === 'SUCCESS') {
                log_message('info', 'PayPal API: Webhook-Signatur erfolgreich verifiziert');
                return true;
            }

            log_message('error', 'PayPal API: Webhook-Signatur ungültig: ' . json_encode($data));
            return false;

        } catch (\Exception $e) {
            log_message('error', 'PayPal API: Fehler bei Webhook-Verifizierung: ' . $e->getMessage());
            return false;
        }
    }
}