<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'users';
    protected $allowedFields = ['empresa_id', 'name','username', 'email', 'password', 'telefone','activo'];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    // Hash da password antes de salvar
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data) {
        if (isset($data['data']['password']))
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }
}
