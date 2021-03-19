<?php

declare(strict_types=1);

namespace Modspace\Core\Dbal;

use Modspace\Core\Inflector\InflectorFactoryInterface;
use Modspace\Core\Model\ModelInterface;
use Modspace\Core\Repository\RepositoryInterface;
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
	 * @return \Modspace\Core\Inflector\InflectorFactoryInterface
	 */
	public function getInflectorFactory(): InflectorFactoryInterface;

	/**
	 * Set inflector factory class object.
	 *
	 * @param \Modspace\Core\Inflector\InflectorFactoryInterface $factory
	 * @return void
	 */
	public function setInflectorFactory(InflectorFactoryInterface $factory);

	/**
	 * Persist model object into database.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return void
	 */
	public function persist(ModelInterface $model);

	/**
	 * Persist existing fetched single model class
	 * object into database.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return void
	 */
	public function save(ModelInterface $model);

	/**
	 * Remove existing fetched single model class
	 * object from database.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return void
	 */
	public function remove(ModelInterface $model);

	/**
	 * Get repository class object for related
	 * model class.
	 *
	 * @param string $model
	 * @return \Modspace\Core\Repository\RepositoryInterface
	 */
	public function getRepository(string $model): RepositoryInterface;

	/**
	 * Get model class object properties.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return array
	 */
	public function getModelClassProperties(ModelInterface $model): array;

	/**
	 * Access model class object property.
	 *
	 * @var \Modspace\Core\Model\ModelInterface $model
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
