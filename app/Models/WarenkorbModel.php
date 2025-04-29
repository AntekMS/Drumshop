<?php namespace App\Models;

use CodeIgniter\Model;

class WarenkorbModel extends Model
{
    protected $table = 'warenkorb';
    protected $primaryKey = 'id';
    protected $allowedFields = ['session_id'];
    protected $useTimestamps = true;
    protected $createdField = 'erstellt_am';
    protected $updatedField = 'aktualisiert_am';
    protected $returnType = 'array';

    public function getWarenkorbBySession($session_id)
    {
        return $this->where('session_id', $session_id)
            ->first();
    }

    public function getWarenkorbPositionen($warenkorb_id)
    {
        $db = \Config\Database::connect();

        return $db->table('warenkorb_positionen')
            ->where('warenkorb_id', $warenkorb_id)
            ->get()
            ->getResultArray();
    }

    public function addWarenkorbPosition($warenkorb_id, $produkt_id, $produkt_name, $menge, $preis)
    {
        $db = \Config\Database::connect();
        $position = $db->table('warenkorb_positionen')
            ->where('warenkorb_id', $warenkorb_id)
            ->where('produkt_id', $produkt_id)
            ->get()
            ->getRowArray();

        if ($position) {
            // Update existing position
            $db->table('warenkorb_positionen')
                ->where('id', $position['id'])
                ->update([
                    'menge' => $position['menge'] + $menge,
                    'aktualisiert_am' => date('Y-m-d H:i:s')
                ]);
        } else {
            // Add new position
            $db->table('warenkorb_positionen')
                ->insert([
                    'warenkorb_id' => $warenkorb_id,
                    'produkt_id' => $produkt_id,
                    'produkt_name' => $produkt_name,
                    'menge' => $menge,
                    'preis' => $preis
                ]);
        }
    }
}