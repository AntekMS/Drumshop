<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Produkt extends BaseController
{
    public function index()
    {
        $produktModel = new \App\Models\ProduktModel();

        $data = [
            'title' => 'Produkte verwalten',
            'produkte' => $produktModel->findAll()
        ];

        return view('admin/templates/header', $data)
            . view('admin/produkte/index', $data)
            . view('admin/templates/footer');
    }

    public function neu()
    {
        $kategorieModel = new \App\Models\KategorieModel();

        $data = [
            'title' => 'Neues Produkt',
            'kategorien' => $kategorieModel->findAll()
        ];

        return view('admin/templates/header', $data)
            . view('admin/produkte/form', $data)
            . view('admin/templates/footer');
    }

    public function speichern()
    {
        $produktModel = new \App\Models\ProduktModel();
        $request = $this->request;

        // Bilduploads verarbeiten
        $bild = $request->getFile('bild');
        $bild_url = '';

        if ($bild && $bild->isValid() && !$bild->hasMoved()) {
            $newName = $bild->getRandomName();
            $bild->move(ROOTPATH . 'public/assets/images/produkte', $newName);
            $bild_url = 'assets/images/produkte/' . $newName;
        }

        // Produkt-Daten speichern
        $data = [
            'name' => $request->getPost('name'),
            'beschreibung' => $request->getPost('beschreibung'),
            'preis' => $request->getPost('preis'),
            'bestand' => $request->getPost('bestand'),
            'kategorie_id' => $request->getPost('kategorie_id'),
            'artikelnummer' => $request->getPost('artikelnummer'),
            'gewicht' => $request->getPost('gewicht'),
            'abmessungen' => $request->getPost('abmessungen'),
            'hervorgehoben' => $request->getPost('hervorgehoben') ? 1 : 0,
            'ist_aktiv' => $request->getPost('ist_aktiv') ? 1 : 0
        ];

        if (!empty($bild_url)) {
            $data['bild_url'] = $bild_url;
        }

        $produktModel->insert($data);

        return redirect()->to('admin/produkte')
            ->with('success', 'Produkt erfolgreich erstellt');
    }

    public function bearbeiten($id)
    {
        $produktModel = new \App\Models\ProduktModel();
        $kategorieModel = new \App\Models\KategorieModel();

        $produkt = $produktModel->find($id);

        if (!$produkt) {
            return redirect()->to('admin/produkte')
                ->with('error', 'Produkt nicht gefunden');
        }

        $data = [
            'title' => 'Produkt bearbeiten',
            'produkt' => $produkt,
            'kategorien' => $kategorieModel->findAll()
        ];

        return view('admin/templates/header', $data)
            . view('admin/produkte/form', $data)
            . view('admin/templates/footer');
    }

    public function aktualisieren($id)
    {
        $produktModel = new \App\Models\ProduktModel();
        $request = $this->request;

        $produkt = $produktModel->find($id);

        if (!$produkt) {
            return redirect()->to('admin/produkte')
                ->with('error', 'Produkt nicht gefunden');
        }

        // Bilduploads verarbeiten
        $bild = $request->getFile('bild');
        $bild_url = $produkt['bild_url'];

        if ($bild && $bild->isValid() && !$bild->hasMoved()) {
            // Altes Bild löschen, wenn vorhanden
            if (!empty($produkt['bild_url']) && file_exists(ROOTPATH . 'public/' . $produkt['bild_url'])) {
                unlink(ROOTPATH . 'public/' . $produkt['bild_url']);
            }

            $newName = $bild->getRandomName();
            $bild->move(ROOTPATH . 'public/assets/images/produkte', $newName);
            $bild_url = 'assets/images/produkte/' . $newName;
        }

        // Produkt-Daten aktualisieren
        $data = [
            'name' => $request->getPost('name'),
            'beschreibung' => $request->getPost('beschreibung'),
            'preis' => $request->getPost('preis'),
            'bestand' => $request->getPost('bestand'),
            'kategorie_id' => $request->getPost('kategorie_id'),
            'artikelnummer' => $request->getPost('artikelnummer'),
            'gewicht' => $request->getPost('gewicht'),
            'abmessungen' => $request->getPost('abmessungen'),
            'hervorgehoben' => $request->getPost('hervorgehoben') ? 1 : 0,
            'ist_aktiv' => $request->getPost('ist_aktiv') ? 1 : 0
        ];

        if (!empty($bild_url)) {
            $data['bild_url'] = $bild_url;
        }

        $produktModel->update($id, $data);

        return redirect()->to('admin/produkte')
            ->with('success', 'Produkt erfolgreich aktualisiert');
    }

    public function loeschen($id)
    {
        $produktModel = new \App\Models\ProduktModel();

        $produkt = $produktModel->find($id);

        if (!$produkt) {
            return redirect()->to('admin/produkte')
                ->with('error', 'Produkt nicht gefunden');
        }

        // Prüfen ob in Bestellungen vorhanden
        $db = \Config\Database::connect();
        $inBestellung = $db->table('bestellpositionen')
            ->where('produkt_id', $id)
            ->countAllResults();

        if ($inBestellung > 0) {
            return redirect()->to('admin/produkte')
                ->with('error', 'Produkt kann nicht gelöscht werden, da es in Bestellungen verwendet wird');
        }

        // Bild löschen, wenn vorhanden
        if (!empty($produkt['bild_url']) && file_exists(ROOTPATH . 'public/' . $produkt['bild_url'])) {
            unlink(ROOTPATH . 'public/' . $produkt['bild_url']);
        }

        $produktModel->delete($id);

        return redirect()->to('admin/produkte')
            ->with('success', 'Produkt erfolgreich gelöscht');
    }
}