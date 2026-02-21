<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaUtilizador extends Migration
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
            'empresa_id' => [ // Relacionamento com a tabela de empresas
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'telefone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'activo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'reset_token' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reset_expires' => [
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
        // Cria um índice para a busca ficar mais rápida
        $this->forge->addKey(['name', 'email', 'telefone']);

        // Chave estrangeira (Opcional, mas recomendado)
        $this->forge->addForeignKey('empresa_id', 'empresas', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('users');

        // Inserir Utilizador Padrão
        $db = \Config\Database::connect();
        $db->table('users')->insert([
            'name'       => 'Administrador',
            'username'       => 'admin',
            'email'      => 'admin@sistema.com',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'telefone'   => '999999999',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
