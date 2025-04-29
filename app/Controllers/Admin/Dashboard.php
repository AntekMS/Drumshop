<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $bestellungModel = new \App\Models\BestellungModel();
        $produktModel = new \App\Models\ProduktModel();
        $kategorieModel = new \App\Models\KategorieModel();
        $db = \Config\Database::connect();

        // Statistiken für Dashboard
        $data = [
            'title' => 'Dashboard',
            'bestellungen_gesamt' => $bestellungModel->countAll(),
            'bestellungen_neu' => $bestellungModel->where('status', 'neu')->countAllResults(),
            'umsatz_gesamt' => $bestellungModel->selectSum('gesamtpreis')->where('zahlungsstatus', 'bezahlt')->first()['gesamtpreis'] ?? 0,
            'produkte_gesamt' => $produktModel->countAll(),
            'produkte_lagernd' => $produktModel->where('bestand >', 0)->countAllResults(),
            'produkte_ohne_bestand' => $produktModel->where('bestand', 0)->countAllResults(),
            'kategorieModel' => $kategorieModel,

            // Letzte Bestellungen
            'letzte_bestellungen' => $bestellungModel->orderBy('erstellt_am', 'DESC')->limit(5)->find(),

            // Bestseller
            'bestseller' => $db->table('bestellpositionen')
                ->select('produkt_id, produkt_name, SUM(menge) as gesamt_verkauft')
                ->groupBy('produkt_id')
                ->orderBy('gesamt_verkauft', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray()
        ];

        return view('admin/templates/header', $data)
            . view('admin/dashboard', $data)
            . view('admin/templates/footer');
    }
}