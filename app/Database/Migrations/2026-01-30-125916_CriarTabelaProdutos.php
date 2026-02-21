<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaProdutos extends Migration
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
            
            
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'codigo_barras' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'unique' => true
            ],
            'sku' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'unique' => true
            ],
            'preco_venda' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'preco_compra' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'estoque_atual' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'estoque_minimo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'categoria_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true, 
            ],
            'subcategoria_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
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

        // Relacionamento com a tabela empresas
        $this->forge->addForeignKey('empresa_id', 'empresas', 'id', 'CASCADE', 'CASCADE');

        // Relacionamento com a tabela categorias
       
        $this->forge->addForeignKey('categoria_id', 'categorias', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('subcategoria_id', 'subcategorias', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('produtos');
    }

    public function down()
    {
        $this->forge->dropTable('produtos');
    }
}
