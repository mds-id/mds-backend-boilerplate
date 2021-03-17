<?php

declare(strict_types=1);

namespace Bluepeer\Core\Dbal;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use Bluepeer\Core\Model\ModelInterface;
use Bluepeer\Core\Repository\RepositoryInterface;
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
	 * @return static
	 */
	public function __construct(Connection $connection)
	{
		$this->setConnection($connection);
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
	 * Extract key and value for entity record creation.
	 */
	private function transformEntity(ModelInterface $model)
	{
		foreach ($this->getModelClassProperties($model) as $name) {
			$result[$name] = $this->modelPropertyAccessor(
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

		switch ($op) {
			case EntityInterface::MODEL_PROPERTY_ACCESS_READ:
				$ret = $obj->getValue();
				break;
			case EntityInterface::MODEL_PROPERTY_ACCESS_WRITE:
				$obj->setAccessible(true);
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
