<?php

declare(strict_types=1);

namespace Bluepeer\Controller;

use Bluepeer\Entity\User;
use Bluepeer\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class UserController extends AbstractController
{
	public function create(Request $request, Response $response, array $args): Response
	{
		$entity = $this->getEntity();
		$user   = new User();

		$user->setName('foo #1');
		$user->setEmail('foo@example.com');

		$entity->persist($user);
		return $response;
	}

	public function getAll(Request $request, Response $response, array $args): Response
	{
		$users  = $this->getEntity()
			->getRepository(User::class)
			->findAll();
		$result = [];

		foreach ($users as $user) {
			$result[] = [
				'id' => $user->getId(),
				'name' => $user->getName(),
				'email' => $user->getEmail()
			];
		}

		return $this->asJson($response, $result);
	}
}
