<?php

declare(strict_types=1);

use Bluepeer\Controller\FooController;
use Bluepeer\Controller\UserController;
use Bluepeer\Core\KernelInterface;
use Bluepeer\Core\RouteMappingInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (RouteMappingInterface $router) {
	$router->get('/api/v1/foo', [FooController::class, 'getAll']);
	$router->get('/api/v1/foo/{id}', [FooController::class, 'getById']);
	$router->post('/api/v1/foo', [FooController::class, 'create']);
	$router->put('/api/v1/foo/{id}', [FooController::class, 'update']);
	$router->patch('/api/v1/foo/{id}', [FooController::class, 'partialUpdate']);
	$router->delete('/api/v1/foo/{id}', [FooController::class, 'delete']);

	$router->get('/create', [UserController::class, 'create']);
	$router->get('/all', [UserController::class, 'getAll']);
	$router->get('/foo/{id}', [UserController::class, 'getById']);
	$router->put('/bar/{id}', [UserController::class, 'update']);
	$router->get('/baz/{id}', [UserController::class, 'delete']);
};
