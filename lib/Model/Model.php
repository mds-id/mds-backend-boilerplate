<?php

declare(strict_types=1);

namespace Bluepeer\Core\Model;

use Bluepeer\Core\Repository\RepositoryInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
abstract class Model implements ModelInterface
{
	/**
	 * @var string
	 */
	private $table;

	/**
	 * @param string
	 */
	private $repository;

	/**
	 * @return static
	 */
	public function __construct()
	{
		$this->setTable($this->getDefaultTable());
		$this->setRepository($this->getDefaultRepository());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTable(): string
	{
		return $this->table;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setTable(string $name)
	{
		$this->table = $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRepository(): string
	{
		return $this->repository;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setRepository(string $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * {@inheritdoc}
	 */
	abstract public function getPrimaryKey(): string;

	/**
	 * Get default table name.
	 *
	 * @return string
	 */
	abstract protected function getDefaultTable(): string;

	/**
	 * Get default repository class name.
	 *
	 * @return string
	 */
	abstract protected function getDefaultRepository(): string;
}
