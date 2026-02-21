<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaEmpresas extends Migration
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
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'razao_social' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nif' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'telefone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'endereco' => [
                'type'       => 'TEXT',
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
        $this->forge->createTable('empresas');
        $db = \Config\Database::connect();
        $builder = $db->table('empresas');

        $builder->insert([
            'nome'         => 'edvsoftwarefaturacao',
            'razao_social' => 'EDV Software, Lda',
            'nif'          => '5000000000', // NIF fictício
            'email'        => 'suporte@edvsoftware.com',
            'telefone'     => '900000000',
            'endereco'     => 'Luanda, Angola',
            'activo'       => 1,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('empresas');
    }
}
