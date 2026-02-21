<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{


            public function login()
            {
                        $db = \Config\Database::connect();
                        $data = $this->request->getJSON(true);

                        $query = $db->query(
                                    "SELECT * FROM users WHERE email = ?",
                                    [$data['email']]
                        );

                        $user = $query->getRowArray();

                        if (!$user || !password_verify($data['password'], $user['password'])) {
                                    return $this->failUnauthorized('Email ou senha inválidos');
                        }

                        // $key = getenv('JWT_SECRET');
                        $key = '$2y$12$J4xBclGXzXOmcTJH.63vDeV7hzoVNDiil5zLBE0jr66e25eU0wT9W';

                        // 🔹 Access Token (curto)
                        $accessPayload = [
                                    'sub' => $user['id'],
                                    'email' => $user['email'],
                                    'role' => $user['role'],
                                    'iat' => time(),
                                    'exp' => time() + (60 * 60) // 15 minutos
                        ];

                        $accessToken = JWT::encode($accessPayload, $key, 'HS256');

                        // 🔹 Refresh Token (longo)
                        $refreshToken = bin2hex(random_bytes(64));

                        $db->query(
                                    "INSERT INTO refresh_tokens (user_id, token, expires_at)
         VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 7 DAY))",
                                    [$user['id'], $refreshToken]
                        );

                        return $this->respond([
                                    'access_token' => $accessToken,
                                    'refresh_token' => $refreshToken
                        ]);
            }
            public function refresh()
            {
                        $db = \Config\Database::connect();
                        $data = $this->request->getJSON(true);

                        if (!isset($data['refresh_token'])) {
                                    return $this->fail('Refresh token é obrigatório');
                        }

                        $query = $db->query(
                                    "SELECT * FROM refresh_tokens 
         WHERE token = ? AND expires_at > NOW()",
                                    [$data['refresh_token']]
                        );

                        $tokenData = $query->getRowArray();

                        if (!$tokenData) {
                                    return $this->failUnauthorized('Refresh token inválido ou expirado');
                        }

                        // Buscar usuário
                        $user = $db->query(
                                    "SELECT id, email FROM users WHERE id = ?",
                                    [$tokenData['user_id']]
                        )->getRowArray();

                        // $key = getenv('JWT_SECRET');
                        $key = '$2y$12$J4xBclGXzXOmcTJH.63vDeV7hzoVNDiil5zLBE0jr66e25eU0wT9W';

                        // Gerar novo access token
                        $payload = [
                                    'sub' => $user['id'],
                                    'email' => $user['email'],
                                    'role' => $user['role'],
                                    'iat' => time(),
                                    'exp' => time() + (60 * 60)
                        ];

                        $newAccessToken = JWT::encode($payload, $key, 'HS256');

                        return $this->respond([
                                    'access_token' => $newAccessToken
                        ]);
            }
            public function logout()
            {
                        $db = \Config\Database::connect();
                        $data = $this->request->getJSON(true);

                        $db->query("DELETE FROM refresh_tokens WHERE token = ?", [
                                    $data['refresh_token']
                        ]);

                        return $this->respond(['message' => 'Logout realizado']);
            }
}
