<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;

class Bestellung extends ResourceController
{
    protected $modelName = 'App\Models\BestellungModel';
    protected $format    = 'json';

    public function index()
    {
        // Nur für authentifizierte Benutzer
        // TODO: API-Authentifizierung implementieren
        $bestellungen = $this->model->findAll();
        return $this->respond($bestellungen);
    }

    public function detail($id = null)
    {
        $bestellung = $this->model->find($id);

        if (!$bestellung) {
            return $this->failNotFound('Bestellung nicht gefunden');
        }

        // Bestellpositionen laden
        $bestellung['positionen'] = $this->model->getBestellpositionen($id);

        return $this->respond($bestellung);
    }

    public function erstellen()
    {
        $data = $this->request->getJSON(true);

        // Warenkorb-Positionen laden
        $db = \Config\Database::connect();
        $warenkorb_positionen = $db->table('warenkorb_positionen')
            ->where('warenkorb_id', $data['warenkorb_id'])
            ->get()
            ->getResultArray();

        if (empty($warenkorb_positionen)) {
            return $this->fail('Warenkorb ist leer');
        }

        // Gesamtpreis berechnen
        $gesamtpreis = 0;
        foreach ($warenkorb_positionen as $position) {
            $gesamtpreis += $position['preis'] * $position['menge'];
        }

        $bestellungsdaten = [
            'kunde_name' => $data['kunde_name'],
            'kunde_email' => $data['kunde_email'],
            'gesamtpreis' => $gesamtpreis,
            'lieferadresse' => $data['lieferadresse'],
            'zahlungsmethode' => $data['zahlungsmethode'],
            'zahlungsstatus' => 'ausstehend',
            'anmerkungen' => $data['anmerkungen'] ?? ''
        ];

        // Bestellung erstellen
        $bestellung_id = $this->model->erstelleBestellung($bestellungsdaten, $warenkorb_positionen);

        // Warenkorb leeren
        $db->table('warenkorb_positionen')
            ->where('warenkorb_id', $data['warenkorb_id'])
            ->delete();

        return $this->respondCreated(['id' => $bestellung_id]);
    }

    public function kundenbestellungen($email)
    {
        // Bestellungen eines Kunden abrufen
        $bestellungen = $this->model->where('kunde_email', $email)
            ->orderBy('erstellt_am', 'DESC')
            ->findAll();

        // Sicherheitsüberprüfung
        // TODO: API-Authentifizierung implementieren

        return $this->respond($bestellungen);
    }

    public function status($id)
    {
        $bestellung = $this->model->find($id);

        if (!$bestellung) {
            return $this->failNotFound('Bestellung nicht gefunden');
        }

        return $this->respond([
            'id' => $bestellung['id'],
            'status' => $bestellung['status'],
            'zahlungsstatus' => $bestellung['zahlungsstatus'],
            'sendungsnummer' => $bestellung['sendungsnummer']
        ]);
    }

    public function statusAendern($id)
    {
        $data = $this->request->getJSON(true);

        $bestellung = $this->model->find($id);

        if (!$bestellung) {
            return $this->failNotFound('Bestellung nicht gefunden');
        }

        // Sicherheitsüberprüfung
        // TODO: API-Authentifizierung implementieren

        $updateData = [];

        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }

        if (isset($data['zahlungsstatus'])) {
            $updateData['zahlungsstatus'] = $data['zahlungsstatus'];
        }

        if (isset($data['sendungsnummer'])) {
            $updateData['sendungsnummer'] = $data['sendungsnummer'];
        }

        if (empty($updateData)) {
            return $this->fail('Keine Daten zum Aktualisieren');
        }

        if (!$this->model->update($id, $updateData)) {
            return $this->fail($this->model->errors());
        }

        return $this->respond($this->model->find($id));
    }

    public function stornieren($id)
    {
        $bestellung = $this->model->find($id);

        if (!$bestellung) {
            return $this->failNotFound('Bestellung nicht gefunden');
        }

        // Sicherheitsüberprüfung
        // TODO: API-Authentifizierung implementieren

        // Nur stornieren, wenn noch nicht versandt
        if ($bestellung['status'] == 'versandt' || $bestellung['status'] == 'geliefert') {
            return $this->fail('Bestellung kann nicht mehr storniert werden');
        }

        if (!$this->model->update($id, ['status' => 'storniert'])) {
            return $this->fail($this->model->errors());
        }

        return $this->respond(['status' => 'storniert']);
    }
}