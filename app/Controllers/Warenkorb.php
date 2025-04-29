<?php

namespace App\Controllers;

class Warenkorb extends BaseController
{
    public function index()
    {
        $session = session();
        $warenkorbModel = new \App\Models\WarenkorbModel();

        // Session-ID erstellen, falls nicht vorhanden
        if (!$session->has('session_id')) {
            $session->set('session_id', session_id());
        }

        $warenkorb = $warenkorbModel->getWarenkorbBySession($session->get('session_id'));

        if (!$warenkorb) {
            $data = [
                'title' => 'Warenkorb',
                'warenkorb' => null,
                'positionen' => [],
                'gesamtpreis' => 0
            ];
        } else {
            $positionen = $warenkorbModel->getWarenkorbPositionen($warenkorb['id']);
            $gesamtpreis = 0;

            foreach ($positionen as $position) {
                $gesamtpreis += $position['preis'] * $position['menge'];
            }

            $data = [
                'title' => 'Warenkorb',
                'warenkorb' => $warenkorb,
                'positionen' => $positionen,
                'gesamtpreis' => $gesamtpreis
            ];
        }

        return view('templates/header', $data)
            . view('warenkorb/index', $data)
            . view('templates/footer');
    }

    public function hinzufuegen()
    {
        $session = session();
        $request = $this->request;
        $warenkorbModel = new \App\Models\WarenkorbModel();
        $produktModel = new \App\Models\ProduktModel();

        // Session-ID erstellen, falls nicht vorhanden
        if (!$session->has('session_id')) {
            $session->set('session_id', session_id());
        }

        $produkt_id = $request->getPost('produkt_id');
        $menge = $request->getPost('menge') ?? 1;

        // Produkt laden
        $produkt = $produktModel->find($produkt_id);

        if (!$produkt) {
            return redirect()->back()->with('error', 'Produkt nicht gefunden');
        }

        // Prüfen ob genug auf Lager
        if ($produkt['bestand'] < $menge) {
            return redirect()->back()->with('error', 'Nicht genügend Produkte auf Lager. Verfügbar: ' . $produkt['bestand']);
        }

        // Warenkorb für Session laden oder erstellen
        $warenkorb = $warenkorbModel->getWarenkorbBySession($session->get('session_id'));

        if (!$warenkorb) {
            $warenkorb_id = $warenkorbModel->insert(['session_id' => $session->get('session_id')]);
            $warenkorb = $warenkorbModel->find($warenkorb_id);
        }

        // Produkt zum Warenkorb hinzufügen
        $warenkorbModel->addWarenkorbPosition(
            $warenkorb['id'],
            $produkt['id'],
            $produkt['name'],
            $menge,
            $produkt['preis']
        );

        return redirect()->to('warenkorb')->with('success', 'Produkt zum Warenkorb hinzugefügt');
    }

    public function aktualisieren()
    {
        $request = $this->request;
        $db = \Config\Database::connect();

        $positionen = $request->getPost('positionen');

        if (is_array($positionen)) {
            foreach ($positionen as $id => $menge) {
                if ($menge > 0) {
                    $db->table('warenkorb_positionen')
                        ->where('id', $id)
                        ->update([
                            'menge' => $menge,
                            'aktualisiert_am' => date('Y-m-d H:i:s')
                        ]);
                } else {
                    $db->table('warenkorb_positionen')
                        ->where('id', $id)
                        ->delete();
                }
            }
        }

        return redirect()->to('warenkorb')->with('success', 'Warenkorb aktualisiert');
    }

    public function entfernen($id)
    {
        $db = \Config\Database::connect();

        $db->table('warenkorb_positionen')
            ->where('id', $id)
            ->delete();

        return redirect()->to('warenkorb')->with('success', 'Produkt aus Warenkorb entfernt');
    }
}