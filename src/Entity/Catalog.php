<?php

declare(strict_types=1);

namespace Modspace\Entity;

use Modspace\Dbal\Common\Collections\ArrayCollection;
use Modspace\Core\Model\Model;
use Modspace\Core\Model\Relation\RelationType;

class Catalog extends Model
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $catalogName;

	/**
	 * @var \Modspace\Entity\Book[]
	 */
	private $books;

	/**
	 * @return static
	 */
	public function __construct()
	{
		$this->books = new ArrayCollection();
	}

	/**
	 * Get id.
	 *
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * Get catalog name.
	 *
	 * @return string
	 */
	public function getCatalogName(): string
	{
		return $this->catalogName;
	}

	/**
	 * Set catalog name.
	 *
	 * @param string $catalogName
	 * @return void
	 */
	public function setCatalogName(string $catalogName)
	{
		$this->catalogName = $catalogName;
	}

	/**
	 * Add book entity class object into
	 * books collection.
	 *
	 * @param \Modspace\Entity\Book $book
	 * @return void
	 */
	public function addBook(Book $book)
	{
		if (!$this->books->contains($book)) {
			$this->books->append($book);
			$book->setCatalog($this);
		}
	}

	/**
	 * Remove book entity class object from
	 * books collection.
	 *
	 * @param \Modspace\Entity\Book $book
	 * @return void
	 */
	public function removeBook(Book $book)
	{
		if ($this->books->contains($book)) {
			$this->books->remove($book);

			if ($book->getCategory() === $this) {
				$book->setCategory(null);
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPrimaryKey(): string
	{
		return 'id';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRelationMetadata(): array
	{
		return [Category::class, 'id'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRelationType(): int
	{
		return RelationType::ONE_TO_MANY;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultTable(): string
	{
		return 'category';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultRepository(): string
	{
		return CategoryRepository::class;
	}
}
