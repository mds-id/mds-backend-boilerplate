<?php

declare(strict_types=1);

namespace Bluepeer\Core\Controller;

use Bluepeer\Core\ContainerAwareInterface;
use Bluepeer\Core\Dbal\EntityInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

use function json_encode;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
abstract class AbstractController implements ContainerAwareInterface
{
	/**
	 * @var \Psr\Container\ContainerInterface
	 */
	private $container;

	/**
	 * @param \Psr\Container\ContainerInterface $container
	 * @return static
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getContainer(): ContainerInterface
	{
		return $this->container;
	}

	/**
	 * Get entity class object.
	 *
	 * @return \Bluepeer\Core\Dbal\EntityInterface
	 */
	public function getEntity(): EntityInterface
	{
		return $this->getContainer()->get(EntityInterface::class);
	}

	/**
	 * @param \Psr\Http\Message\ResponseInterface $response
	 * @param array $data
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function asJson(ResponseInterface $response, array $data): ResponseInterface
	{
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($data));
		return $response;
	}
}
