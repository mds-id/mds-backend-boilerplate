<?php

declare(strict_types=1);

use Modspace\Controller\BookController;
use Modspace\Controller\UserController;
use Modspace\Core\KernelInterface;
use Modspace\Core\RouteMappingInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (RouteMappingInterface $router) {
	$router->group('/api/v1', function(RouteMappingInterface $router) {
		$router->get('/user', [UserController::class, 'all']);
		$router->get('/user/{id}', [UserController::class, 'getById']);
		$router->post('/user', [UserController::class, 'create']);
		$router->put('/user/{id}', [UserController::class, 'update']);
		$router->delete('/user/{id}', [UserController::class, 'delete']);

		$router->get('/book', [BookController::class, 'all']);
		$router->get('/book/{book_id}', [
			BookController::class,
			'getById'
		]);
		$router->post('/book/catalog/{catalog_id}', [BookController::class, 'create']);
		$router->put('/book/{book_id}/catalog/{catalog_id}', [
			BookController::class, 'update'
		]);
		$router->delete('/book/{book_id}/catalog/{catalog_id}', [
			BookController::class,
			'delete'
		]);
	});
};
