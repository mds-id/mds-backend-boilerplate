<?php

declare(strict_types=1);

namespace Modspace\Core\Controller;

use RuntimeException;
use Throwable;
use Modspace\Core\ContainerAwareInterface;
use Modspace\Core\Dbal\EntityInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

use function json_encode;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
abstract class AbstractController implements ContainerAwareInterface
{
	/**
	 * @var int
	 */
	const JSON_AS_ARRAY = 1;

	/**
	 * @var int
	 */
	const JSON_AS_OBJECT = 2;

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
	 * @param \Psr\Http\Message\RequestInterface $request
	 * @param int $jsonForm
	 * @return array|object
	 */
	public function getJson(
		RequestInterface $request,
		int $jsonForm = AbstractController::JSON_AS_ARRAY
	) {
		if ($request->getHeader('Content-Type')[0] !== 'application/json') {
			throw new RuntimeException(
				"'Content-Type' must be 'application/json'."
			);
		}

		return json_decode(
			$request->getBody()->getContents(),
			$jsonForm === AbstractController::JSON_AS_ARRAY
				? true
				: false
		);
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
