<?php

declare(strict_types=1);

namespace Bluepeer\Controller;

use Bluepeer\Core\EntityInterface;
use Psr\Http\Message\Response as ConcreteResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class FooController extends AbstractController
{
	private $entity;

	public function __construct(EntityInterface $entity)
	{
		$this->entity = $entity;
	}

	public function doShit(Request $request, Response $response, array $args): Response
	{
		dump($this->entity);
		return new ConcreteResponse();
	}
}
