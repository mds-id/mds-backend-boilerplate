<?php

declare(strict_types=1);

use Modspace\Controller\FooController;
use Modspace\Controller\UserController;
use Modspace\Core\KernelInterface;
use Modspace\Core\RouteMappingInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (RouteMappingInterface $router) {
	$router->group('/api/v1', function(RouteMappingInterface $router) {
		$router->get('/foo', [FooController::class, 'getAll']);
		$router->get('/foo/{id}', [FooController::class, 'getById']);
		$router->post('/foo', [FooController::class, 'create']);
		$router->put('/foo/{id}', [FooController::class, 'update']);
		$router->patch('/foo/{id}', [FooController::class, 'partialUpdate']);
		$router->delete('/foo/{id}', [FooController::class, 'delete']);
	});

	$router->get('/create', [UserController::class, 'create']);
	$router->get('/all', [UserController::class, 'getAll']);
	$router->get('/foo/{id}', [UserController::class, 'getById']);
	$router->put('/bar/{id}', [UserController::class, 'update']);
	$router->get('/baz/{id}', [UserController::class, 'delete']);
};
