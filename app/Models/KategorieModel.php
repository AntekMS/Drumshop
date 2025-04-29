<?php namespace App\Models;

use CodeIgniter\Model;

class KategorieModel extends Model
{
    protected $table = 'kategorien';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'beschreibung', 'bild_url', 'eltern_id'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'erstellt_am';
    protected $updatedField = 'aktualisiert_am';
    protected $returnType = 'array';

    public function getHauptkategorien()
    {
        return $this->where('eltern_id IS NULL')
            ->findAll();
    }

    public function getUnterkategorien($eltern_id)
    {
        return $this->where('eltern_id', $eltern_id)
            ->findAll();
    }
}