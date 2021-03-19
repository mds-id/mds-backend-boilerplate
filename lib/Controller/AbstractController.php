<?php

declare(strict_types=1);

namespace Modspace\Core\Controller;

use Throwable;
use Modspace\Core\ContainerAwareInterface;
use Modspace\Core\Dbal\EntityInterface;
use Fig\Http\Message\StatusCodeInterface;
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
	 * @return \Modspace\Core\Dbal\EntityInterface
	 */
	public function getEntity(): EntityInterface
	{
		return $this->getContainer()->get(EntityInterface::class);
	}

	/**
	 * @param \Psr\Http\Message\ResponseInterface $response
	 * @param array $data
	 * @param int $code
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function asJson(
		ResponseInterface $response,
		array $data,
		int $code = StatusCodeInterface::STATUS_OK
	): ResponseInterface {
		$response = $response->withHeader('Content-Type', 'application/json');
		$response
			->getBody()
			->write(json_encode($data));
		return $response->withStatus($code);
	}

	/**
	 * Handle throwed exception as JSON response.
	 *
	 * @param \Psr\Http\Message\ResponseInterface $response
	 * @param \Throwable $e
	 * @param int $code
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function handleThrowedException(
		ResponseInterface $response,
		Throwable $e,
		int $code = StatusCodeInterface::STATUS_BAD_REQUEST
	): ResponseInterface {
		$fault = [
			'message' => $e->getMessage(),
			'code'    => $e->getCode(),
			'file'    => $e->getFile(),
			'line'    => $e->getLine(),
			'trace'   => $e->getTrace()
		];

		return $this->asJson($response, $fault, $code);
	}
}
