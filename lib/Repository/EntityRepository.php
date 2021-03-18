<?php

declare(strict_types=1);

namespace Bluepeer\Core\Repository;

use Bluepeer\Core\Dbal\EntityInterface;
use Bluepeer\Core\Model\ModelInterface;
use Doctrine\DBAL\Query\QueryBuilder;

use function sprintf;
use function ucfirst;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
abstract class EntityRepository implements RepositoryInterface
{
	/**
	 * @var \Bluepeer\Core\Dbal\EntityInterface
	 */
	private $entity;

	/**
	 * @var \Bluepeer\Core\Model\ModelInterface
	 */
	private $model;

	/**
	 * @var \Bluepeer\Core\Dbal\EntityInterface $entity
	 * @var \Bluepeer\Core\Model\ModelInterface $model
	 * @return static
	 */
	public function __construct(EntityInterface $entity, ModelInterface $model)
	{
		$this->setEntity($entity);
		$this->setModel($model);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity(): EntityInterface
	{
		return $this->entity;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setEntity(EntityInterface $entity)
	{
		$this->entity = $entity;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getModel(): ModelInterface
	{
		return $this->model;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setModel(ModelInterface $model)
	{
		$this->model = $model;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getQueryBuilder(): QueryBuilder
	{
		return $this->getEntity()
			->getConnection()
			->createQueryBuilder();
	}

	/**
	 * {@inheritdoc}
	 */
	public function findAll(): array
	{
		$statement  = $this->getQueryBuilder()
			->select('*')
			->from($this->getModel()->getTable())
			->execute();
		$properties = $this->getEntity()
			->getModelClassProperties($this->getModel());
		$inflector  = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$aggregated = [];

		while (($row = $statement->fetchAssociative()) !== false) {
			$robj = clone $this->getModel();

			foreach ($properties as $name) {
				$this->getEntity()
					->modelPropertyAccessor(
						$robj,
						$name,
						EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
						$row[$inflector->snakeize($name)]
					);
			}

			$aggregated[] = $robj;
		}

		return $aggregated;
	}

	/**
	 * {@inheritdoc}
	 */
	public function find($id)
	{
		$statement = $this->getQueryBuilder()
			->select('*')
			->from($this->getModel()->getTable())
			->where(sprintf('%s = ?', $this->getModel()->getPrimaryKey()))
			->setParameter(0, $id)
			->execute();
		$inflector = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$row       = $statement->fetchAssociative();

		if ($row == null) {
			return null;
		}

		$properties = $this->getEntity()
			->getModelClassProperties($this->getModel());
		$robj       = clone $this->getModel();

		foreach ($properties as $name) {
			$this->getEntity()
				->modelPropertyAccessor(
					$robj,
					$name,
					EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
					$row[$inflector->snakeize($name)]
				);
		}

		return $robj;
	}
}