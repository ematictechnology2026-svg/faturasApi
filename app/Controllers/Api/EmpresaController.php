<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class EmpresaController extends ResourceController
{
            protected $modelName = 'App\Models\EmpresaModel';
            protected $format    = 'json';
            public function index()
            {

                        $data = $this->model->where('activo', 1)->findAll();

                        return $this->respond($data);
            }
            public function create()
            {
                        $data = $this->request->getJSON(true);
                        if ($this->model->insert($data)) return $this->respondCreated($data, 'Registo feito com sucesso !');
                        return $this->failValidationErrors($this->model->errors());
            }
            public function show($id = null)
            {
                        $data = $this->model->find($id);
                        return $data ? $this->respond($data) : $this->failNotFound('Empresa não encontrado');
            }
            public function update($id = null)
            {
                        $data = $this->request->getJSON(true);

                        // Verifica se o registro existe
                        $registro = $this->model->find($id);
                        if (!$registro) {
                                    return $this->failNotFound('Registro não encontrado');
                        }

                        // Se quiseres permitir nif null
                        if (empty($data['nif'])) {
                                    $data['nif'] = null;
                        }

                        // Atualiza
                        if (!$this->model->update($id, $data)) {
                                    return $this->fail($this->model->errors());
                        }

                        // Retorna o registro atualizado
                        return $this->respond($this->model->find($id));
            }



            public function delete($id = null)
            {
                        // Verifica se o ID foi passado
                        if ($id === null) {
                                    return $this->fail('ID é obrigatório');
                        }

                        // Verifica se o usuário existe
                        if (!$this->model->find($id)) {
                                    return $this->failNotFound('Empresa não encontrado');
                        }

                        // Exclui o usuário
                        if ($this->model->delete($id)) {
                                    return $this->respondDeleted([
                                                'message' => 'Empresa excluído com sucesso'
                                    ]);
                        }

                        return $this->fail('Erro ao excluir Empresa');
            }
            public function search()
            {

                        $termo = $this->request->getGet('termo');

                        if (empty($termo)) {
                                    return $this->fail('Por favor, insira um termo de pesquisa.', 400);
                        }

                        // Procura em múltiplos campos usando like com 'orLike'
                        $empresas = $this->model->like('nome', $termo)
                                    ->orLike('email', $termo)
                                    ->orLike('telefone', $termo)
                                    ->orLike('nif', $termo)
                                    ->findAll();

                        return $this->respond($empresas);
            }
            // POST ou PUT /api/empresas/(:num)/desactivarEmpresa
            public function desactivarEmpresa($id = null)
            {
                        if ($this->model->update($id, ['activo' => 0])) {
                                    return $this->respond(['message' => 'Empresa desativada com sucesso']);
                        }
                        return $this->fail('Não foi possível desativar a empresa');
            }

            // POST ou PUT /api/empresas/(:num)/activarEmpresa
            public function activarEmpresa($id = null)
            {
                        if ($this->model->update($id, ['activo' => 1])) {
                                    return $this->respond(['message' => 'Empresa ativada com sucesso']);
                        }
                        return $this->fail('Não foi possível ativar a empresa');
            }
}
