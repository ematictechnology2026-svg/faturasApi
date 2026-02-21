<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaSubcategorias extends Migration
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
            'categoria_id' => [ // Relacionamento com a categoria pai
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        
        // Define que se a categoria pai for excluída, as subcategorias também serão (CASCADE)
        $this->forge->addForeignKey('categoria_id', 'categorias', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('subcategorias');
    }

    public function down()
    {
        $this->forge->dropTable('subcategorias');
    }
}
