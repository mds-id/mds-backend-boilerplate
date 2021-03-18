<?php

declare(strict_types=1);

namespace Bluepeer\Core\Model;

use Bluepeer\Core\Repository\RepositoryInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface ModelInterface
{
	/**
	 * Get table name.
	 *
	 * @return string
	 */
	public function getTable(): string;

	/**
	 * Set table name.
	 *
	 * @param string
	 * @return void
	 */
	public function setTable(string $name);

	/**
	 * Get repository class object.
	 *
	 * @return string
	 */
	public function getRepository(): string;

	/**
	 * Set repository class object.
	 *
	 * @param string $repository
	 * @return void
	 */
	public function setRepository(string $repository);

	/**
	 * Get associated model's primary key.
	 *
	 * @return string
	 */
	public function getPrimaryKey(): string;
}
