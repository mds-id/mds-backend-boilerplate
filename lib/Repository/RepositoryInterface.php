<?php

declare(strict_types=1);

namespace Bluepeer\Core\Repository;

use Bluepeer\Core\Dbal\EntityInterface;
use Bluepeer\Core\Model\ModelInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface RepositoryInterface
{
	/**
	 * Get entity class object.
	 *
	 * @return \Bluepeer\Core\Dbal\EntityInterface
	 */
	public function getEntity(): EntityInterface;

	/**
	 * Set entity class object.
	 *
	 * @var \Bluepeer\Core\Dbal\EntityInterface $entity
	 * @return void
	 */
	public function setEntity(EntityInterface $entity);

	/**
	 * Get model class object.
	 *
	 * @return \Bluepeer\Core\Model\ModelInterface
	 */
	public function getModel(): ModelInterface;

	/**
	 * Set model class object.
	 *
	 * @var \Bluepeer\Core\Model\ModelInterface $model
	 * @return void
	 */
	public function setModel(ModelInterface $model);

	/**
	 * Get Doctrine DBAL query builder class object.
	 *
	 * @return \Doctrine\DBAL\Query\QueryBuilder
	 */
	public function getQueryBuilder();

	/**
	 * Get all entries from associated entity and deserialize
	 * it into array of model class object.
	 *
	 * @return array
	 */
	public function findAll(): array;

	/**
	 * Get entry from associated entity by it's primary key
	 * value and deserialize it into model class object.
	 *
	 * @return \Bluepeer\Core\Model\ModelInterface|null
	 */
	public function find($id);
}