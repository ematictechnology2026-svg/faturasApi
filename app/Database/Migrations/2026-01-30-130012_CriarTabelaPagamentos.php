<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaPagamentos extends Migration
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
            'valor_pago' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'data_pagamento' => [
                'type' => 'DATETIME',
            ],
            'metodo_pagamento' => [ // Ex: Dinheiro, TPA, Transferência
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'referencia_transacao' => [ // Cód. comprovativo ou ID do stripe/paypal
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('fatura_id', 'faturas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pagamentos');
    }

    public function down()
    {
        $this->forge->dropTable('pagamentos');
    }
}
