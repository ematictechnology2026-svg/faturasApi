<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaClientes extends Migration
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
            'empresa_id' => [ // Empresa que "possui" este cliente
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nif' => [ // Identificação fiscal
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
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
            'morada' => [
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
        $this->forge->addForeignKey('empresa_id', 'empresas', 'id', 'CASCADE', 'CASCADE');
          $this->forge->createTable('clientes');

        // Inserir Cliente Default (Consumidor Final)
        $db = \Config\Database::connect();
        $builder = $db->table('clientes');
        
        $builder->insert([
            'empresa_id' => 1, 
            'nome'       => 'Consumidor Final',
            'nif'        => '999999999',
            'email'      => 'cliente.default@exemplo.com',
            'telefone'   => '000000000',
            'morada'     => 'Cidade de Luanda, Angola',
            'activo'     => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

    }

    public function down()
    {
        $this->forge->dropTable('clientes');
    }
}
