<?php

declare(strict_types=1);

namespace Modspace\Controller;

use Modspace\Entity\Book;
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
	}

	public function create(Request $request, Response $response, array $args): Response
	{
	}

	public function update(Request $request, Response $response, array $args): Response
	{
	}

	public function delete(Request $request, Response $response, array $args): Response
	{
	}
}
