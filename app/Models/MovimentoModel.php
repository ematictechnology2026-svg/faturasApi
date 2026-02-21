<?php
namespace App\Models;
use CodeIgniter\Model;

class MovimentoModel extends Model {
    protected $table = 'movimentos_tesouraria';
    protected $allowedFields = ['conta_id', 'tipo', 'valor', 'descricao', 'referencia_tipo', 'referencia_id', 'data_movimento'];
    
    // Automatiza a atualização do saldo após registar um movimento
    protected $afterInsert = ['atualizarSaldoConta'];

    protected function atualizarSaldoConta(array $data) {
        $contaModel = new \App\Models\ContaModel();
        $movimento = $data['data'];
        
        if ($movimento['tipo'] == 'entrada') {
            $contaModel->where('id', $movimento['conta_id'])->increment('saldo_atual', $movimento['valor']);
        } else {
            $contaModel->where('id', $movimento['conta_id'])->decrement('saldo_atual', $movimento['valor']);
        }
    }
}
