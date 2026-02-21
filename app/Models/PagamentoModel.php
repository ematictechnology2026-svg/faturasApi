<?php
namespace App\Models;
use CodeIgniter\Model;

class PagamentoModel extends Model {
    protected $table = 'pagamentos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'fatura_id', 
        'valor_pago', 
        'data_pagamento', 
        'metodo_pagamento', 
        'referencia_transacao'
    ];
    protected $useTimestamps = true;
}
