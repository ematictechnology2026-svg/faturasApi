<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProdutoModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
    public function export($formato = 'xlsx')
    {
        $produtoModel = new ProdutoModel();
        $produtos = $produtoModel->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabeçalhos
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nome');
        $sheet->setCellValue('C1', 'Preço');
        $sheet->setCellValue('D1', 'Quantidade');
        $sheet->setCellValue('E1', 'Data Criação');

        // Dados
        $linha = 2;
        foreach ($produtos as $produto) {
            $sheet->setCellValue('A' . $linha, $produto['id']);
            $sheet->setCellValue('B' . $linha, $produto['nome']);
            $sheet->setCellValue('C' . $linha, $produto['preco']);
            $sheet->setCellValue('D' . $linha, $produto['quantidade']);
            $sheet->setCellValue('E' . $linha, $produto['created_at']);
            $linha++;
        }

        $fileName = 'produtos_' . date('Y-m-d');

        // Escolher formato
        if ($formato == 'xls') {
            $writer = new Xls($spreadsheet);
            $fileName .= '.xls';
            $this->response->setHeader('Content-Type', 'application/vnd.ms-excel');
        } else {
            $writer = new Xlsx($spreadsheet);
            $fileName .= '.xlsx';
            $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }

        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $this->response->setHeader('Cache-Control', 'max-age=0');

        $writer->save('php://output');
        exit;
    }
    public function import()
    {
        $file = $this->request->getFile('file');

        if (!$file->isValid()) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Arquivo inválido'
            ]);
        }

        $spreadsheet = IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $produtoModel = new ProdutoModel();

        // Ignorar primeira linha (cabeçalho)
        unset($rows[0]);

        foreach ($rows as $row) {
            $produtoModel->insert([
                'nome'       => $row[1],
                'preco'      => $row[2],
                'quantidade' => $row[3],
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Produtos importados com sucesso!'
        ]);
    }
}
