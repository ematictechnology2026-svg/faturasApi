<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\FaturaModel;
use App\Models\FaturaItemModel;
use App\Models\ProdutoModel;

class VendaController extends ResourceController
{
    public function realizarVenda()
    {
        $db = \Config\Database::connect();
        $json = $this->request->getJSON(true);

        // Validação básica
        if (
            !isset($json['empresa_id']) ||
            !isset($json['cliente_id']) ||
            !isset($json['metodo_pagamento']) ||
            !isset($json['itens']) ||
            !is_array($json['itens'])
        ) {
            return $this->fail('Dados inválidos.');
        }

        $faturaModel  = new FaturaModel();
        $itemModel    = new FaturaItemModel();
        $produtoModel = new ProdutoModel();

        $db->transBegin();

        try {

            // 1️⃣ Calcular total da fatura
            $valorTotal = 0;
            foreach ($json['itens'] as $item) {
                $valorTotal += $item['quantidade'] * $item['preco'];
            }

            // 2️⃣ Criar fatura (CAMPOS CERTOS)
            $faturaData = [
                'empresa_id'        => $json['empresa_id'],
                'cliente_id'        => $json['cliente_id'],
                'numero_fatura'     => 'FT-' . time(),
                'data_emissao'      => date('Y-m-d'),
                'valor_total'       => $valorTotal,
                'status'            => 'paga',
                'metodo_pagamento'  => $json['metodo_pagamento']
            ];

            $faturaId = $faturaModel->insert($faturaData);

            if (!$faturaId) {
                throw new \Exception('Erro ao criar fatura');
            }

            // 3️⃣ Itens + Stock
            foreach ($json['itens'] as $item) {

                if (
                    !isset($item['produto_id']) ||
                    !isset($item['quantidade']) ||
                    !isset($item['preco'])
                ) {
                    throw new \Exception('Item inválido');
                }

                $produto = $produtoModel->find($item['produto_id']);

                if (!$produto) {
                    throw new \Exception('Produto não encontrado');
                }

                if ($produto['estoque_atual'] < $item['quantidade']) {
                    throw new \Exception('Stock insuficiente');
                }

                $itemModel->insert([
                    'fatura_id'      => $faturaId,
                    'produto_id'     => $item['produto_id'],
                    'quantidade'     => $item['quantidade'],
                    'preco_unitario' => $item['preco'],
                    'subtotal'       => $item['quantidade'] * $item['preco']
                ]);

                // Atualizar stock
                $produtoModel->update(
                    $item['produto_id'],
                    [
                        'estoque_atual' =>
                        $produto['estoque_atual'] - $item['quantidade']
                    ]
                );
            }

            $db->transCommit();

            return $this->respondCreated([
                'status'    => 'Venda concluída com sucesso',
                'fatura_id' => $faturaId
            ]);

        } catch (\Exception $e) {

            $db->transRollback();
            return $this->fail($e->getMessage());
        }
    }
}
