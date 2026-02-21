<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController extends ResourceController
{
    protected $modelName = UserModel::class;
    protected $format    = 'json';

    // Listar todos os usuários
    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    // Criar um usuário
    public function create()
    {

        $data = $this->request->getJSON(true);
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        if ($this->model->insert($data)) {
            return $this->respondCreated([
                'message' => 'Usuário criado com sucesso',
                'data' => $data
            ]);
        }

        return $this->failValidationErrors($this->model->errors());
    }


    // Buscar usuário por id
    public function show($id = null)
    {
        $user = $this->model->find($id);
        if ($user) {
            return $this->respond($user);
        }
        return $this->failNotFound('Usuário não encontrado');
    }
    public function update($id = null)
    {
        // Verifica se o ID foi passado
        if ($id === null) {
            return $this->fail('ID é obrigatório');
        }

        // Pega o JSON como array
        $data = $this->request->getJSON(true);
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Verifica se o registro existe
        if (!$this->model->find($id)) {
            return $this->failNotFound('Usuário não encontrado');
        }

        // Faz o update
        if ($this->model->update($id, $data)) {
            return $this->respond([
                'message' => 'Usuário atualizado com sucesso',
                'data' => $data
            ]);
        }

        // Erro de validação
        return $this->failValidationErrors($this->model->errors());
    }
    public function delete($id = null)
    {
        // Verifica se o ID foi passado
        if ($id === null) {
            return $this->fail('ID é obrigatório');
        }

        // Verifica se o usuário existe
        if (!$this->model->find($id)) {
            return $this->failNotFound('Usuário não encontrado');
        }

        // Exclui o usuário
        if ($this->model->delete($id)) {
            return $this->respondDeleted([
                'message' => 'Usuário excluído com sucesso'
            ]);
        }

        return $this->fail('Erro ao excluir usuário');
    }



    public function login()
    {
        $db   = \Config\Database::connect();
        $data = $this->request->getJSON(true);

        if (!isset($data['login'], $data['password'])) {
            return $this->failValidationErrors('Login e senha são obrigatórios');
        }

        // Buscar usuário por email OU username OU telefone
        $query = $db->query(
            "SELECT * FROM users 
         WHERE email = ? 
            OR username = ? 
            OR telefone = ?
         LIMIT 1",
            [
                $data['login'],
                $data['login'],
                $data['login']
            ]
        );

        $user = $query->getRowArray();

        // Verificar usuário e senha
        if (!$user || !password_verify($data['password'], $user['password'])) {
            return $this->failUnauthorized('Credenciais inválidas');
        }

        // Chave JWT (NÃO use hash de senha aqui)
        $key = getenv('JWT_SECRET');

        $payload = [
            'iss' => 'api-ci4',
            'sub' => $user['id'],
            'email' => $user['email'],
            'iat' => time(),
            'exp' => time() + (60 * 60) // 1 hora
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'token' => $token,
            'user' => [
                'id'       => $user['id'],
                'name'     => $user['name'],
                'email'    => $user['email'],
                'username' => $user['username'],
                'telefone'    => $user['telefone'],
            ]
        ]);
    }

    public function changeRole($id = null)
    {

        $db = \Config\Database::connect();
        $role = $db->query("select role from users where id='$id' ")->getRowArray();

        // Verifica se o ID foi passado
        if ($id === null) {
            return $this->fail('ID do usuário é obrigatório');
        }

        // Pega o JSON enviado
        $data = $this->request->getJSON(true);

        if (!isset($data['role'])) {
            return $this->fail('O novo role é obrigatório');
        }
        if ($data === $role) {
            return $this->fail('Utilizador ja e do tipo ' . $role);
        }

        $newRole = $data['role'];

        // Verifica se o role é válido
        $validRoles = ['user', 'admin'];

        // adicione todos os roles existentes
        if (!in_array($newRole, $validRoles)) {
            return $this->failValidationErrors(['role' => 'Role inválido']);
        }

        // Verifica se o usuário existe
        $user = $this->model->find($id);
        if (!$user) {
            return $this->failNotFound('Usuário não encontrado');
        }

        // Atualiza o role
        $dados = $this->model->update($id, ['role' => $newRole]);

        return $this->respond([
            'message' => 'Role atualizado com sucesso',
            'user' => [
                'id' => $id,
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $newRole
            ]
        ]);
    }

    public function pesquisarPorNome()
    {
        $name = $this->request->getGet('name');

        if (!$name) {
            return $this->fail('Informe um nome ou letra para pesquisa');
        }

        $users = $this->model
            ->like('name', $name)
            ->findAll();

        return $this->respond($users);
    }
    public function pesquisarPorEmail()
    {
        $email = $this->request->getGet('email');

        if (!$email) {
            return $this->fail('Informe um email ou letra para pesquisa');
        }

        $users = $this->model
            ->like('email', $email)
            ->find();
        return json_encode($users);
        return $this->respond($users);
    }
    public function search()
    {

        $termo = $this->request->getGet('termo');

        if (empty($termo)) {
            return $this->fail('Por favor, insira um termo de pesquisa.', 400);
        }

        $model = new \App\Models\UserModel();

        // Procura em múltiplos campos usando like com 'orLike'
        $users = $model->like('name', $termo)
            ->orLike('email', $termo)
            ->orLike('telefone', $termo)
            ->findAll();

        return $this->respond($users);
    }
    public function forgotPassword()
    {
        $db   = \Config\Database::connect();
        $data = $this->request->getJSON(true);
        return json_encode($data);
        if (!isset($data['login'])) {
            return $this->failValidationErrors('Email ou telefone é obrigatório');
        }

        // Buscar usuário
        $query = $db->query(
            "SELECT id, email, phone FROM users 
         WHERE email = ? OR phone = ? 
         LIMIT 1",
            [$data['login'], $data['login']]
        );

        $user = $query->getRowArray();

        if (!$user) {
            return $this->respond([
                'message' => 'Se o usuário existir, receberá instruções de recuperação'
            ]);
        }

        $token = bin2hex(random_bytes(32));

        $db->table('users')->where('id', $user['id'])->update([
            'reset_token'   => hash('sha256', $token),
            'reset_expires' => date('Y-m-d H:i:s', time() + 3600) // 1 hora
        ]);

        return $this->respond([
            'message' => 'Se o usuário existir, receberá instruções de recuperação'
        ]);
    }
    public function validateResetToken()
    {
        $db   = \Config\Database::connect();
        $data = $this->request->getJSON(true);

        if (!isset($data['token'])) {
            return $this->failValidationErrors('Token é obrigatório');
        }

        $hashedToken = hash('sha256', $data['token']);

        $user = $db->table('users')
            ->where('reset_token', $hashedToken)
            ->where('reset_expires >=', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        if (!$user) {
            return $this->failUnauthorized('Token inválido ou expirado');
        }

        return $this->respond([
            'message' => 'Token válido'
        ]);
    }
    public function resetPassword()
    {
        $db   = \Config\Database::connect();
        $data = $this->request->getJSON(true);

        if (!isset($data['token'], $data['password'])) {
            return $this->failValidationErrors('Token e nova senha são obrigatórios');
        }

        if (strlen($data['password']) < 6) {
            return $this->failValidationErrors('Senha deve ter no mínimo 6 caracteres');
        }

        $hashedToken = hash('sha256', $data['token']);

        $user = $db->table('users')
            ->where('reset_token', $hashedToken)
            ->where('reset_expires >=', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        if (!$user) {
            return $this->failUnauthorized('Token inválido ou expirado');
        }

        $db->table('users')->where('id', $user['id'])->update([
            'password'      => password_hash($data['password'], PASSWORD_DEFAULT),
            'reset_token'   => null,
            'reset_expires' => null
        ]);

        return $this->respond([
            'message' => 'Senha redefinida com sucesso'
        ]);
    }
}
