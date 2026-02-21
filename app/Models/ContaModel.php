<?php
namespace App\Models;

use CodeIgniter\Model;

class ContaModel extends Model
{
    protected $table            = 'contas_tesouraria';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // Protege contra remoção acidental

    // Campos que podem ser editados
    protected $allowedFields    = ['nome', 'tipo', 'saldo_atual'];

    // Datas automáticas
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    /**
     * Retorna o saldo total somado de todas as contas
     */
    public function getSaldoGlobal()
    {
        return $this->selectSum('saldo_atual')->first()['saldo_atual'] ?? 0;
    }
}
