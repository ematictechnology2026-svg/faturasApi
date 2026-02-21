<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class ClienteController extends ResourceController
{
    protected $modelName = 'App\Models\ClienteModel';
    protected $format    = 'json';

    public function index()
    {
         $data = $this->model->where('activo', 1)->findAll();

                        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        return $data ? $this->respond($data) : $this->failNotFound('Cliente não encontrado');
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
        if ($this->model->update($id, $data)) return $this->respond($data);
        return $this->fail($this->model->errors());
    }

    public function delete($id = null)
    {
        // Verifica se o cliente existe
        $cliente = $this->model->find($id);

        if (!$cliente) {
            return $this->failNotFound('Cliente não encontrado');
        }

        // Exclui o cliente
        if (!$this->model->delete($id)) {
            return $this->fail('Não foi possível excluir o cliente');
        }

        // Retorna os dados do cliente excluído
        return $this->respondDeleted([
            'status'  => true,
            'message' => 'Cliente excluído com sucesso',
            'data'    => $cliente
        ]);
    }
    public function search()
    {

        $termo = $this->request->getGet('termo');

        if (empty($termo)) {
            return $this->fail('Por favor, insira um termo de pesquisa.', 400);
        }

        // Procura em múltiplos campos usando like com 'orLike'
        $Clientes = $this->model->like('nome', $termo)
            ->orLike('email', $termo)
            ->orLike('telefone', $termo)
            ->orLike('nif', $termo)
            ->findAll();

        return $this->respond($Clientes);
    }

    // POST ou PUT /api/Clientes/(:num)/desactivarCliente
    public function desactivarCliente($id = null)
    {
        if ($this->model->update($id, ['activo' => 0])) {
            return $this->respond(['message' => 'Cliente desativado com sucesso']);
        }
        return $this->fail('Não foi possível desativar o cliente');
    }

    // POST ou PUT /api/Clientes/(:num)/activarCliente
    public function activarCliente($id = null)
    {
        if ($this->model->update($id, ['activo' => 1])) {
            return $this->respond(['message' => 'Cliente ativada com sucesso']);
        }
        return $this->fail('Não foi possível ativar o Cliente');
    }
}
