<?php

declare(strict_types=1);

namespace Modspace\Controller;

use Modspace\Core\Controller\AbstractController;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class StudentsController extends AbstractController
{
	public function all(Request $request, Response $response, array $args): Response
	{
		$students = $this->getEntity()
			->getRepository(Students::class)
			->findAll();
		$results = [];

		foreach ($students as $student) {
			$results[] = [
				'id' => $student->getId(),
				'name' => $student->getName(),
				'contact_info' => [
					'id' => $student->getContactInfo()->getId(),
					'city' => $student->getContactInfo()->getCity(),
					'phone' => $student->getContactInfo()->getPhone()
				]
			];
		}

		return $this->asJson($response, $results);
	}

	public function getById(Request $request, Response $response, array $args): Response
	{
		$student = $this->getEntity()
			->getRepository(Students::class)
			->find($args['students_id']);

		if (null === $student) {
			return $this->handleThrowedException(
				$response,
				new ErrorException(
					sprintf(
						'Data student with id \'%s\' not found.',
						$args['student_id']
					)
				)
			);
		}

		return $this->asJson(
			$response,
			[
				'id' => $student->getId(),
				'name' => $student->getName(),
				'contact_info' => [
					'id' => $student->getContactInfo()->getId(),
					'city' => $student->getContactInfo()->getCity(),
					'phone' => $student->getContactInfo()->getPhone()
				]
			]
		);
	}

	public function create(Request $request, Response $response, array $args): Response
	{
		try {
			$payload = $this->getJson($request);
		} catch (Throwable $e) {
			return $this->handleThrowedException($response, $e);
		}

		$entity = $this->getEntity();
		$student = new Students();

		$student->getName($payload['name']);
		$entity->persist($student);

		return $this->asJson(
			$response,
			[
				'id' => $student->getId(),
				'name' => $student->getName()
			]
		);
	}

	public function update(Request $request, Response $response, array $args): Response
	{
		try {
			$payload = $this->getJson($request);
		} catch (Throwable $e) {
			return $this->handleThrowedException($response, $e);
		}

		$entity = $this->getEntity();
		$student = $entity->getRepository(Students::class)
			->find($args['students_id']);

		if (null === $student) {
			return $this->handleThrowedException(
				$response,
				new ErrorException(
					sprintf(
						'Student with id \'%s\' not found.',
						$args['students_id']
					)
				)
			);
		}

		$student->setName($payload['name']);
		$entity->save($student);

		return $this->asJson(
			$response,
			[
				'id' => $student->getId(),
				'name' => $student->getName()
			]
		);
	}

	public function delete(Request $request, Response $response, array $args): Response
	{
		$entity = $this->getEntity();
		$student = $entity->getRepository(Students::class)
			->find($args['students_id']);

		if (null === $student) {
			return $this->handleThrowedException(
				$response,
				new ErrorException(
					sprintf(
						'Student with id \'%s\' not found.',
						$args['students_id']
					)
				)
			);
		}

		$entity->remove($student);

		return $this->asJson(
			$response,
			[
				'message' => sprintf(
					'Student with id \'%s\' was removed.',
					$args['students_id']
				)
			]
		);
	}
}
