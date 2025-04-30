<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Bestellung extends BaseController
{
    public function index()
    {
        $bestellungModel = new \App\Models\BestellungModel();

        $data = [
            'title' => 'Bestellungen verwalten',
            'bestellungen' => $bestellungModel->orderBy('erstellt_am', 'DESC')->findAll()
        ];

        return view('admin/templates/header', $data)
            . view('admin/bestellungen/index', $data)
            . view('admin/templates/footer');
    }

    public function detail($id)
    {
        $bestellungModel = new \App\Models\BestellungModel();

        $bestellung = $bestellungModel->find($id);

        if (!$bestellung) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung nicht gefunden');
        }

        $positionen = $bestellungModel->getBestellpositionen($id);

        $data = [
            'title' => 'Bestellung #' . $id,
            'bestellung' => $bestellung,
            'positionen' => $positionen
        ];

        return view('admin/templates/header', $data)
            . view('admin/bestellungen/detail', $data)
            . view('admin/templates/footer');
    }

    public function bearbeiten($id)
    {
        $bestellungModel = new \App\Models\BestellungModel();

        $bestellung = $bestellungModel->find($id);

        if (!$bestellung) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung nicht gefunden');
        }

        $positionen = $bestellungModel->getBestellpositionen($id);

        $data = [
            'title' => 'Bestellung #' . $id . ' bearbeiten',
            'bestellung' => $bestellung,
            'positionen' => $positionen
        ];

        return view('admin/templates/header', $data)
            . view('admin/bestellungen/bearbeiten', $data)
            . view('admin/templates/footer');
    }

    public function aktualisieren($id)
    {
        $bestellungModel = new \App\Models\BestellungModel();
        $request = $this->request;

        $bestellung = $bestellungModel->find($id);

        if (!$bestellung) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung nicht gefunden');
        }

        // Basisdaten der Bestellung aktualisieren
        $data = [
            'kunde_name' => $request->getPost('kunde_name'),
            'kunde_email' => $request->getPost('kunde_email'),
            'lieferadresse' => $request->getPost('lieferadresse'),
            'zahlungsmethode' => $request->getPost('zahlungsmethode'),
            'anmerkungen' => $request->getPost('anmerkungen'),
            'status' => $request->getPost('status'),
            'zahlungsstatus' => $request->getPost('zahlungsstatus'),
            'sendungsnummer' => $request->getPost('sendungsnummer')
        ];

        // Daten in der Datenbank aktualisieren
        $bestellungModel->update($id, $data);

        // Bestellpositionen aktualisieren, wenn vorhanden
        $positionsmengen = $request->getPost('positionen');
        $positionspreise = $request->getPost('preise');

        if (is_array($positionsmengen)) {
            $db = \Config\Database::connect();
            $gesamtpreis = 0;

            foreach ($positionsmengen as $positions_id => $menge) {
                if (isset($positionspreise[$positions_id])) {
                    $einzelpreis = $positionspreise[$positions_id];
                    $zwischensumme = $einzelpreis * $menge;
                    $gesamtpreis += $zwischensumme;

                    // Position aktualisieren
                    $db->table('bestellpositionen')
                        ->where('id', $positions_id)
                        ->update([
                            'menge' => $menge,
                            'einzelpreis' => $einzelpreis,
                            'zwischensumme' => $zwischensumme
                        ]);
                }
            }

            // Gesamtpreis der Bestellung aktualisieren
            $bestellungModel->update($id, ['gesamtpreis' => $gesamtpreis]);
        }

        // Falls Status auf "versandt" gesetzt wurde, E-Mail an Kunden senden
        if ($data['status'] == 'versandt' && !empty($data['sendungsnummer']) && !empty($bestellung['kunde_email'])) {
            $this->sendeVersandbenachrichtigung($bestellung, $data['sendungsnummer']);
        }

        return redirect()->to('admin/bestellungen/detail/' . $id)
            ->with('success', 'Bestellung erfolgreich aktualisiert');
    }

    public function statusAendern($id)
    {
        $bestellungModel = new \App\Models\BestellungModel();
        $request = $this->request;

        $bestellung = $bestellungModel->find($id);

        if (!$bestellung) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung nicht gefunden');
        }

        $status = $request->getPost('status');
        $zahlungsstatus = $request->getPost('zahlungsstatus');
        $sendungsnummer = $request->getPost('sendungsnummer');

        $data = [];

        if ($status) {
            $data['status'] = $status;
        }

        if ($zahlungsstatus) {
            $data['zahlungsstatus'] = $zahlungsstatus;
        }

        if ($sendungsnummer) {
            $data['sendungsnummer'] = $sendungsnummer;
        }

        if (!empty($data)) {
            $bestellungModel->update($id, $data);

            // E-Mail-Benachrichtigung an Kunden senden
            if ($status == 'versandt' && !empty($bestellung['sendungsnummer']) && !empty($bestellung['kunde_email'])) {
                $this->sendeVersandbenachrichtigung($bestellung, $sendungsnummer);
            }

            return redirect()->to('admin/bestellungen/detail/' . $id)
                ->with('success', 'Bestellstatus erfolgreich aktualisiert');
        }

        return redirect()->to('admin/bestellungen/detail/' . $id)
            ->with('error', 'Keine Änderungen vorgenommen');
    }

    // Methode zum Stornieren einer Bestellung
    public function stornieren($id)
    {
        $bestellungModel = new \App\Models\BestellungModel();

        $bestellung = $bestellungModel->find($id);

        if (!$bestellung) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung nicht gefunden');
        }

        // Prüfen, ob Bestellung bereits storniert oder abgeschlossen ist
        if ($bestellung['status'] == 'storniert') {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung ist bereits storniert');
        }

        if ($bestellung['status'] == 'geliefert') {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Gelieferte Bestellungen können nicht storniert werden');
        }

        // Bestellung stornieren
        $bestellungModel->update($id, [
            'status' => 'storniert'
        ]);

        // Falls bezahlt, Rückerstattung vermerken
        if ($bestellung['zahlungsstatus'] == 'bezahlt') {
            $bestellungModel->update($id, [
                'zahlungsstatus' => 'zurückerstattet'
            ]);
        }

        // Benachrichtigung an Kunden senden
        if (!empty($bestellung['kunde_email'])) {
            $this->sendeStornierungsbenachrichtigung($bestellung);
        }

        return redirect()->to('admin/bestellungen')
            ->with('success', 'Bestellung wurde erfolgreich storniert');
    }

    // Alternative Stornierungsroute mit Bestätigung
    public function stornierungBestaetigen($id)
    {
        $bestellungModel = new \App\Models\BestellungModel();

        $bestellung = $bestellungModel->find($id);

        if (!$bestellung) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung nicht gefunden');
        }

        $data = [
            'title' => 'Bestellung stornieren',
            'bestellung' => $bestellung
        ];

        return view('admin/templates/header', $data)
            . view('admin/bestellungen/stornieren', $data)
            . view('admin/templates/footer');
    }

    // Eigentliche Stornierungsaktion nach Bestätigung
    public function stornierungDurchfuehren()
    {
        $request = $this->request;
        $id = $request->getPost('bestellung_id');

        if (empty($id)) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Ungültige Anfrage');
        }

        $bestellungModel = new \App\Models\BestellungModel();
        $bestellung = $bestellungModel->find($id);

        if (!$bestellung) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung nicht gefunden');
        }

        // Bestellung stornieren
        $bestellungModel->update($id, [
            'status' => 'storniert'
        ]);

        // Falls bezahlt, Rückerstattung vermerken
        if ($bestellung['zahlungsstatus'] == 'bezahlt') {
            $bestellungModel->update($id, [
                'zahlungsstatus' => 'zurückerstattet'
            ]);
        }

        // Bestandsaktualisierung für alle Positionen
        if ($request->getPost('bestand_zurueckbuchen') == '1') {
            $this->bestandZurueckbuchen($id);
        }

        // Benachrichtigung an Kunden senden
        $sendeEmail = $request->getPost('email_senden') == '1';
        if ($sendeEmail && !empty($bestellung['kunde_email'])) {
            $grund = $request->getPost('stornierung_grund');
            $this->sendeStornierungsbenachrichtigung($bestellung, $grund);
        }

        return redirect()->to('admin/bestellungen')
            ->with('success', 'Bestellung wurde erfolgreich storniert');
    }

    private function bestandZurueckbuchen($bestellung_id)
    {
        $bestellungModel = new \App\Models\BestellungModel();
        $db = \Config\Database::connect();

        // Bestellpositionen laden
        $positionen = $bestellungModel->getBestellpositionen($bestellung_id);

        // Produkt-Modell für Bestandsänderungen
        $produktModel = new \App\Models\ProduktModel();

        // Für jede Position den Bestand erhöhen
        foreach ($positionen as $position) {
            $produkt_id = $position['produkt_id'];
            $menge = $position['menge'];

            $produkt = $produktModel->find($produkt_id);
            if ($produkt) {
                // Bestand erhöhen
                $neuerBestand = $produkt['bestand'] + $menge;
                $produktModel->update($produkt_id, ['bestand' => $neuerBestand]);
            }
        }
    }

    public function loeschen($id)
    {
        $bestellungModel = new \App\Models\BestellungModel();
        $db = \Config\Database::connect();

        $bestellung = $bestellungModel->find($id);

        if (!$bestellung) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung nicht gefunden');
        }

        // Transaktion starten
        $db->transStart();

        // Erst Bestellpositionen löschen
        $db->table('bestellpositionen')
            ->where('bestellung_id', $id)
            ->delete();

        // Dann die Bestellung selbst löschen
        $bestellungModel->delete($id);

        // Transaktion abschließen
        $db->transComplete();

        // Prüfen, ob die Transaktion erfolgreich war
        if ($db->transStatus() === false) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung konnte nicht gelöscht werden');
        }

        return redirect()->to('admin/bestellungen')
            ->with('success', 'Bestellung wurde erfolgreich gelöscht');
    }

    public function email($id)
    {
        $bestellungModel = new \App\Models\BestellungModel();

        $bestellung = $bestellungModel->find($id);

        if (!$bestellung) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung nicht gefunden');
        }

        $positionen = $bestellungModel->getBestellpositionen($id);

        $data = [
            'title' => 'E-Mail an Kunden: Bestellung #' . $id,
            'bestellung' => $bestellung,
            'positionen' => $positionen
        ];

        return view('admin/templates/header', $data)
            . view('admin/bestellungen/email', $data)
            . view('admin/templates/footer');
    }

    public function emailSenden($id)
    {
        $bestellungModel = new \App\Models\BestellungModel();
        $request = $this->request;

        $bestellung = $bestellungModel->find($id);

        if (!$bestellung) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung nicht gefunden');
        }

        $betreff = $request->getPost('betreff');
        $nachricht = $request->getPost('nachricht');

        if (empty($betreff) || empty($nachricht)) {
            return redirect()->to('admin/bestellungen/email/' . $id)
                ->with('error', 'Betreff und Nachricht sind erforderlich');
        }

        $email = \Config\Services::email();

        $email->setFrom('info@drumshop.de', 'DrumShop');
        $email->setTo($bestellung['kunde_email']);
        $email->setSubject($betreff);
        $email->setMessage($nachricht);

        // E-Mail senden (im Produktivsystem aktivieren)
        // if ($email->send()) {
        //     return redirect()->to('admin/bestellungen/detail/' . $id)
        //         ->with('success', 'E-Mail wurde erfolgreich versendet');
        // } else {
        //     return redirect()->to('admin/bestellungen/email/' . $id)
        //         ->with('error', 'E-Mail konnte nicht versendet werden: ' . $email->printDebugger(['headers']));
        // }

        // Für Demozwecke: Immer Erfolg melden
        return redirect()->to('admin/bestellungen/detail/' . $id)
            ->with('success', 'E-Mail wurde erfolgreich versendet');
    }

    public function rechnung($id)
    {
        $bestellungModel = new \App\Models\BestellungModel();

        $bestellung = $bestellungModel->find($id);

        if (!$bestellung) {
            return redirect()->to('admin/bestellungen')
                ->with('error', 'Bestellung nicht gefunden');
        }

        $positionen = $bestellungModel->getBestellpositionen($id);

        $data = [
            'title' => 'Rechnung: Bestellung #' . $id,
            'bestellung' => $bestellung,
            'positionen' => $positionen,
            'rechnungsnummer' => 'RE-' . date('Y') . '-' . str_pad($bestellung['id'], 5, '0', STR_PAD_LEFT),
            'rechnungsdatum' => date('d.m.Y')
        ];

        return view('admin/templates/header', $data)
            . view('admin/bestellungen/rechnung', $data)
            . view('admin/templates/footer');
    }

    private function sendeVersandbenachrichtigung($bestellung, $sendungsnummer)
    {
        $email = \Config\Services::email();

        $email->setFrom('info@drumshop.de', 'DrumShop');
        $email->setTo($bestellung['kunde_email']);

        $email->setSubject('Ihre Bestellung #' . $bestellung['id'] . ' wurde versandt');

        $nachricht = "Sehr geehrte(r) " . $bestellung['kunde_name'] . ",\n\n";
        $nachricht .= "Ihre Bestellung #" . $bestellung['id'] . " wurde versandt.\n\n";
        $nachricht .= "Sendungsnummer: " . $sendungsnummer . "\n\n";
        $nachricht .= "Sie können den Status Ihrer Sendung jederzeit über unsere Website verfolgen.\n\n";
        $nachricht .= "Vielen Dank für Ihren Einkauf bei DrumShop!\n\n";
        $nachricht .= "Mit freundlichen Grüßen\n";
        $nachricht .= "Ihr DrumShop-Team";

        $email->setMessage($nachricht);

        // E-Mail senden (im Produktivsystem aktivieren)
        // $email->send();
    }

    private function sendeStornierungsbenachrichtigung($bestellung, $grund = '')
    {
        $email = \Config\Services::email();

        $email->setFrom('info@drumshop.de', 'DrumShop');
        $email->setTo($bestellung['kunde_email']);

        $email->setSubject('Ihre Bestellung #' . $bestellung['id'] . ' wurde storniert');

        $nachricht = "Sehr geehrte(r) " . $bestellung['kunde_name'] . ",\n\n";
        $nachricht .= "Ihre Bestellung #" . $bestellung['id'] . " wurde storniert.\n\n";

        if (!empty($grund)) {
            $nachricht .= "Grund der Stornierung: " . $grund . "\n\n";
        }

        if ($bestellung['zahlungsstatus'] == 'bezahlt') {
            $nachricht .= "Der Kaufbetrag wird in Kürze auf Ihrem Konto gutgeschrieben.\n\n";
        }

        $nachricht .= "Bei Fragen zu dieser Stornierung kontaktieren Sie uns bitte unter info@drumshop.de\n\n";
        $nachricht .= "Mit freundlichen Grüßen\n";
        $nachricht .= "Ihr DrumShop-Team";

        $email->setMessage($nachricht);

        // E-Mail senden (im Produktivsystem aktivieren)
        // $email->send();
    }
}