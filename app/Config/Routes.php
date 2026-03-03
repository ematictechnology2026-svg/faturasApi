<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->post('login', 'Api\UserController::login');
$routes->post('users/forgotPassword', 'UserController::forgotPassword');
$routes->post('auth/validate-token', 'UserController::validateResetToken');
$routes->post('users/reset-password', 'UserController::resetPassword');
$routes->post('api/refresh', 'Api\UserController::refresh');

// Grupo Protegido por JWT
$routes->group('api', ['namespace' => 'App\Controllers\Api', /*'filter' => 'jwt'*/], function ($routes) {

            // Rotas de Utilizadores (Específicas primeiro para evitar conflitos com (:num))
            $routes->get('users/pesquisarPorNome', 'UserController::pesquisarPorNome');
            $routes->get('users/pesquisarPorEmail', 'UserController::pesquisarPorEmail');
            $routes->post('users/(:num)/role', 'UserController::changeRole/$1');

            // CRUD Standard de Users
            $routes->get('users', 'UserController::index');
            $routes->get('users/(:num)', 'UserController::show/$1');
            $routes->post('users', 'UserController::create');
            $routes->put('users/(:num)', 'UserController::update/$1');
            $routes->delete('users/(:num)', 'UserController::delete/$1');

            // CRUD de Clientes
            $routes->get('clientes/search', 'ClienteController::search');
            $routes->post('clientes/(:num)/desactivarCliente', 'ClienteController::desactivarCliente/$1');
            $routes->post('clientes/(:num)/activarCliente', 'ClienteController::activarCliente/$1');
            $routes->resource('clientes', ['controller' => 'ClienteController']);
            // crud produtos   
            $routes->get('produtos/search', 'ProdutoController::search');
            $routes->get('produtos/baixoEstoque', 'ProdutoController::baixoEstoque');
            $routes->post('produtos/alterarEstoque/(:num)', 'ProdutoController::alterarEstoque/$1');
            $routes->resource('produtos', ['controller' => 'ProdutoController']);
            $routes->get('produtos/export/(:segment)', 'ProdutoController::export/$1');
            $routes->post('produtos/import', 'ProdutoController::import');
            // crud categorias
            $routes->resource('categorias', ['controller' => 'CategoriaController']);
            // crud subcategorias
            $routes->resource('subcategorias', ['controller' => 'SubcategoriaController']);
            // Rota customizada para filtrar subcategorias por categoria pai
            $routes->get('subcategorias/pai/(:num)', 'SubcategoriaController::porCategoria/$1');
            // Rotas de empresas
            $routes->get('empresas/search', 'EmpresaController::search');
            $routes->post('empresas/(:num)/desactivarEmpresa', 'EmpresaController::desactivarEmpresa/$1');
            $routes->post('empresas/(:num)/activarEmpresa', 'EmpresaController::activarEmpresa/$1');
            $routes->resource('empresas', ['controller' => 'EmpresaController']);

            // Rota tesouraria
            $routes->get('tesouraria/contas', 'TesourariaController::listarContas');
            $routes->post('tesouraria/movimentar', 'TesourariaController::movimentar');
            $routes->post('tesouraria/transferir', 'TesourariaController::transferir');

            // Rota de Vendas
            $routes->post('vendas/efetuar', 'VendaController::realizarVenda');

            $routes->get('/', 'UserController::index');
});
