<?php
namespace App\Models;
use CodeIgniter\Model;

class EmpresaModel extends Model {
    protected $table = 'empresas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'razao_social', 'nif', 'email', 'telefone', 'endereco','activo'];
   protected $validationRules = [
 
    'nif' => 'permit_empty|is_unique[clientes.nif,id,{id}]',
    'email' => 'required|valid_email',
    'nome'  => 'required|min_length[3]'
];


    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
}
