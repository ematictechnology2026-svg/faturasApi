<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
{
    $header = $request->getHeaderLine('Authorization');

    if (!$header) {
        return service('response')
            ->setJSON(['error' => 'Token não informado'])
            ->setStatusCode(401);
    }

    $token = explode(' ', $header);

    if (count($token) !== 2 || $token[0] !== 'Bearer') {
        return service('response')
            ->setJSON(['error' => 'Token mal formatado'])
            ->setStatusCode(401);
    }

    try {
        $key = getenv('JWT_SECRET');
        $decoded = JWT::decode($token[1], new Key($key, 'HS256'));

        // ⚡ Salvar dados do usuário no request
        $request->user = $decoded;

        // ⚡ Verifica role se foi passada no argumento do filter
        if ($arguments) {
            $requiredRole = $arguments[0];
            if ($decoded->role !== $requiredRole) {
                return service('response')
                    ->setJSON(['error' => 'Acesso negado: role insuficiente'])
                    ->setStatusCode(403);
            }
        }

    } catch (\Exception $e) {
        return service('response')
            ->setJSON(['error' => 'Token inválido ou expirado'])
            ->setStatusCode(401);
    }
}


    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada aqui
    }
}
