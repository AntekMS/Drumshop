<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Produkt extends BaseController
{
    public function index()
    {
        $produktModel = new \App\Models\ProduktModel();
        $kategorieModel = new \App\Models\KategorieModel();

        // Filter anwenden (falls vorhanden)
        $request = $this->request;

        // Nur nach Namen filtern, wenn ein Suchbegriff eingegeben wurde
        if ($request->getGet('name')) {
            $produktModel->like('name', $request->getGet('name'));
        }

        // Nach Kategorie filtern
        if ($request->getGet('kategorie')) {
            $produktModel->where('kategorie_id', $request->getGet('kategorie'));
        }

        // Nach Preisbereich filtern
        if ($request->getGet('preis_min')) {
            $produktModel->where('preis >=', $request->getGet('preis_min'));
        }

        if ($request->getGet('preis_max')) {
            $produktModel->where('preis <=', $request->getGet('preis_max'));
        }

        // Nach Bestand filtern
        if ($request->getGet('bestand')) {
            switch ($request->getGet('bestand')) {
                case 'auf_lager':
                    $produktModel->where('bestand >', 0);
                    break;
                case 'niedrig':
                    $produktModel->where('bestand >', 0)->where('bestand <', 5);
                    break;
                case 'ausverkauft':
                    $produktModel->where('bestand', 0);
                    break;
            }
        }

        // Nach Status filtern
        if ($request->getGet('status')) {
            switch ($request->getGet('status')) {
                case 'aktiv':
                    $produktModel->where('ist_aktiv', 1);
                    break;
                case 'inaktiv':
                    $produktModel->where('ist_aktiv', 0);
                    break;
                case 'hervorgehoben':
                    $produktModel->where('hervorgehoben', 1);
                    break;
            }
        }

        // Sortierung anwenden
        $sortierung = $request->getGet('sortierung') ?? 'name_asc';
        switch ($sortierung) {
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
            case 'bestand_asc':
                $produktModel->orderBy('bestand', 'ASC');
                break;
            case 'bestand_desc':
                $produktModel->orderBy('bestand', 'DESC');
                break;
            case 'neu':
                $produktModel->orderBy('erstellt_am', 'DESC');
                break;
        }

        // Daten für die View vorbereiten
        $data = [
            'title' => 'Produkte verwalten',
            'produkte' => $produktModel->findAll(),
            'kategorien' => $kategorieModel->findAll()
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

        // Checkbox-Werte verarbeiten
        $ist_aktiv = $request->getPost('ist_aktiv') ? 1 : 0;
        $hervorgehoben = $request->getPost('hervorgehoben') ? 1 : 0;

        // Produkt-Daten speichern
        $data = [
            'name' => $request->getPost('name'),
            'beschreibung' => $request->getPost('beschreibung'),
            'preis' => $request->getPost('preis'),
            'bestand' => $request->getPost('bestand'),
            'kategorie_id' => $request->getPost('kategorie_id') ?: null,
            'artikelnummer' => $request->getPost('artikelnummer'),
            'gewicht' => $request->getPost('gewicht') ?: null,
            'abmessungen' => $request->getPost('abmessungen') ?: null,
            'hervorgehoben' => $hervorgehoben,
            'ist_aktiv' => $ist_aktiv
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

        // Checkbox-Werte verarbeiten
        $ist_aktiv = $request->getPost('ist_aktiv') ? 1 : 0;
        $hervorgehoben = $request->getPost('hervorgehoben') ? 1 : 0;

        // Produkt-Daten aktualisieren
        $data = [
            'name' => $request->getPost('name'),
            'beschreibung' => $request->getPost('beschreibung'),
            'preis' => $request->getPost('preis'),
            'bestand' => $request->getPost('bestand'),
            'kategorie_id' => $request->getPost('kategorie_id') ?: null,
            'artikelnummer' => $request->getPost('artikelnummer'),
            'gewicht' => $request->getPost('gewicht') ?: null,
            'abmessungen' => $request->getPost('abmessungen') ?: null,
            'hervorgehoben' => $hervorgehoben,
            'ist_aktiv' => $ist_aktiv
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

    public function massenAktion()
    {
        $request = $this->request;
        $produktModel = new \App\Models\ProduktModel();
        $db = \Config\Database::connect();
        $builder = $db->table('produkte');

        $aktion = $request->getPost('aktion');
        $filter = $request->getPost('filter');

        if (empty($aktion) || empty($filter)) {
            return redirect()->to('admin/produkte')
                ->with('error', 'Ungültige Eingabe');
        }

        // Passende Produkte ermitteln
        switch ($filter) {
            case 'alle':
                // Alle Produkte auswählen - kein zusätzlicher Filter
                break;

            case 'ausgewaehlte':
                // Ausgewählte Produkte (per Checkbox) - diese Funktion müsste erweitert werden
                $selected = $request->getPost('selected');
                if (empty($selected)) {
                    return redirect()->to('admin/produkte')
                        ->with('error', 'Keine Produkte ausgewählt');
                }
                $builder->whereIn('id', $selected);
                break;

            case 'ohne_bestand':
                $builder->where('bestand', 0);
                break;

            case 'inaktiv':
                $builder->where('ist_aktiv', 0);
                break;

            case 'kategorie':
                $kategorie_id = $request->getPost('kategorie_id');
                if (empty($kategorie_id)) {
                    return redirect()->to('admin/produkte')
                        ->with('error', 'Keine Kategorie ausgewählt');
                }
                $builder->where('kategorie_id', $kategorie_id);
                break;

            default:
                return redirect()->to('admin/produkte')
                    ->with('error', 'Ungültiger Filter');
        }

        // Aktion ausführen
        switch ($aktion) {
            case 'aktivieren':
                $builder->set('ist_aktiv', 1);
                $builder->update();
                break;

            case 'deaktivieren':
                $builder->set('ist_aktiv', 0);
                $builder->update();
                break;

            case 'hervorheben':
                $builder->set('hervorgehoben', 1);
                $builder->update();
                break;

            case 'entfernen_hervorgehoben':
                $builder->set('hervorgehoben', 0);
                $builder->update();
                break;

            case 'bestand_aendern':
                $bestand = $request->getPost('bestand');
                if ($bestand === null) {
                    return redirect()->to('admin/produkte')
                        ->with('error', 'Kein Bestand angegeben');
                }
                $builder->set('bestand', $bestand);
                $builder->update();
                break;

            case 'loeschen':
                // IDs ermitteln
                $produkteZumLoeschen = $builder->get()->getResultArray();

                // Prüfen, ob Produkte in Bestellungen vorhanden sind
                $idsZumLoeschen = array_column($produkteZumLoeschen, 'id');
                $inBestellung = $db->table('bestellpositionen')
                    ->whereIn('produkt_id', $idsZumLoeschen)
                    ->countAllResults();

                if ($inBestellung > 0) {
                    return redirect()->to('admin/produkte')
                        ->with('error', 'Einige Produkte können nicht gelöscht werden, da sie in Bestellungen verwendet werden');
                }

                // Bilder löschen
                foreach ($produkteZumLoeschen as $produkt) {
                    if (!empty($produkt['bild_url']) && file_exists(ROOTPATH . 'public/' . $produkt['bild_url'])) {
                        unlink(ROOTPATH . 'public/' . $produkt['bild_url']);
                    }
                }

                // Produkte löschen
                $builder->delete();
                break;

            default:
                return redirect()->to('admin/produkte')
                    ->with('error', 'Ungültige Aktion');
        }

        return redirect()->to('admin/produkte')
            ->with('success', 'Massenaktion erfolgreich durchgeführt');
    }
}