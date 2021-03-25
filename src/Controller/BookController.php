<?php

declare(strict_types=1);

namespace Modspace\Controller;

use ErrorException;
use Modspace\Entity\Book;
use Modspace\Entity\Catalog;
use Modspace\Core\Controller\AbstractController;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class BookController extends AbstractController
{
	public function all(Request $request, Response $response, array $args): Response
	{
		$books = $this->getEntity()
			->getRepository(Book::class)
			->findAll();
		$result = [];

		foreach ($books as $book) {
			$result[] = [
				'id' => $book->getId(),
				'title' => $book->getTitle(),
				'catalog' => [
					'id' => $book->getCatalog()->getId(),
					'catalog_name' => $book->getCatalog()->getCatalogName()
				]
			];
		}

		return $this->asJson($response, $result);
	}

	public function getById(Request $request, Response $response, array $args): Response
	{
		$book = $this->getEntity()
			->getRepository(Book::class)
			->find($args['book_id']);

		if ($book === null) {
			return $this->asJson($response, []);
		}

		return $this->asJson(
			$response,
			[
				'id' => $book->getId(),
				'title' => $book->getTitle(),
				'catalog' => [
					'id' => $book->getCatalog()->getId(),
					'catalog_name' => $book->getCatalog()->getCatalogName()
				]
			]
		);
	}

	public function create(Request $request, Response $response, array $args): Response
	{
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

		$payload = $this->getJson($request);

		$book = new Book();
		$book->setTitle($payload['title']);
		$book->setCatalog($catalog);

		$entity->persist($book);

		return $this->asJson(
			$response,
			[
				'id' => $book->getId(),
				'title' => $book->getTitle(),
				'catalog' => [
					'id' => $book->getCatalog()->getId(),
					'catalog_name' => $book->getCatalog()->getCatalogName()
				]
			]
		);
	}

	public function update(Request $request, Response $response, array $args): Response
	{
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

		$book = $entity->getRepository(Book::class)
			->find($args['book_id']);

		if (null === $book) {
			return $this->handleThrowedException(
				$response,
				new ErrorException(
					sprintf(
						'Book with id \'%s\' not found.',
						$args['book_id']
					)
				)
			);
		}

		$payload = $this->getJson($request);

		$book->setTitle($payload['title']);
		$book->setCatalog($catalog);

		$entity->save($book);

		return $this->asJson(
			$response,
			[
				'id' => $book->getId(),
				'title' => $book->getTitle(),
				'catalog' => [
					'id' => $book->getCatalog()->getId(),
					'catalog_name' => $book->getCatalog()->getCatalogName()
				]
			]
		);
	}

	public function delete(Request $request, Response $response, array $args): Response
	{
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

		$book = $entity->getRepository(Book::class)
			->find($args['book_id']);

		if (null === $book) {
			return $this->handleThrowedException(
				$response,
				new ErrorException(
					sprintf(
						'Book with id \'%s\' not found.',
						$args['book_id']
					)
				)
			);
		}

		$entity->remove($book);

		return $this->asJson(
			$response,
			[
				'message' => sprintf(
					'Data with id \'%s\' deleted successfully.',
					$book->getId()
				)
			]
		);
	}
}
