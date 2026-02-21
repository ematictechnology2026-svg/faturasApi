<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class SubcategoriaController extends ResourceController
{
            protected $modelName = 'App\Models\SubcategoriaModel';
            protected $format    = 'json';

            // GET /subcategorias
            public function index()
            {
                        return $this->respond($this->model->listarCompleto());
            }
            public function show($id = null)
            {
                        $data = $this->model->find($id);
                        if (!$data) return $this->failNotFound('Categoria não encontrada');
                        return $this->respond($data);
            }
            // GET /subcategorias/pai/(:num) -> Filtra por categoria pai
            public function porCategoria($id = null)
            {
                        $data = $this->model->where('categoria_id', $id)->findAll();
                        return $this->respond($data);
            }

            // POST /subcategorias
            public function create()
            {
                        helper('text');
                        $data = $this->request->getJSON(true);

                        if (isset($data['nome'])) {
                                    $data['slug'] = url_title($data['nome'], '-', true);
                        }

                        if ($this->model->insert($data)) {
                                    return $this->respondCreated($data, 'Subcategoria criada');
                        }

                        return $this->fail($this->model->errors());
            }
            public function update($id = null)
            {
                        $data = $this->request->getJSON(true);

                        if (!$this->model->update($id, $data)) {
                                    return $this->fail($this->model->errors());
                        }

                        return $this->respond([
                                    'status'  => true,
                                    'message' => 'Subcategoria atualizado com sucesso',
                                    'data'    => $this->model->find($id)
                        ]);
            }
            public function delete($id = null)
            {
                        if ($this->model->delete($id)) {
                                    return $this->respondDeleted(['id' => $id], 'Categoria eliminada');
                        }
                        return $this->failNotFound();
            }
}
