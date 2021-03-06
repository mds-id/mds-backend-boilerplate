<?php

declare(strict_types=1);

namespace Bluepeer\Core;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface KernelInterface extends ContainerInterface
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

	/**
	 * {@inheritdoc}
	 */
	public function get($id);

	/**
	 * {@inheritdoc}
	 */
	public function has($id);
}
