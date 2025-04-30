<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class PayPal extends BaseConfig
{
    /**
     * PayPal-Modus: 'sandbox' oder 'production'
     */
    public $mode = 'sandbox';

    /**
     * API-Zugangsdaten für Sandbox (Testumgebung)
     */
    public $sandboxClientId = 'AVdathZPObWjwHc-I1t0Ci-uHvEquR1Jpo0b9UFkIjz9fytGYdnJzOsUaavmvzXlgKifKhZilqnXIDiw';
    public $sandboxClientSecret = 'EH7gmdILheL-Oco5mIVwzeYYsmWYmoooiysqSMCzpgnr45gRfv-5RCGJqlLWZoeDtMnKtEhol3NVykBi';

    /**
     * API-Zugangsdaten für Production (Live-Umgebung)
     */
    public $productionClientId = '';
    public $productionClientSecret = '';

    /**
     * Webhook-Konfiguration
     */
    public $webhookId = '';

    /**
     * PayPal API URLs
     */
    public $sandboxUrl = 'https://api-m.sandbox.paypal.com/';
    public $productionUrl = 'https://api-m.paypal.com/';

    /**
     * Gibt die aktuelle API-Basis-URL zurück basierend auf dem ausgewählten Modus
     */
    public function getBaseUrl()
    {
        return $this->mode === 'production' ? $this->productionUrl : $this->sandboxUrl;
    }

    /**
     * Gibt die Client-ID für den aktuellen Modus zurück
     */
    public function getClientId()
    {
        return $this->mode === 'production' ? $this->productionClientId : $this->sandboxClientId;
    }

    /**
     * Gibt das Client-Secret für den aktuellen Modus zurück
     */
    public function getClientSecret()
    {
        return $this->mode === 'production' ? $this->productionClientSecret : $this->sandboxClientSecret;
    }
}