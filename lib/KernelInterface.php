<?php

declare(strict_types=1);

namespace Bluepeer\Core;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface KernelInterface extends
	ContainerInterface,
	ContainerAwareInterface
{
	/**
	 * Get request handler object.
	 *
	 * @return \Psr\Http\Server\RequestHandlerInterface
	 */
	public function getHandler(): RequestHandlerInterface;

	/**
	 * Set request handler object.
	 *
	 * @param \Psr\Http\Server\RequestHandlerInterface $handler
	 * @return void
	 */
	public function setHandler(RequestHandlerInterface $handler);
}
