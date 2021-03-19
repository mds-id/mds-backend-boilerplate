<?php

declare(strict_types=1);

namespace Modspace\Core\Dbal;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use Modspace\Core\Inflector\InflectorFactoryInterface;
use Modspace\Core\Model\ModelInterface;
use Modspace\Core\Repository\RepositoryInterface;
use Doctrine\DBAL\Connection;

use function ucfirst;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Entity implements EntityInterface
{
	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	private $connection;

	/**
	 * @var \Modspace\Core\Inflector\InflectorFactoryInterface
	 */
	private $inflectorFactory;

	/**
	 * @param \Doctrine\DBAL\Connection $connection
	 * @param \Modspace\Core\Inflector\InflectorFactoryInterface $inflectorFactory
	 * @return static
	 */
	public function __construct(
		Connection $connection,
		InflectorFactoryInterface $inflectorFactory
	) {
		$this->setConnection($connection);
		$this->setInflectorFactory($inflectorFactory);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConnection(): Connection
	{
		return $this->connection;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setConnection(Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getInflectorFactory(): InflectorFactoryInterface
	{
		return $this->inflectorFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setInflectorFactory(InflectorFactoryInterface $inflectorFactory)
	{
		$this->inflectorFactory = $inflectorFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function persist(ModelInterface $model)
	{
		$normalized   = $this->transformEntity($model);
		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->insert($model->getTable());

		// loop 'normalized' keys as column names.
		foreach (array_keys($normalized) as $key) {
			$queryBuilder = $queryBuilder->setValue($key, '?');
		}

		// loop 'normalized' values as column values.
		foreach (array_values($normalized) as $key => $value) {
			$queryBuilder = $queryBuilder->setParameter($key, $value);
		}

		$queryBuilder->execute();
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(ModelInterface $model)
	{
		$normalized   = $this->transformEntity($model);
		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->update($model->getTable());

		foreach (array_keys($normalized) as $key) {
			$queryBuilder = $queryBuilder->set($key, '?');		
		}

		foreach (array_values($normalized) as $key => $value) {
			$queryBuilder = $queryBuilder->setParameter($key, $value);
		}

		$primaryKey     = $model->getPrimaryKey();
		$normalized     = ucfirst($primaryKey);
		$queryBuilder   = $queryBuilder
			->where(sprintf('%s = ?', $primaryKey))
			->setParameter(
				$key + 1,
				call_user_func([$model, sprintf('get%s', $normalized)])
			);

		$queryBuilder->execute();
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove(ModelInterface $model)
	{
		$primaryKey = $model->getPrimaryKey();
		$normalized = ucfirst($primaryKey);
		$pval       = call_user_func([
			$model,
			sprintf('get%s', $normalized)
		]);

		if ($pval === null) {
			throw new InvalidArgumentException(
				'Primary key of given model class object has null value.'
			);
		}

		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->delete($model->getTable())
			->where(sprintf('%s = ?', $primaryKey))
			->setParameter(0, $pval);

		$queryBuilder->execute();
	}

	/**
	 * Extract key and value for entity record creation.
	 */
	private function transformEntity(ModelInterface $model)
	{
		$inflector = $this->getInflectorFactory()
			->createSimpleInflector();

		foreach ($this->getModelClassProperties($model) as $name) {
			$normalized          = $inflector->snakeize($name);
			$result[$normalized] = $this->modelPropertyAccessor(
				$model,
				$name,
				EntityInterface::MODEL_PROPERTY_ACCESS_READ
			);
		}

		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getModelClassProperties(ModelInterface $model): array
	{
		$properties = [];
		$refl       = new ReflectionClass($model);

		foreach ($refl->getProperties(ReflectionProperty::IS_PRIVATE) as $obj) {
			$result[] = $obj->getName();
		}

		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function modelPropertyAccessor(
		ModelInterface $model,
		string $name,
		int $op,
		$data = null
	) {
		$refl         = new ReflectionClass($model);
		$ret          = null;
		$gotException = true;

		foreach ($refl->getProperties(ReflectionProperty::IS_PRIVATE) as $obj) {
			if ($obj->getName() === $name) {
				$gotException = false;
				break;
			}
		}

		if ($gotException) {
			throw InvalidArgumentException(
				sprintf(
					"Class property with name '%s' doesn't exist.",
					$name
				)
			);
		}

		$obj->setAccessible(true);

		switch ($op) {
			case EntityInterface::MODEL_PROPERTY_ACCESS_READ:
				$ret = $obj->getValue($model);
				break;
			case EntityInterface::MODEL_PROPERTY_ACCESS_WRITE:
				$obj->setValue($model, $data);
				break;
		}

		return $ret;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRepository(string $model): RepositoryInterface
	{
		if (!is_subclass_of($model, ModelInterface::class)) {
			throw new InvalidArgumentException(
				sprintf(
					"Class '%s' must be an instance of '%s'.",
					$model,
					ModelInterface::class
				)
			);
		}

		$model      = new $model();
		$repository = sprintf('\\%s', $model->getRepository());

		return new $repository($this, $model);
	}
}
