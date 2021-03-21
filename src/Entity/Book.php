<?php

declare(strict_types=1);

namespace Modspace\Entity;

use Modspace\Core\Model\Model;
use Modspace\Core\Relation\RelationType;

class Book extends Model
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var \Modspace\Entity\Catalog
	 */
	private $catalog;

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
	 * Get title.
	 *
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * Set title.
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle(string $title)
	{
		$this->title = $title;
	}

	/**
	 * Get related catalog class object.
	 *
	 * @return \Modspace\Entity\Catalog
	 */
	public function getCatalog(): Catalog
	{
		return $this->catalog;
	}

	/**
	 * Set related catalog class object.
	 *
	 * @param \Modspace\Entity\Catalog $catalog
	 * @return void
	 */
	public function setCatalog(Catalog $catalog)
	{
		$this->catalog = $catalog;
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
	public function getRelationTargetClass(): string
	{
		return Catalog::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRelationTargetPrimaryKey(): string
	{
		return 'id';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRelationBindObject(): string
	{
		return 'catalog';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRelationType(): int
	{
		return RelationType::MANY_TO_ONE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getForeignKey(): string
	{
		return 'catalogId';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultTable(): string
	{
		return 'book';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultRepository(): string
	{
		return BookRepository::class;
	}
}
