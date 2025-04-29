<?php

namespace App\Libraries;

/**
 * PayPal API Bibliothek für DrumShop
 *
 * Diese Klasse stellt eine Schnittstelle zur PayPal REST API her
 * und ermöglicht die Erstellung und Abwicklung von Zahlungen.
 */
class PayPalAPI
{
    /**
     * API-Basis-URL für Sandbox (Testumgebung)
     * @var string
     */
    protected $sandbox_url = 'https://api-m.sandbox.paypal.com/';

    /**
     * API-Basis-URL für Produktionsumgebung
     * @var string
     */
    protected $production_url = 'https://api-m.paypal.com/';

    /**
     * PayPal Client ID
     * @var string
     */
    protected $client_id;

    /**
     * PayPal Client Secret
     * @var string
     */
    protected $client_secret;

    /**
     * OAuth Token für API-Zugriff
     * @var string
     */
    protected $token;

    /**
     * API-Modus (sandbox oder production)
     * @var string
     */
    protected $mode;

    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->client_id = getenv('PAYPAL_CLIENT_ID') ?: '';
        $this->client_secret = getenv('PAYPAL_CLIENT_SECRET') ?: '';
        $this->mode = getenv('PAYPAL_MODE') ?: 'sandbox';
    }

    /**
     * Gibt die aktuelle API-Basis-URL zurück
     *
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->mode === 'production' ? $this->production_url : $this->sandbox_url;
    }

    /**
     * Holt ein OAuth-Token von PayPal
     *
     * @return string Access Token
     * @throws \Exception bei Fehler
     */
    protected function getAccessToken()
    {
        if (empty($this->client_id) || empty($this->client_secret)) {
            throw new \Exception('PayPal API Zugangsdaten nicht konfiguriert');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . 'v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_USERPWD, $this->client_id . ':' . $this->client_secret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: de_DE'
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('cURL Fehler: ' . curl_error($ch));
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            throw new \Exception('PayPal API Fehler: ' . ($data['error_description'] ?? $data['error']));
        }

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
        if (!$this->token) {
            $this->token = $this->getAccessToken();
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . 'v2/checkout/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token,
            'PayPal-Request-Id: ' . uniqid('drumshop_order_', true)
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('cURL Fehler: ' . curl_error($ch));
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            throw new \Exception('PayPal API Fehler: ' . ($data['error_description'] ?? $data['error']));
        }

        return $data;
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
        if (!$this->token) {
            $this->token = $this->getAccessToken();
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . 'v2/checkout/orders/' . $order_id . '/capture');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token,
            'PayPal-Request-Id: ' . uniqid('drumshop_capture_', true)
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('cURL Fehler: ' . curl_error($ch));
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            throw new \Exception('PayPal API Fehler: ' . ($data['error_description'] ?? $data['error']));
        }

        return $data;
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
        if (!$this->token) {
            $this->token = $this->getAccessToken();
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . 'v2/checkout/orders/' . $order_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('cURL Fehler: ' . curl_error($ch));
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            throw new \Exception('PayPal API Fehler: ' . ($data['error_description'] ?? $data['error']));
        }

        return $data;
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
        if (!$this->token) {
            $this->token = $this->getAccessToken();
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . 'v2/payments/captures/' . $capture_id . '/refund');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($refund_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token,
            'PayPal-Request-Id: ' . uniqid('drumshop_refund_', true)
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('cURL Fehler: ' . curl_error($ch));
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            throw new \Exception('PayPal API Fehler: ' . ($data['error_description'] ?? $data['error']));
        }

        return $data;
    }
}