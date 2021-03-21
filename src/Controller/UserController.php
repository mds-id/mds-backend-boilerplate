<?php

declare(strict_types=1);

namespace Modspace\Controller;

use Modspace\Entity\User;
use Modspace\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class UserController extends AbstractController
{
	public function all(Request $request, Response $response, array $args): Response
	{
		$users = $this->getEntity()
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

	public function getById(Request $request, Response $response, array $args): Response
	{
		return $response;
	}

	public function create(Request $request, Response $response, array $args): Response
	{
		$entity = $this->getEntity();
		$user   = new User();

		$user->setName('fitrah');
		$user->setEmail('fitrah@gmail.com');

		$entity->persist($user);

		return $this->asJson(
			$response,
			[
				'id' => $user->getId(),
				'name' => $user->getName(),
				'email' => $user->getEmail()
			]
		);
	}

	public function update(Request $request, Response $response, array $args): Response
	{
		$entity = $this->getEntity();
		$user = $entity->getRepository(User::class)
			->find($args['id']);

		if (null === $user) {
			return $this->asJson($response, []);
		}

		$user->setName('daus');
		$user->setEmail('daus@example.com');

		$entity->save($user);

		return $this->asJson(
			$response,
			[
				'id' => $user->getId(),
				'name' => $user->getName(),
				'email' => $user->getEmail()
			]
		);
	}

	public function delete(Request $request, Response $response, array $args): Response
	{
		$entity = $this->getEntity();
		$user = $entity->getRepository(User::class)
			->find($args['id']);

		if (null === $user) {
			return $this->asJson($response, []);
		}

		try {
			$entity->remove($user);
		} catch (Throwable $e) {
			return $this->handleThrowedException($response, $e);
		}

		return $this->asJson(
			$response,
			[]
		);
	}
}
