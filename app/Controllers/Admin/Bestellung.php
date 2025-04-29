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
}