<?php

namespace App\Models;

use CodeIgniter\Model;

class SubcategoriaModel extends Model
{
    protected $table            = 'subcategorias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // Ajuste conforme sua migration

    protected $allowedFields    = ['categoria_id', 'nome', 'slug'];

    // Datas
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Busca subcategorias trazendo o nome da categoria pai
     */
    public function listarCompleto()
    {
        return $this->select('subcategorias.*, categorias.nome as categoria_pai')
                    ->join('categorias', 'categorias.id = subcategorias.categoria_id')
                    ->findAll();
    }
}
