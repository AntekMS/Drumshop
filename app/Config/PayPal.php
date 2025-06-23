<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class PayPal extends BaseConfig
{
   
    public $mode = 'sandbox';

    public $sandboxClientId = 'AVdathZPObWjwHc-I1t0Ci-uHvEquR1Jpo0b9UFkIjz9fytGYdnJzOsUaavmvzXlgKifKhZilqnXIDiw';
    public $sandboxClientSecret = 'EH7gmdILheL-Oco5mIVwzeYYsmWYmoooiysqSMCzpgnr45gRfv-5RCGJqlLWZoeDtMnKtEhol3NVykBi';

    public $productionClientId = '';
    public $productionClientSecret = '';

    public $webhookId = '';


    public $sandboxUrl = 'https://api-m.sandbox.paypal.com/';
    public $productionUrl = 'https://api-m.paypal.com/';


    public function getBaseUrl()
    {
        return $this->mode === 'production' ? $this->productionUrl : $this->sandboxUrl;
    }


    public function getClientId()
    {
        return $this->mode === 'production' ? $this->productionClientId : $this->sandboxClientId;
    }

   
    public function getClientSecret()
    {
        return $this->mode === 'production' ? $this->productionClientSecret : $this->sandboxClientSecret;
    }
}
