<?php

namespace App\Controllers;

class Produkt extends BaseController
{
    public function index()
    {
        $request = service('request');
        $produktModel = new \App\Models\ProduktModel();

        // Filtern nach Kategorie
        $kategorie = $request->getGet('kategorie');
        if (!empty($kategorie)) {
            $produktModel->where('kategorie_id', $kategorie);
        }

        // Preisfilter
        $preis_min = $request->getGet('preis_min');
        if (!empty($preis_min)) {
            $produktModel->where('preis >=', $preis_min);
        }

        $preis_max = $request->getGet('preis_max');
        if (!empty($preis_max)) {
            $produktModel->where('preis <=', $preis_max);
        }

        // Nur aktive Produkte anzeigen
        $produktModel->where('ist_aktiv', true);

        // Sortierung
        $sortierung = $request->getGet('sortierung');
        switch($sortierung) {
            case 'name_asc':
                $produktModel->orderBy('name', 'ASC');
                break;
            case 'name_desc':
                $produktModel->orderBy('name', 'DESC');
                break;
            case 'preis_asc':
                $produktModel->orderBy('preis', 'ASC');
                break;
            case 'preis_desc':
                $produktModel->orderBy('preis', 'DESC');
                break;
            case 'neu':
                $produktModel->orderBy('erstellt_am', 'DESC');
                break;
            default:
                $produktModel->orderBy('name', 'ASC');
        }

        $data = [
            'title' => 'Produkte',
            'produkte' => $produktModel->findAll(),
        ];

        return view('templates/header', $data)
            . view('produkte/index', $data)
            . view('templates/footer');
    }

    public function kategorie($id)
    {
        $produktModel = new \App\Models\ProduktModel();
        $kategorieModel = new \App\Models\KategorieModel();

        $kategorie = $kategorieModel->find($id);

        if (!$kategorie) {
            return redirect()->to('/produkte')->with('error', 'Kategorie nicht gefunden');
        }

        $data = [
            'title' => $kategorie['name'],
            'kategorie' => $kategorie,
            'produkte' => $produktModel->getProduktByKategorie($id),
        ];

        return view('templates/header', $data)
            . view('produkte/kategorie', $data)
            . view('templates/footer');
    }

    public function detail($id)
    {
        $produktModel = new \App\Models\ProduktModel();
        $kategorieModel = new \App\Models\KategorieModel();

        $produkt = $produktModel->find($id);

        if (!$produkt) {
            return redirect()->to('/produkte')->with('error', 'Produkt nicht gefunden');
        }

        $kategorie = null;
        if (!empty($produkt['kategorie_id'])) {
            $kategorie = $kategorieModel->find($produkt['kategorie_id']);
        }

        $aehnlicheProdukte = [];
        if (!empty($produkt['kategorie_id'])) {
            $aehnlicheProdukte = $produktModel->getProduktByKategorie($produkt['kategorie_id']);
        }

        $data = [
            'title' => $produkt['name'],
            'produkt' => $produkt,
            'kategorie' => $kategorie,
            'aehnlicheProdukte' => $aehnlicheProdukte
        ];

        return view('templates/header', $data)
            . view('produkte/detail', $data)
            . view('templates/footer');
    }
}