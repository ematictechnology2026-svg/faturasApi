<?php
namespace App\Models;
use CodeIgniter\Model;

class FaturaItemModel extends Model {
    protected $table = 'faturas_itens';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'fatura_id', 
        'produto_id', 
        'quantidade', 
        'preco_unitario', 
        'subtotal'
    ];
    // Como na migration não pusemos created_at/updated_at, deixamos como false
    protected $useTimestamps = false; 
}
