<?php namespace App\Models;

use CodeIgniter\Model;

class ProduktModel extends Model
{
    protected $table = 'produkte';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'beschreibung', 'preis', 'bestand', 'bild_url',
        'kategorie_id', 'artikelnummer', 'gewicht', 'abmessungen',
        'hervorgehoben', 'ist_aktiv'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'erstellt_am';
    protected $updatedField = 'aktualisiert_am';
    protected $returnType = 'array';

    public function getAktiveProdukte($limit = null)
    {
        return $this->where('ist_aktiv', true)
            ->limit($limit)
            ->findAll();
    }

    public function getHervorgehobeneProdukte()
    {
        return $this->where('hervorgehoben', true)
            ->where('ist_aktiv', true)
            ->findAll();
    }

    public function getProduktByKategorie($kategorie_id)
    {
        return $this->where('kategorie_id', $kategorie_id)
            ->where('ist_aktiv', true)
            ->findAll();
    }
}