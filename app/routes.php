<?php

declare(strict_types=1);

use Modspace\Controller\BookController;
use Modspace\Controller\CatalogController;
use Modspace\Controller\ContactInfoController;
use Modspace\Controller\StudentsController;
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

		$router->get('/catalog', [CatalogController::class, 'all']);
		$router->get('/catalog/{catalog_id}', [CatalogController::class, 'getById']);
		$router->post('/catalog', [CatalogController::class, 'create']);
		$router->put('/catalog/{catalog_id}', [CatalogController::class, 'update']);
		$router->delete('/catalog/{catalog_id}', [CatalogController::class, 'remove']);

		// students
		$router->get('/students', [StudentsController::class, 'all']);
		$router->get('/students/{students_id}', [StudentsController::class, 'getById']);
		$router->post('/students', [StudentsController::class, 'create']);
		$router->put('/students/{students_id}', [StudentsController::class, 'update']);
		$router->delete('/students/{students_id}', [StudentsController::class, 'delete']);

		// contact info
		$router->get('/contact_info', [ContactInfoController::class, 'all']);
		$router->get('/contact_info/{ci_id}', [ContactInfoController::class, 'getById']);
		$router->post(
			'/students/{students_id}/contact_info',
			[ContactInfoController::class, 'create']
		);
		$router->put('/contact_info/{ci_id}', [ContactInfoController::class, 'update']);
		$router->delete('/contact_info/{ci_id}', [ContactInfoController::class, 'remove']);
	});
};
