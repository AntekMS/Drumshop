<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Kategorie extends BaseController
{
    public function index()
    {
        $kategorieModel = new \App\Models\KategorieModel();
        $produktModel = new \App\Models\ProduktModel();

        $kategorien = $kategorieModel->findAll();

        // Berechne die Anzahl der Produkte pro Kategorie inklusive Unterkategorien
        $produkt_counts = [];

        // Hilfsfunktion zum rekursiven Sammeln aller Unterkategorien-IDs
        function getAlleUnterkategorienIds($kategorien, $eltern_id, &$ids = []) {
            $ids[] = $eltern_id;

            foreach ($kategorien as $kategorie) {
                if ($kategorie['eltern_id'] == $eltern_id) {
                    getAlleUnterkategorienIds($kategorien, $kategorie['id'], $ids);
                }
            }

            return $ids;
        }

        foreach ($kategorien as $kategorie) {
            // Sammle alle IDs dieser Kategorie und ihrer Unterkategorien
            $kategorie_ids = [];
            getAlleUnterkategorienIds($kategorien, $kategorie['id'], $kategorie_ids);

            // Zähle Produkte in dieser Kategorie und allen Unterkategorien
            $count = $produktModel->whereIn('kategorie_id', $kategorie_ids)->countAllResults();
            $produkt_counts[$kategorie['id']] = $count;
        }

        $data = [
            'title' => 'Kategorien verwalten',
            'kategorien' => $kategorien,
            'produkt_counts' => $produkt_counts  // Übergebe die Produktanzahl an die View
        ];

        return view('admin/templates/header', $data)
            . view('admin/kategorien/index', $data)
            . view('admin/templates/footer');
    }

    public function neu()
    {
        $kategorieModel = new \App\Models\KategorieModel();

        $data = [
            'title' => 'Neue Kategorie',
            'kategorien' => $kategorieModel->findAll() // Für Elternkategorie-Auswahl
        ];

        return view('admin/templates/header', $data)
            . view('admin/kategorien/form', $data)
            . view('admin/templates/footer');
    }

    public function speichern()
    {
        $kategorieModel = new \App\Models\KategorieModel();
        $request = $this->request;

        // Bilduploads verarbeiten
        $bild = $request->getFile('bild');
        $bild_url = '';

        if ($bild && $bild->isValid() && !$bild->hasMoved()) {
            $newName = $bild->getRandomName();
            $bild->move(ROOTPATH . 'public/assets/images/kategorien', $newName);
            $bild_url = 'assets/images/kategorien/' . $newName;
        }

        // Kategorie-Daten speichern
        $data = [
            'name' => $request->getPost('name'),
            'beschreibung' => $request->getPost('beschreibung'),
            'eltern_id' => $request->getPost('eltern_id') ?: null
        ];

        if (!empty($bild_url)) {
            $data['bild_url'] = $bild_url;
        }

        $kategorieModel->insert($data);

        return redirect()->to('admin/kategorien')
            ->with('success', 'Kategorie erfolgreich erstellt');
    }

    public function bearbeiten($id)
    {
        $kategorieModel = new \App\Models\KategorieModel();

        $kategorie = $kategorieModel->find($id);

        if (!$kategorie) {
            return redirect()->to('admin/kategorien')
                ->with('error', 'Kategorie nicht gefunden');
        }

        $data = [
            'title' => 'Kategorie bearbeiten',
            'kategorie' => $kategorie,
            'kategorien' => $kategorieModel->where('id !=', $id)->findAll() // Elternkategorien, aber nicht sich selbst
        ];

        return view('admin/templates/header', $data)
            . view('admin/kategorien/form', $data)
            . view('admin/templates/footer');
    }

    public function aktualisieren($id)
    {
        $kategorieModel = new \App\Models\KategorieModel();
        $request = $this->request;

        $kategorie = $kategorieModel->find($id);

        if (!$kategorie) {
            return redirect()->to('admin/kategorien')
                ->with('error', 'Kategorie nicht gefunden');
        }

        // Zirkuläre Abhängigkeit vermeiden
        $eltern_id = $request->getPost('eltern_id') ?: null;
        if ($eltern_id == $id) {
            return redirect()->back()
                ->with('error', 'Eine Kategorie kann nicht sich selbst als Elternkategorie haben');
        }

        // Bilduploads verarbeiten
        $bild = $request->getFile('bild');
        $bild_url = $kategorie['bild_url'];

        if ($bild && $bild->isValid() && !$bild->hasMoved()) {
            // Altes Bild löschen, wenn vorhanden
            if (!empty($kategorie['bild_url']) && file_exists(ROOTPATH . 'public/' . $kategorie['bild_url'])) {
                unlink(ROOTPATH . 'public/' . $kategorie['bild_url']);
            }

            $newName = $bild->getRandomName();
            $bild->move(ROOTPATH . 'public/assets/images/kategorien', $newName);
            $bild_url = 'assets/images/kategorien/' . $newName;
        }

        // Kategorie-Daten aktualisieren
        $data = [
            'name' => $request->getPost('name'),
            'beschreibung' => $request->getPost('beschreibung'),
            'eltern_id' => $eltern_id
        ];

        if (!empty($bild_url)) {
            $data['bild_url'] = $bild_url;
        }

        $kategorieModel->update($id, $data);

        return redirect()->to('admin/kategorien')
            ->with('success', 'Kategorie erfolgreich aktualisiert');
    }

    public function loeschen($id)
    {
        $kategorieModel = new \App\Models\KategorieModel();
        $produktModel = new \App\Models\ProduktModel();

        $kategorie = $kategorieModel->find($id);

        if (!$kategorie) {
            return redirect()->to('admin/kategorien')
                ->with('error', 'Kategorie nicht gefunden');
        }

        // Prüfen ob Produkte mit dieser Kategorie existieren
        $produkte = $produktModel->where('kategorie_id', $id)->findAll();

        if (!empty($produkte)) {
            return redirect()->to('admin/kategorien')
                ->with('error', 'Kategorie kann nicht gelöscht werden, da Produkte zugeordnet sind');
        }

        // Prüfen ob Unterkategorien existieren
        $unterkategorien = $kategorieModel->where('eltern_id', $id)->findAll();

        if (!empty($unterkategorien)) {
            return redirect()->to('admin/kategorien')
                ->with('error', 'Kategorie kann nicht gelöscht werden, da Unterkategorien existieren');
        }

        // Bild löschen, wenn vorhanden
        if (!empty($kategorie['bild_url']) && file_exists(ROOTPATH . 'public/' . $kategorie['bild_url'])) {
            unlink(ROOTPATH . 'public/' . $kategorie['bild_url']);
        }

        $kategorieModel->delete($id);

        return redirect()->to('admin/kategorien')
            ->with('success', 'Kategorie erfolgreich gelöscht');
    }
}