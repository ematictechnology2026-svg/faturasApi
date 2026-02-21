<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class ProdutoController extends ResourceController
{
    protected $modelName = 'App\Models\ProdutoModel';
    protected $format    = 'json';


    public function index()
    {

        $produtos = $this->model->listarProdutosCompleto();

        return $this->respond($produtos);
    }

    public function show($id = null)
    {
        $data = $this->model->select('produtos.*, categorias.nome as categoria_nome, subcategorias.nome as subcategoria_nome')
            ->join('categorias', 'categorias.id = produtos.categoria_id', 'left')
            ->join('subcategorias', 'subcategorias.id = produtos.subcategoria_id', 'left')
            ->find($id);

        if (!$data) {
            return $this->failNotFound('Produto não encontrado');
        }

        return $this->respond($data);
    }

    // GET /api/produtos/baixo-estoque
    public function baixoEstoque()
    {
        $data = $this->model->where('estoque_atual <= estoque_minimo')->findAll();
        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if ($this->model->insert($data)) return $this->respondCreated($data);
        return $this->failValidationErrors($this->model->errors());
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors());
        }

        return $this->respond([
            'status'  => true,
            'message' => 'Produto atualizado com sucesso',
            'data'    => $this->model->find($id)
        ]);
    }
    public function delete($id = null)
    {
        // Verifica se o ID foi passado
        if ($id === null) {
            return $this->fail('ID é obrigatório');
        }

        // Verifica se o usuário existe
        if (!$this->model->find($id)) {
            return $this->failNotFound('Produtos não encontrado');
        }

        // Exclui o usuário
        if ($this->model->delete($id)) {
            return $this->respondDeleted([
                'message' => 'Produtos excluído com sucesso'
            ]);
        }

        return $this->fail('Erro ao excluir produto');
    }
    public function search()
    {

        $termo = $this->request->getGet('termo');

        if (empty($termo)) {
            return $this->fail('Por favor, insira um termo de pesquisa.', 400);
        }


        $empresas = $this->model->like('nome', $termo)
            ->orLike('codigo_barras', $termo)
            ->orLike('preco_venda', $termo)
            ->findAll();

        return $this->respond($empresas);
    }
    public function alterarEstoque($id = null)
    {
        // 1. Recebe os dados (getVar funciona para JSON ou POST)
        $novaQuantidade = $this->request->getVar('quantidade');
      
        // 2. Validação básica
        if (empty($id) || $novaQuantidade === null) {
            return $this->failValidationErrors('ID ou quantidade não fornecidos.');
        }
 
        $dados = [
            'estoque_atual' => $novaQuantidade
        ];

        // 3. Executa a atualização
        if ($this->model->update($id, $dados)) {
            return $this->respond([
                'status'  => 200,
                'message' => 'Estoque atualizado com sucesso.',
                'data'    => $dados
            ]);
        }

        return $this->fail('Falha ao atualizar o estoque no banco de dados.');
    }
}
