<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Produkt extends BaseController
{
    public function index()
    {
        $produktModel = new \App\Models\ProduktModel();
        $kategorieModel = new \App\Models\KategorieModel();

        // Alle Produkte abrufen
        $produkte = $produktModel->findAll();

        // Alle Kategorien abrufen für die Anzeige der Kategorienamen
        $kategorien = $kategorieModel->findAll();

        // Kategorien für schnelleren Lookup in ein assoziatives Array umwandeln
        $kategorien_lookup = [];
        foreach ($kategorien as $kategorie) {
            $kategorien_lookup[$kategorie['id']] = $kategorie;
        }

        $data = [
            'title' => 'Produkte verwalten',
            'produkte' => $produkte,
            'kategorien' => $kategorien,
            'kategorien_lookup' => $kategorien_lookup
        ];

        return view('admin/templates/header', $data)
            . view('admin/produkte/index', $data)
            . view('admin/templates/footer');
    }

    public function neu()
    {
        $kategorieModel = new \App\Models\KategorieModel();

        // Kategorien hierarchisch aufbereiten für das Dropdown
        $kategorien = $kategorieModel->findAll();
        $hierarchische_kategorien = $this->bereiteKategorienHierarchieVor($kategorien);

        $data = [
            'title' => 'Neues Produkt',
            'kategorien' => $hierarchische_kategorien
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

        // Kategorien hierarchisch aufbereiten für das Dropdown
        $kategorien = $kategorieModel->findAll();
        $hierarchische_kategorien = $this->bereiteKategorienHierarchieVor($kategorien);

        $data = [
            'title' => 'Produkt bearbeiten',
            'produkt' => $produkt,
            'kategorien' => $hierarchische_kategorien
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

    /**
     * Hilfsmethode, um die Kategorien für ein Dropdown hierarchisch vorzubereiten
     *
     * @param array $kategorien Alle Kategorien aus der Datenbank
     * @param int|null $eltern_id ID der Elternkategorie für die aktuelle Rekursionsebene
     * @param int $ebene Verschachtelungstiefe für die Einrückung im Dropdown
     * @return array Hierarchisch aufbereitete Kategorien für das Dropdown
     */
    private function bereiteKategorienHierarchieVor($kategorien, $eltern_id = null, $ebene = 0)
    {
        $ergebnis = [];

        foreach ($kategorien as $kategorie) {
            if ($kategorie['eltern_id'] == $eltern_id) {
                // Kategorie-Name mit Einrückung versehen
                $einrueckung = str_repeat('— ', $ebene);
                $kategorie['name_formatiert'] = $einrueckung . $kategorie['name'];

                // Hauptkategorie zum Ergebnis hinzufügen
                $ergebnis[] = $kategorie;

                // Rekursiv Unterkategorien hinzufügen
                $unterkategorien = $this->bereiteKategorienHierarchieVor($kategorien, $kategorie['id'], $ebene + 1);
                $ergebnis = array_merge($ergebnis, $unterkategorien);
            }
        }

        return $ergebnis;
    }
}