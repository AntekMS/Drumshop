<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $produktModel = new \App\Models\ProduktModel();
        $kategorieModel = new \App\Models\KategorieModel();

        $data = [
            'title' => 'Willkommen',
            'hervorgehobeneProdukte' => $produktModel->getHervorgehobeneProdukte(),
            'neueProdukte' => $produktModel->getAktiveProdukte(8),
            'kategorien' => $kategorieModel->getHauptkategorien()
        ];

        return view('templates/header', $data)
            . view('home', $data)
            . view('templates/footer');
    }
}