<?php

declare(strict_types=1);

namespace Modspace\Core\Model;

use Modspace\Core\Repository\RepositoryInterface;

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
	 * Get associated model's foreign key constraint.
	 *
	 * @return string
	 */
	public function getForeignKey(): string;

	/**
	 * Get associated model's primary key.
	 *
	 * @return string
	 */
	public function getPrimaryKey(): string;

	/**
	 * Get relation target entity class.
	 *
	 * @return string
	 */
	public function getRelationTargetClass(): string;

	/**
	 * Get relation target primary key constraint.
	 *
	 * @return string
	 */
	public function getRelationTargetPrimaryKey(): string;

	/**
	 * Get property name where relation object will
	 * bounded to.
	 *
	 * @return string
	 */
	public function getRelationBindObject(): string;

	/**
	 * Get entity class relation type.
	 *
	 * @return int
	 */
	public function getRelationType(): int;

	/**
	 * If current entity class object was deleted,
	 * determine if it want to remove related record
	 * with foreign key bounded to it's current entity
	 * class object's primary key or otherwise.
	 *
	 * @return bool
	 */
	public function isOrphanRemoval(): bool;
}
