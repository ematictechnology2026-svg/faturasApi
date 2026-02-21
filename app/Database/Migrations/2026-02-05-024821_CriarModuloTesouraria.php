<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarModuloTesouraria extends Migration
{
    // No terminal: php spark make:migration CriarModuloTesouraria

    public function up()
    {
        // Tabela de Contas (Ex: Caixa, Banco Millennium, Carteira)
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'tipo'        => ['type' => 'ENUM', 'constraint' => ['caixa', 'banco', 'digital']],
            'saldo_atual' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('contas_tesouraria');

        // Tabela de Movimentos (O histórico)
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'conta_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tipo'             => ['type' => 'ENUM', 'constraint' => ['entrada', 'saida']],
            'valor'            => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'descricao'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'referencia_tipo'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], 
            'referencia_id'    => ['type' => 'INT', 'null' => true], 
            'data_movimento'   => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('conta_id', 'contas_tesouraria', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('movimentos_tesouraria');
    }


    public function down()
    {
      $this->forge->dropTable('contas_tesouraria');
      $this->forge->dropTable('movimentos_tesouraria');
    }
}
