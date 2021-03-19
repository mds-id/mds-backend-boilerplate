<?php

declare(strict_types=1);

namespace Modspace\Core;

use InvalidArgumentException;
use Modspace\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteCollectorProxy;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class RouteMapping implements RouteMappingInterface
{
	/**
	 * @var Modspace\Core\KernelInterface
	 */
	private $kernel;

	/**
	 * @var boolean
	 */
	private $grouped = false;

	/**
	 * @var string
	 */
	private $routeGroup;

	/**
	 * @param \Modspace\Core\KernelInterface $kernel
	 * @return static
	 */
	public function __construct(KernelInterface $kernel)
	{
		$this->kernel = $kernel;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(string $pattern, array $callable): RouteInterface
	{
		return $this->map(['GET'], $pattern, $callable);
	}

	/**
	 * {@inheritdoc}
	 */
	public function post(string $pattern, array $callable): RouteInterface
	{
		return $this->map(['POST'], $pattern, $callable);
	}

	/**
	 * {@inheritdoc}
	 */
	public function put(string $pattern, array $callable): RouteInterface
	{
		return $this->map(['PUT'], $pattern, $callable);
	}

	/**
	 * {@inheritdoc}
	 */
	public function patch(string $pattern, array $callable): RouteInterface
	{
		return $this->map(['PATCH'], $pattern, $callable);
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete(string $pattern, array $callable): RouteInterface
	{
		return $this->map(['DELETE'], $pattern, $callable);
	}

	/**
	 * {@inheritdoc}
	 */
	public function options(string $pattern, array $callable): RouteInterface
	{
		return $this->map(['OPTIONS'], $pattern, $callable);
	}

	/**
	 * {@inheritdoc}
	 */
	public function any(string $pattern, array $callable): RouteInterface
	{
		return $this->map(
			['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
			$pattern,
			$callable
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function map(array $methods, string $pattern, array $callable): RouteInterface
	{
		$this->checkCallableRequirements($callable);

		if ($this->isGrouped()) {
			$pattern = $this->getRouteGroup() . $pattern;
		}

		return $this->kernel
			->getHandler()
			->map(
				$methods,
				$pattern,
				[$this->kernel->get($callable[0]), $callable[1]]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function group(string $pattern, $callable)
	{
		$this->useGroup(true);
		$this->setRouteGroup($pattern);

		$callable($this);

		$this->useGroup(false);
	}

	/**
	 * {@inheritdoc}
	 */
	public function useGroup(bool $grouped)
	{
		$this->grouped = $grouped;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGrouped(): bool
	{
		return $this->grouped;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRouteGroup(): string
	{
		return $this->routeGroup;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setRouteGroup(string $route)
	{
		$this->routeGroup = $route;
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		return $this->kernel->handle($request);
	}

	/**
	 * Check route callable requirements.
	 *
	 * @param array $callable
	 * @return void
	 */
	private function checkCallableRequirements(array $callable)
	{
		if (!class_exists($callable[0])) {
			throw new InvalidArgumentException(
				sprintf(
					"Controller with name '%s' is not exists.",
					$callable[0]
				)
			);
		}

		if (!is_subclass_of($callable[0], AbstractController::class, true)) {
			throw new InvalidArgumentException(
				sprintf(
					"Controller with name '%s' must be instance of '%s'",
					$callable[0],
					AbstractController::class
				)
			);
		}

		if (!method_exists($callable[0], $callable[1])) {
			throw new InvalidArgumentException(
				"Method with name '%s' not exists in controller '%s'.",
				$callable[1],
				$callable[0]
			);
		}
	}
}
