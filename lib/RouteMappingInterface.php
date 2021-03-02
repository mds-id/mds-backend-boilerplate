<?php

declare(strict_types=1);

namespace Bluepeer\Core;

use Slim\Interfaces\RouteInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface RouteMappingInterface
{
	/**
	 * Map current route pattern with 'GET' method.
	 *
	 * @param string $pattern
	 * @param array $callable
	 * @return \Slim\Interfaces\RouteInterface
	 */
	public function get(string $pattern, array $callable): RouteInterface;

	/**
	 * Map current route pattern with 'POST' method.
	 *
	 * @param string $pattern
	 * @param array $callable
	 * @return \Slim\Interfaces\RouteInterface
	 */
	public function post(string $pattern, array $callable): RouteInterface;

	/**
	 * Map current route pattern with 'PUT' method.
	 *
	 * @param string $pattern
	 * @param array $callable
	 * @return \Slim\Interfaces\RouteInterface
	 */
	public function put(string $pattern, array $callable): RouteInterface;

	/**
	 * Map current route pattern with 'PATCH' method.
	 *
	 * @param string $pattern
	 * @param array $callable
	 * @return \Slim\Interfaces\RouteInterface
	 */
	public function patch(string $pattern, array $callable): RouteInterface;

	/**
	 * Map current route pattern with 'DELETE' method.
	 *
	 * @param string $pattern
	 * @param array $callable
	 * @return \Slim\Interfaces\RouteInterface
	 */
	public function delete(string $pattern, array $callable): RouteInterface;

	/**
	 * Map current route pattern with 'OPTIONS' method.
	 *
	 * @param string $pattern
	 * @param array $callable
	 * @return \Slim\Interfaces\RouteInterface
	 */
	public function options(string $pattern, array $callable): RouteInterface;

	/**
	 * Map current route pattern with any HTTP method except 'HEAD'
	 * and 'CONNECT'.
	 *
	 * @param string $pattern
	 * @param array $callable
	 * @return \Slim\Interfaces\RouteInterface
	 */
	public function any(string $pattern, array $callable): RouteInterface;

	/**
	 * Map current route pattern with any given HTTP method.
	 *
	 * @param array $methods
	 * @param string $pattern
	 * @param array $callable
	 * @return \Slim\Interfaces\RouteInterface
	 */
	public function map(array $methods, string $pattern, array $callable): RouteInterface;
}
