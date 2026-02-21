<?php
namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\ContaModel;
use App\Models\MovimentoModel;

class TesourariaController extends BaseController
{
    use ResponseTrait;

    protected $contaModel;
    protected $movimentoModel;

    public function __construct()
    {
        $this->contaModel = new ContaModel();
        $this->movimentoModel = new MovimentoModel();
    }

    /**
     * Lista todas as contas e saldos (GET /api/tesouraria/contas)
     */
    public function listarContas()
    {
        $contas = $this->contaModel->findAll();
        return $this->respond($contas);
    }

    /**
     * Regista uma entrada ou saída (POST /api/tesouraria/movimentar)
     */
    public function movimentar()
    {
        // Validação dos dados recebidos via JSON
        $regras = [
            'conta_id'  => 'required|is_not_unique[contas_tesouraria.id]',
            'tipo'      => 'required|in_list[entrada,saida]',
            'valor'     => 'required|decimal|greater_than[0]',
            'descricao' => 'required|min_length[3]'
        ];

        if (!$this->validate($regras)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $dados = $this->request->getJSON(true); // Obtém dados do corpo da requisição JSON

        // O MovimentoModel já tem o 'afterInsert' que atualiza o saldo da ContaModel automaticamente
        if ($this->movimentoModel->insert($dados)) {
            return $this->respondCreated([
                'status'  => 201,
                'message' => 'Movimento registado e saldo atualizado.',
                'data'    => $dados
            ]);
        }

        return $this->fail('Erro ao processar movimentação.');
    }

    /**
     * Transferência entre contas (POST /api/tesouraria/transferir)
     */
    public function transferir()
    {
        $db = \Config\Database::connect();
        $dados = $this->request->getJSON(true);

        // Inicia uma Transação de Base de Dados para garantir segurança
        $db->transStart();

        // 1. Saída da conta de origem
        $this->movimentoModel->insert([
            'conta_id'  => $dados['origem_id'],
            'tipo'      => 'saida',
            'valor'     => $dados['valor'],
            'descricao' => 'Transferência enviada para ' . $dados['destino_nome']
        ]);

        // 2. Entrada na conta de destino
        $this->movimentoModel->insert([
            'conta_id'  => $dados['destino_id'],
            'tipo'      => 'entrada',
            'valor'     => $dados['valor'],
            'descricao' => 'Transferência recebida de ' . $dados['origem_nome']
        ]);

        $db->transComplete(); // Finaliza a transação

        if ($db->transStatus() === false) {
            return $this->fail('Falha crítica na transferência bancária.');
        }

        return $this->respond(['message' => 'Transferência realizada com sucesso!']);
    }
}
