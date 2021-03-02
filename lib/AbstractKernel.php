<?php

namespace Bluepeer\Core;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
abstract class AbstractKernel implements KernelInterface
{
	/**
	 * @var \Psr\Http\Server\RequestHandlerInterface
	 */
	private $handler;

	/**
	 * @return static
	 */
	public function __construct(RequestHandlerInterface $handler)
	{
		$this->setHandler($handler);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getHandler(): RequestHandlerInterface
	{
		return $this->handler;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setHandler(RequestHandlerInterface $handler)
	{
		$this->handler = $handler;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($id)
	{
		return $this->getHandler()->get($id);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has($id)
	{
		return $this->getHandler()->has($id);
	}
}
