<?php

declare(strict_types=1);

namespace Modspace\Controller;

use Modspace\Entity\ContactInfo;
use Modspace\Entity\Students;
use Modspace\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class ContactInfoController extends AbstractController
{
	public function all(Request $request, Response $response, array $args): Response
	{
		$contactInfos = $this->getEntity()
			->getRepository(ContactInfo::class)
			->findAll();
		$results = [];

		foreach ($contactInfos as $contactInfo) {
			$results[] = [
				'id' => $contactInfo->getId(),
				'city' => $contactInfo->getCity(),
				'phone' => $contactInfo->getPhone(),
				'student' => null === $contactInfo->getStudent()
					? []
					: [
						'id' => $contactInfo->getStudent()->getId(),
						'name' => $contactInfo->getStudent()->getName()
					]
			];
		}

		return $this->asJson($response, $results);
	}

	public function getById(Request $request, Response $response, array $args): Response
	{
		$contactInfo = $this->getEntity()
			->getRepository(ContactInfo::class)
			->find($args['ci_id']);

		if (null === $contactInfo) {
			return $this->handleThrowedException(
				$response,
				new ErrorException(
					sprintf(
						'Contact info with id \'%s\' not found.',
						$args['ci_id']
					)
				)
			);
		}

		return $this->asJson(
			$response,
			[
				'id' => $contactInfo->getId(),
				'city' => $contactInfo->getCity(),
				'phone' => $contactInfo->getPhone(),
				'student' => null === $contactInfo->getStudent()
					? []
					: [
						'id' => $contactInfo->getStudent()->getId(),
						'name' => $contactInfo->getStudent()->getName()
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
		$student = $entity->getRepository(Students::class);

		if (null === $student) {
			return $this->handleThrowedException(
				$response,
				new ErrorException(
					sprintf(
						'Contact info with \'%s\' not found.',
						$args['ci_id']
					)
				)
			);
		}

		$contactInfo = new ContactInfo();
		$contactInfo->setCity($payload['city']);
		$contactInfo->setPhone($payload['phone']);
		$contactInfo->setStudent($student);

		$entity->persist($contactInfo);

		return $this->asJson(
			$response,
			[
				'id' => $contactInfo->getId(),
				'city' => $contactInfo->getCity(),
				'phone' => $contactInfo->getPhone(),
				'student' => [
					'id' => $contactInfo->getStudent()->getId(),
					'name' => $contactInfo->getStudent()->getName()
				]
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
		$contactInfo = $entity->getRepository(ContactInfo::class)
			->find($args['ci_id']);

		if (null === $contactInfo) {
			return $this->handleThrowedException(
				$response,
				new ErrorException(
					sprintf(
						'Contact info with id \'%s\' not found.',
						$args['ci_id']
					)
				)
			);
		}

		$contactInfo->setCity($payload['city']);
		$contactInfo->setPhone($payload['phone']);

		$entity->save($contactInfo);

		return $this->asJson(
			$response,
			[
				'id' => $contactInfo->getId(),
				'city' => $contactInfo->getCity(),
				'phone' => $contactInfo->getPhone(),
				'student' => null === $contactInfo->getStudent()
					? []
					: [
						'id' => $contactInfo->getStudent()->getId(),
						'name' => $contactInfo->getStudent()->getName()
					]
			]
		);
	}

	public function remove(Request $request, Response $response, array $args): Response
	{
		$entity = $this->getEntity();
		$contactInfo = $entity->getRepository(ContactInfo::class)
			->find($args['ci_id']);

		if (null === $contactInfo) {
			return $this->handleThrowedException(
				$response,
				new ErrorException(
					sprintf(
						'Contact info with id \'%s\' not found.',
						$args['ci_id']
					)
				)
			);
		}

		$entity->remove($contactInfo);

		return $this->asJson(
			$response,
			[
				'message' => sprintf(
					'Contact info with id \'%s\' has been removed.'
				)
			]
		);
	}
}
