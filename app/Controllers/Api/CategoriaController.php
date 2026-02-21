<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class CategoriaController extends ResourceController
{
            protected $modelName = 'App\Models\CategoriaModel';
            protected $format    = 'json';

            // GET /categorias
            public function index()
            {
                        $data = $this->model->listarComSubcategorias();
                        return $this->respond($data);
            }

            // GET /categorias/(:segment)
            public function show($id = null)
            {
                        $data = $this->model->find($id);

                        if (!$data) {
                                    return $this->failNotFound('Categoria não encontrada');
                        }

                        // Busca as subcategorias vinculadas a este ID
                        $subcategoriaModel = new \App\Models\SubcategoriaModel();
                        $data['subcategorias'] = $subcategoriaModel->where('categoria_id', $id)->findAll();

                        return $this->respond($data);
            }

            // POST /categorias
            public function create()
            {
                        helper('text');
                        $data = $this->request->getJSON(true); // Captura JSON do corpo da requisição

                        if (isset($data['nome'])) {
                                    $data['slug'] = url_title($data['nome'], '-', true);
                        }

                        if ($this->model->insert($data)) {
                                    return $this->respondCreated($data, 'Categoria criada com sucesso');
                        }

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
                                    'message' => 'Categoria atualizado com sucesso',
                                    'data'    => $this->model->find($id)
                        ]);
            }
            // DELETE /categorias/(:segment)
            public function delete($id = null)
            {
                        if ($this->model->delete($id)) {
                                    return $this->respondDeleted(['id' => $id], 'Categoria eliminada');
                        }
                        return $this->failNotFound();
            }
}
