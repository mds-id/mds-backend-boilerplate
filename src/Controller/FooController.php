<?php

declare(strict_types=1);

namespace Bluepeer\Controller;

use Bluepeer\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class FooController extends AbstractController
{
	public function getAll(Request $request, Response $response, array $args): Response
	{
		return $this->asJson(
			$response,
			[
				'req_id' => sha1(uniqid()),
				'route'  => $request->getRequestTarget(),
				'method' => $request->getMethod(),
				'args'   => $args
			]
		);
	}

	public function getById(Request $request, Response $response, array $args): Response
	{
		return $this->asJson(
			$response,
			[
				'req_id' => sha1(uniqid()),
				'route'  => $request->getRequestTarget(),
				'method' => $request->getMethod(),
				'args'   => $args
			]
		);
	}

	public function create(Request $request, Response $response, array $args): Response
	{
		return $this->asJson(
			$response,
			[
				'req_id' => sha1(uniqid()),
				'route'  => $request->getRequestTarget(),
				'method' => $request->getMethod(),
				'args'   => $args
			]
		);
	}

	public function update(Request $request, Response $response, array $args): Response
	{
		return $this->asJson(
			$response,
			[
				'req_id' => sha1(uniqid()),
				'route'  => $request->getRequestTarget(),
				'method' => $request->getMethod(),
				'args'   => $args
			]
		);
	}

	public function partialUpdate(Request $request, Response $response, array $args): Response
	{
		return $this->asJson(
			$response,
			[
				'req_id' => sha1(uniqid()),
				'route'  => $request->getRequestTarget(),
				'method' => $request->getMethod(),
				'args'   => $args
			]
		);
	}

	public function delete(Request $request, Response $response, array $args): Response
	{
		return $this->asJson(
			$response,
			[
				'req_id' => sha1(uniqid()),
				'route'  => $request->getRequestTarget(),
				'method' => $request->getMethod(),
				'args'   => $args
			]
		);
	}
}
