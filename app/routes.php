<?php

declare(strict_types=1);

use Bluepeer\Controller\FooController;
use Bluepeer\Core\KernelInterface;
use Bluepeer\Core\RouteMappingInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (RouteMappingInterface $router) {
    $router->get('/', [FooController::class, 'doShit']);
};
