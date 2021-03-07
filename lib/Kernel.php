<?php

namespace Bluepeer\Core;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Middleware\RoutingMiddleware;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
final class Kernel implements KernelInterface
{
	/**
	 * @var \Psr\Http\Server\RequestHandlerInterface
	 */
	private $handler;

	/**
	 * @param \Psr\Container\ContainerInterface $container
	 * @return static
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->initialize($container);
	}

	/**
	 * Initialize class constructor.
	 *
	 * @param \Psr\Container\ContainerInterface $container
	 * @return void
	 */
	private function initialize(ContainerInterface $container)
	{
		AppFactory::setContainer($container);
		$this->setHandler(AppFactory::create());
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
		return $this->getContainer()->get($id);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has($id)
	{
		return $this->getContainer()->has($id);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResponseFactory(): ResponseFactoryInterface
	{
		return $this->getHandler()->getResponseFactory();
	}

	/**
	 * @return \Slim\Middleware\RoutingMiddleware
	 */
	public function addRoutingMiddleware(): RoutingMiddleware
	{
		return $this->getHandler()->addRoutingMiddleware();
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		return $this->getHandler()->handle($request);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getContainer(): ContainerInterface
	{
		return $this->getHandler()->getContainer();
	}
}
