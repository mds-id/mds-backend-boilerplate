<?php
declare(strict_types=1);

use Modspace\Core\Kernel;
use Modspace\Core\RouteMapping;
use DI\ContainerBuilder;
use Slim\ResponseEmitter;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
	$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

$kernel = new Kernel($containerBuilder->build());

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($kernel);

// Initiate the route mapper.
$router = new RouteMapping($kernel);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($router);

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$responseFactory = $kernel->getResponseFactory();

// Add Routing Middleware
$kernel->addRoutingMiddleware();

// Run App & Emit Response
$response = $router->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
