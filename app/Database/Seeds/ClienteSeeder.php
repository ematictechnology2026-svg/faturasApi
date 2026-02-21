<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'empresa_id' => 1,
            'nome'       => 'Consumidor Final',
            'nif'        => '999999999',
            'email'      => 'consumidor@exemplo.com',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('clientes')->insert($data);
    }
}
