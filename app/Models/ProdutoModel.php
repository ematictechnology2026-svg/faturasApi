<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $table = 'produtos';
    protected $allowedFields = ['empresa_id', 'nome', 'codigo_barras', 'sku', 'preco_venda', 'preco_compra', 'estoque_atual', 'estoque_minimo', 'categoria_id', 'subcategoria_id', 'activo'];
    protected $validationRules = [

        'codigo_barras' => 'permit_empty|is_unique[produtos.codigo_barras,id,{id}]',
        'sku' => 'permit_empty|is_unique[produtos.sku,id,{id}]'

    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;


    // Dentro do seu ProdutoModel.php
    public function buscarProdutosComCategorias()
    {
        return $this->select('produtos.*, categorias.nome as categoria_nome')
            ->join('categorias', 'categorias.id = produtos.categoria_id', 'left')
            ->findAll();
    }


    public function listarProdutosCompleto()
    {
        return $this->select('produtos.*, categorias.nome as categoria_nome, subcategorias.nome as subcategoria_nome')
            ->join('categorias', 'categorias.id = produtos.categoria_id', 'left')
            ->join('subcategorias', 'subcategorias.id = produtos.subcategoria_id', 'left')
            ->where('produtos.deleted_at', null)
            ->findAll();
    }
}
