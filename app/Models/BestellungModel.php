<?php namespace App\Models;

use CodeIgniter\Model;

class BestellungModel extends Model
{
    protected $table = 'bestellungen';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kunde_name', 'kunde_email', 'gesamtpreis', 'status',
        'lieferadresse', 'zahlungsmethode', 'zahlungsstatus',
        'sendungsnummer', 'anmerkungen'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'erstellt_am';
    protected $updatedField = 'aktualisiert_am';
    protected $returnType = 'array';

    public function getBestellpositionen($bestellung_id)
    {
        $db = \Config\Database::connect();

        return $db->table('bestellpositionen')
            ->where('bestellung_id', $bestellung_id)
            ->get()
            ->getResultArray();
    }

    public function erstelleBestellung($data, $warenkorb_positionen)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Bestellung einfügen
        $this->insert($data);
        $bestellung_id = $this->insertID();

        // Bestellpositionen einfügen
        foreach ($warenkorb_positionen as $position) {
            $db->table('bestellpositionen')->insert([
                'bestellung_id' => $bestellung_id,
                'produkt_id' => $position['produkt_id'],
                'produkt_name' => $position['produkt_name'],
                'menge' => $position['menge'],
                'einzelpreis' => $position['preis'],
                'zwischensumme' => $position['preis'] * $position['menge']
            ]);

            // Bestand aktualisieren
            $db->table('produkte')
                ->where('id', $position['produkt_id'])
                ->set('bestand', 'bestand - ' . $position['menge'], false)
                ->update();
        }

        $db->transComplete();

        return $bestellung_id;
    }
}