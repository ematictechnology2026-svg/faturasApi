<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table            = 'categorias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // Ativa o deleted_at

    protected $allowedFields    = ['nome', 'slug', 'descricao'];

    // Datas
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Retorna a categoria com suas subcategorias vinculadas
     */
    public function getSubcategorias($categoriaId)
    {
        return $this->db->table('subcategorias')
            ->where('categoria_id', $categoriaId)
            ->get()
            ->getResultArray();
    }
    // No seu CategoriaModel.php
    public function listarComSubcategorias()
    {
        $categorias = $this->findAll();

        foreach ($categorias as &$categoria) {
            $categoria['subcategorias'] = $this->db->table('subcategorias')
                ->where('categoria_id', $categoria['id'])
                ->get()
                ->getResultArray();
        }

        return $categorias;
    }
}
