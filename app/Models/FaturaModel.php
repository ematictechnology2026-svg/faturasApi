<?php
namespace App\Models;
use CodeIgniter\Model;

class FaturaModel extends Model {
    protected $table = 'faturas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'empresa_id', 
        'cliente_id', 
        'numero_fatura', 
        'data_emissao', 
        'valor_total', 
        'status', 
        'metodo_pagamento'
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $returnType = 'array';
}
