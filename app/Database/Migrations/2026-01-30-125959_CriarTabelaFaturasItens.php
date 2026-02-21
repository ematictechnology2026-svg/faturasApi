<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaFaturasItens extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'fatura_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'produto_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'quantidade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'preco_unitario' => [ // Importante: guarda o preço do dia da venda
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
        ]);

        $this->forge->addKey('id', true);

        // Chaves Estrangeiras para integridade referencial
        $this->forge->addForeignKey('fatura_id', 'faturas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('produto_id', 'produtos', 'id', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('faturas_itens');
    }

    public function down()
    {
        $this->forge->dropTable('faturas_itens');
    }
}
