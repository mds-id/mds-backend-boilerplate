<?php

declare(strict_types=1);

namespace Modspace\Controller;

use ErrorException;
use Modspace\Core\Controller\AbstractController;
use Modspace\Entity\Catalog;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use function iterator_to_array;

class CatalogController extends AbstractController
{
	public function all(Request $request, Response $response, array $args): Response
	{
		$catalogs = $this->getEntity()
			->getRepository(Catalog::class)
			->findAll();
		$result   = [];

		foreach ($catalogs as $catalog) {
			$result[] = [
				'id' => $catalog->getId(),
				'catalog_name' => $catalog->getCatalogName(),
				'books' => array_map(function($el) {
					return [
						'id' => $el->getId(),
						'title' => $el->getTitle(),
					];
				}, $catalog->getBooks()->toArray())
			];
		}

		return $this->asJson($response, $result);
	}

	public function getById(Request $request, Response $response, array $args): Response
	{
		$entity  = $this->getEntity();
		$catalog = $entity->getRepository(Catalog::class)
			->find($args['catalog_id']);

		if (null === $catalog) {
			return $this->handleThrowedException(
				$response,
				new ErrorException(
					sprintf(
						'Data catalog with id \'%s\' not found.',
						$args['catalog_id']
					)
				)
			);
		}

		return $this->asJson(
			$response,
			[
				'id' => $catalog->getId(),
				'catalog_name' => $catalog->getCatalogName(),
				'books' => array_map(function($el) {
					return [
						'id' => $el->getId(),
						'title' => $el->getTitle()
					];
				}, $catalog->getBooks()->toArray())
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

		$entity  = $this->getEntity();
		$catalog = new Catalog();

		$catalog->setCatalogName($payload['catalog_name']);
		$entity->persist($catalog);

		return $this->asJson(
			$response,
			[
				'id' => $catalog->getId(),
				'catalog_name' => $catalog->getCatalogName()
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

		$entity  = $this->getEntity();
		$catalog = $entity->getRepository(Catalog::class)
			->find($args['catalog_id']);

		if (null === $catalog) {
			return $this->handleThrowedException(
				$response,
				new ErrorException(
					sprintf(
						'Catalog with id \'%s\' not found.',
						$args['catalog_id']
					)
				)
			);
		}

		$catalog->setCatalogName($payload['catalog_name']);
		$entity->save($catalog);

		return $this->asJson(
			$response,
			[
				'id' => $catalog->getId(),
				'catalog_name' => $catalog->getCatalogName()
			]
		);
	}

	public function remove(Request $request, Response $response, array $args): Response
	{
	}
}
