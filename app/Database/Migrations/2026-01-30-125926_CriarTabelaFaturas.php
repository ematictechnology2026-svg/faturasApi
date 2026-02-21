<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaFaturas extends Migration
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
            'empresa_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'cliente_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'numero_fatura' => [ // Ex: FT 2024/001
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'data_emissao' => [
                'type' => 'DATETIME',
            ],
            'valor_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'status' => [ // Pendente, Paga, Cancelada
                'type'       => 'ENUM',
                'constraint' => ['pendente', 'paga', 'cancelada'],
                'default'    => 'pendente',
            ],
            'metodo_pagamento' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'activo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('empresa_id', 'empresas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('faturas');
    }

    public function down()
    {
        $this->forge->dropTable('faturas');
    }
}
