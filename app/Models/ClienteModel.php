<?php
namespace App\Models;
use CodeIgniter\Model;

class ClienteModel extends Model {
    protected $table = 'clientes';
    protected $allowedFields = ['empresa_id', 'nome', 'nif', 'email', 'telefone', 'morada','activo'];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
}
