<?php

declare(strict_types=1);

namespace Bluepeer\Core\Dbal;

use Bluepeer\Core\Inflector\InflectorFactoryInterface;
use Bluepeer\Core\Model\ModelInterface;
use Bluepeer\Core\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface EntityInterface
{
	/**
	 * @var int
	 */
	const MODEL_PROPERTY_ACCESS_READ = 1;

	/**
	 * @var int
	 */
	const MODEL_PROPERTY_ACCESS_WRITE = 2;

	/**
	 * Get connection object.
	 *
	 * @return \Doctrine\DBAL\Connection
	 */
	public function getConnection(): Connection;

	/**
	 * Set connection object.
	 *
	 * @param \Doctrine\DBAL\Connection $connection
	 * @return void
	 */
	public function setConnection(Connection $connection);

	/**
	 * Get inflector factory class object.
	 *
	 * @return \Bluepeer\Core\Inflector\InflectorFactoryInterface
	 */
	public function getInflectorFactory(): InflectorFactoryInterface;

	/**
	 * Set inflector factory class object.
	 *
	 * @param \Bluepeer\Core\Inflector\InflectorFactoryInterface $factory
	 * @return void
	 */
	public function setInflectorFactory(InflectorFactoryInterface $factory);

	/**
	 * Persist model object into database.
	 *
	 * @param \Bluepeer\Core\Model\ModelInterface $model
	 * @return void
	 */
	public function persist(ModelInterface $model);

	/**
	 * Persist existing fetched single model class
	 * object into database.
	 *
	 * @param \Bluepeer\Core\Model\ModelInterface $model
	 * @return void
	 */
	public function save(ModelInterface $model);

	/**
	 * Remove existing fetched single model class
	 * object from database.
	 *
	 * @param \Bluepeer\Core\Model\ModelInterface $model
	 * @return void
	 */
	public function remove(ModelInterface $model);

	/**
	 * Get repository class object for related
	 * model class.
	 *
	 * @param string $model
	 * @return \Bluepeer\Core\Repository\RepositoryInterface
	 */
	public function getRepository(string $model): RepositoryInterface;

	/**
	 * Get model class object properties.
	 *
	 * @param \Bluepeer\Core\Model\ModelInterface $model
	 * @return array
	 */
	public function getModelClassProperties(ModelInterface $model): array;

	/**
	 * Access model class object property.
	 *
	 * @var \Bluepeer\Core\Model\ModelInterface $model
	 * @var string $name
	 * @var int $op
	 * @var $data mixed|null
	 * @return mixed|null
	 */
	public function modelPropertyAccessor(
		ModelInterface $model,
		string $name,
		int $op,
		$data = null
	);
}
