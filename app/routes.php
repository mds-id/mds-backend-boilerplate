<?php

declare(strict_types=1);

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
	});
};
