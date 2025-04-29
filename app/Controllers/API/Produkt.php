<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;

class Produkt extends ResourceController
{
    protected $modelName = 'App\Models\ProduktModel';
    protected $format    = 'json';

    public function index()
    {
        $produkte = $this->model->findAll();
        return $this->respond($produkte);
    }

    public function detail($id = null)
    {
        $produkt = $this->model->find($id);

        if (!$produkt) {
            return $this->failNotFound('Produkt nicht gefunden');
        }

        return $this->respond($produkt);
    }

    public function erstellen()
    {
        $data = $this->request->getJSON(true);

        if (!$this->model->save($data)) {
            return $this->fail($this->model->errors());
        }

        $produkt_id = $this->model->getInsertID();
        $produkt = $this->model->find($produkt_id);

        return $this->respondCreated($produkt);
    }

    public function aktualisieren($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->model->find($id)) {
            return $this->failNotFound('Produkt nicht gefunden');
        }

        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors());
        }

        return $this->respond($this->model->find($id));
    }

    public function loeschen($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Produkt nicht gefunden');
        }

        if (!$this->model->delete($id)) {
            return $this->fail('Löschen fehlgeschlagen');
        }

        return $this->respondDeleted(['id' => $id]);
    }

    // Zusätzliche API-Endpunkte

    public function hervorgehoben()
    {
        $produkte = $this->model->getHervorgehobeneProdukte();
        return $this->respond($produkte);
    }

    public function neu()
    {
        $produkte = $this->model->orderBy('erstellt_am', 'DESC')
            ->where('ist_aktiv', true)
            ->limit(8)
            ->find();
        return $this->respond($produkte);
    }

    public function kategorie($kategorie_id)
    {
        $produkte = $this->model->getProduktByKategorie($kategorie_id);
        return $this->respond($produkte);
    }

    public function suche()
    {
        $suchbegriff = $this->request->getGet('q');

        if (empty($suchbegriff)) {
            return $this->fail('Kein Suchbegriff angegeben');
        }

        $produkte = $this->model->like('name', $suchbegriff)
            ->orLike('beschreibung', $suchbegriff)
            ->orLike('artikelnummer', $suchbegriff)
            ->where('ist_aktiv', true)
            ->find();

        return $this->respond($produkte);
    }

    public function lagerbestand($id)
    {
        $produkt = $this->model->find($id);

        if (!$produkt) {
            return $this->failNotFound('Produkt nicht gefunden');
        }

        return $this->respond([
            'id' => $produkt['id'],
            'name' => $produkt['name'],
            'bestand' => $produkt['bestand']
        ]);
    }
}