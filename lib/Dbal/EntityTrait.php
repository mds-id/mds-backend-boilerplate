<?php

declare(strict_types=1);

namespace Modspace\Core\Dbal;

use RuntimeException;
use Throwable;
use Modspace\Core\Dbal\EntityInterface;
use Modspace\Core\Exception\Model\Relation\RelationIntegrityException;
use Modspace\Core\Exception\Model\Relation\RelationRetrievalException;
use Modspace\Core\Model\ModelInterface;
use Modspace\Core\Model\Relation\RelationType;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait EntityTrait
{
	/**
	 * Check if current entity model object have
	 * relation or otherwise.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return bool
	 */
	private function hasRelation(ModelInterface $model)
	{
		switch ($model->getRelationType()) {
			case RelationType::ONE_TO_ONE:
			case RelationType::ONE_TO_MANY:
			case RelationType::MANY_TO_ONE:
			case RelationType::MANY_TO_MANY:
				return true;
		}

		return false;
	}

	private function handleRelationalPersist(ModelInterface $model)
	{
		if (!$this->hasRelation($model)) {
			return;
		}

		switch ($model->getRelationType()) {
			case RelationType::ONE_TO_ONE:
				$this->handleOneToOneRelationalPersist($model);
				break;
			case RelationType::ONE_TO_MANY:
				$this->handleOneToManyRelationalPersist($model);
				break;
			case RelationType::MANY_TO_ONE:
				$this->handleManyToOneRelationalPersist($model);
				break;
			case RelationType::MANY_TO_MANY:
				$this->handleManyToManyRelationalPersist($model);
				break;
		}
	}

	/**
	 * Handle record persistence on entity model object that
	 * have one-to-one relational mapping.
	 *
	 * @param ModelInterface $model
	 * @return void
	 */
	private function handleOneToOneRelationalPersist(ModelInterface $model)
	{
		if ($model->getForeignKey() !== '') {
			$this->handleInversedOneToOneRelationalPersist($model);
			return;
		}

		$normalized = $this->transformEntity($model);
		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->insert($model->getTable());

		foreach (array_keys($normalized) as $key) {
			$queryBuilder = $queryBuilder->setValue($key, '?');
		}

		foreach (array_values($normalized) as $key => $value) {
			$queryBuilder = $queryBuilder->setParameter($key, $value);
		}

		try {
			$queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$this->modelPropertyAccessor(
			$model,
			$model->getPrimaryKey(),
			EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
			$this->getConnection()->lastInsertId()
		);
	}

	/**
	 * Handle record persistence on entity model object that
	 * have one-to-one inversed relational mapping.
	 *
	 * @param ModelInterface $model
	 * @return void
	 */
	private function handleInversedOneToOneRelationalPersist(ModelInterface $model)
	{
		try {
			$this->checkInvertedOneToOneRelationalConsistency($model);
		} catch (Throwable $e) {
			throw $e;
		}

		$inflector = $this->getInflectorFactory()
			->createSimpleInflector();
		$relationTargetObj = call_user_func([
			$model,
			sprintf('get%s', ucfirst($model->getRelationBindObject()))
		]);
		$foreignKey = $inflector->snakeize($model->getForeignKey());
		$foreignKeyValue = call_user_func([
			$relationTargetObj,
			sprintf('get%s', ucfirst($relationTargetObj->getPrimaryKey()))
		]);

		$normalized = $this->transformEntity($model);
		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->insert($model->getTable());

		$normalized[$foreignKey] = $foreignKeyValue;

		foreach (array_keys($normalized) as $key) {
			$queryBuilder = $queryBuilder->setValue($key, '?');
		}

		foreach (array_values($normalized) as $key => $value) {
			$queryBuilder = $queryBuilder->setParameter($key, $value);
		}

		try {
			$queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$this->modelPropertyAccessor(
			$model,
			$model->getPrimaryKey(),
			EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
			$this->getConnection()->lastInsertId()
		);
	}

	/**
	 * Handle record persistence on entity mode object that
	 * have one-to-many relational mapping.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return void
	 */
	private function handleOneToManyRelationalPersist(ModelInterface $model)
	{
		$normalized   = $this->transformEntity($model);
		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->insert($model->getTable());

		foreach (array_keys($normalized) as $key) {
			$queryBuilder = $queryBuilder->setValue($key, '?');
		}

		foreach (array_values($normalized) as $key => $value) {
			$queryBuilder = $queryBuilder->setParameter($key, $value);
		}

		try {
			$queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$this->modelPropertyAccessor(
			$model,
			$model->getPrimaryKey(),
			EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
			$this->getConnection()->lastInsertId()
		);
	}

	/**
	 * Handle record persistence on entity model object that
	 * have many-to-one relational mapping.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return void
	 * @throws \Throwable If query execution failed.
	 */
	private function handleManyToOneRelationalPersist(ModelInterface $model)
	{
		$normalized  = $this->transformEntity($model);
		$inflector   = $this->getInflectorFactory()
			->createSimpleInflector();
		$foreignKey  = $inflector->snakeize($model->getForeignKey());
		$inversedObj = call_user_func([
			$model,
			sprintf('get%s', ucfirst($model->getRelationBindObject()))
		]);

		$normalized[$foreignKey] = call_user_func([
			$inversedObj,
			sprintf('get%s', $inversedObj->getPrimaryKey())
		]);

		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->insert($model->getTable());

		foreach (array_keys($normalized) as $key) {
			$queryBuilder = $queryBuilder->setValue($key, '?');
		}

		foreach (array_values($normalized) as $key => $value) {
			$queryBuilder = $queryBuilder->setParameter($key, $value);
		}

		try {
			$queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$this->modelPropertyAccessor(
			$model,
			$model->getPrimaryKey(),
			EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
			$this->getConnection()->lastInsertId()
		);
	}

	private function handleManyToManyRelationalPersist(ModelInterface $model)
	{
	}

	private function handleRelationalSave(ModelInterface $model)
	{
		if (!$this->hasRelation($model)) {
			return;
		}

		switch ($model->getRelationType()) {
			case RelationType::ONE_TO_ONE:
				$this->handleOneToOneRelationalSave($model);
				break;
			case RelationType::ONE_TO_MANY:
				$this->handleOneToManyRelationalSave($model);
				break;
			case RelationType::MANY_TO_ONE:
				$this->handleManyToOneRelationalSave($model);
				break;
			case RelationType::MANY_TO_MANY:
				$this->handleManyToManyRelationalSave($model);
				break;
		}
	}

	/**
	 * Handle record update on entity model object that
	 * have one-to-one relational mapping.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return void
	 * @throws \Throwable If query execution failed.
	 */
	private function handleOneToOneRelationalSave(ModelInterface $model)
	{
		if ($model->getForeignKey() !== '') {
			$this->handleInvertedOneToOneRelationalSave($model);
			return;
		}

		$normalized = $this->transformEntity($model);
		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->update($model->getTable());

		foreach (array_keys($normalized) as $key) {
			$queryBuilder = $queryBuilder->set($key, '?');
		}

		foreach (array_values($normalized) as $key => $value) {
			$queryBuilder = $queryBuilder->setParameter($key, $value);
		}

		$inflector = $this->getInflectorFactory()
			->createSimpleInflector();
		$primaryKey = $inflector->snakeize($model->getPrimaryKey());
		$queryBuilder = $queryBuilder
			->where(sprintf('%s = ?', $primaryKey))
			->setParameter(
				$key + 1,
				call_user_func([
					$model,
					sprintf('get%s', ucfirst($model->getPrimaryKey()))
				])
			);

		try {
			$queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}
	}

	private function handleInvertedOneToOneRelationalSave(ModelInterface $model)
	{
		try {
			$this->checkInvertedOneToOneRelationalConsistency($model);
			return;
		} catch (Throwable $e) {
			throw $e;
		}

		$relationTargetObj = call_user_func([
			$model,
			sprintf('get%s', ucfirst($model->getRelationBindObject()))
		]);

		if (null === $relationTargetObj) {
			throw new RelationRetrievalException(
				'Relation object must not be null.'
			);
		}

		$normalized = $this->transformEntity($model);
		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->update($model->getTable());

		foreach (array_keys($normalized) as $key) {
			$queryBuilder = $queryBuilder->set($key, '?');
		}

		foreach (array_values($normalized) as $key => $value) {
			$queryBuilder = $queryBuilder->setParameter($key, $value);
		}

		$inflector = $this->getInflectorFactory()
			->createSimpleInflector();
		$primaryKey = $model->getPrimaryKey();
		$foreignKey = $model->getForeignKey();
		$primaryKeyValue = call_user_func([
			$model,
			sprintf('get%s', ucfirst($primaryKey))
		]);
		$foreignKeyValue = call_user_func([
			$relationTargetObj,
			sprintf('get%s', ucfirst($relationTargetObj->getPrimaryKey()))
		]);
		$queryBuilder = $queryBuilder
			->where(sprintf('%s = ?', $inflector->snakeize($primaryKey)))
			->andWhere(sprintf('%s = ?', $inflector->snakeize($foreignKey)))
			->setParameter($key + 1, $primaryKeyValue)
			->setParameter($key + 2, $foreignKeyValue);

		try {
			$queryBuilder->execute();
		} catch (Exception $e) {
			throw $e;
		}
	}

	private function checkInvertedOneToOneRelationalConsistency(ModelInterface $model)
	{
		$relationTargetObj = call_user_func([
			$model,
			sprintf('get%s', ucfirst($model->getRelationBindObject()))
		]);
		$foreignKey = $model->getForeignKey();
		$foreignKeyValue = call_user_func([
			$relationTargetObj,
			sprintf('get%s', ucfirst($relationTargetObj->getPrimaryKey()))
		]);
		$inflector = $this->getInflectorFactory()
			->createSimpleInflector();
		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->select('count(*)')
			->from($model->getTable())
			->where(sprintf('%s = ?', $inflector->snakeize($foreignKey)))
			->setParameter(0, $foreignKeyValue);

		try {
			$statement = $queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$results = array_values($statement->fetchAssociative());
		$fieldCount = intval($results[0]);

		if ($fieldCount === 1) {
			throw new RelationIntegrityException(
				sprintf(
					'Primary key of entity class \'%s\' has been occupied.',
					get_class($relationTargetObj)
				)
			);
		}
	}

	/**
	 * Handle record update on entity model object that
	 * have one-to-many relational mapping.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return void
	 * @throws \Throwable If query execution failed.
	 */
	private function handleOneToManyRelationalSave(ModelInterface $model)
	{
		$normalized   = $this->transformEntity($model);
		$inflector    = $this->getInflectorFactory()
			->createSimpleInflector();
		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->update($model->getTable());

		foreach (array_keys($normalized) as $key) {
			$queryBuilder = $queryBuilder->set($key, '?');
		}

		foreach (array_values($normalized) as $key => $value) {
			$queryBuilder = $queryBuilder->setParameter($key, $value);
		}

		$primaryKey   = $model->getPrimaryKey();
		$normalized   = $inflector->snakeize($primaryKey);
		$queryBuilder = $queryBuilder
			->where(sprintf('%s = ?', $normalized))
			->setParameter(
				$key + 1,
				call_user_func([$model, sprintf('get%s', ucfirst($primaryKey))])
			);

		try {
			$queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}
	}

	/**
	 * Handle record update on entity model object that
	 * have many-to-one relational mapping.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return void
	 * @throws \Throwable If query execution failed.
	 */
	private function handleManyToOneRelationalSave(ModelInterface $model)
	{
		$normalized  = $this->transformEntity($model);
		$inflector   = $this->getInflectorFactory()
			->createSimpleInflector();
		$foreignKey  = $inflector->snakeize($model->getForeignKey());
		$inversedObj = call_user_func([
			$model,
			sprintf('get%s', $inflector->camelize($model->getRelationBindObject()))
		]);

		$normalized[$foreignKey] = call_user_func([
			$inversedObj,
			sprintf('get%s', $inversedObj->getPrimaryKey())
		]);

		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->update($model->getTable());

		foreach (array_keys($normalized) as $key) {
			$queryBuilder = $queryBuilder->set($key, '?');
		}

		foreach (array_values($normalized) as $key => $value) {
			$queryBuilder = $queryBuilder->setParameter($key, $value);
		}

		$primaryKey = $model->getPrimaryKey();
		$normalized = $inflector->snakeize($primaryKey);

		$queryBuilder = $queryBuilder
			->where(sprintf('%s = ?', $normalized))
			->setParameter(
				$key + 1,
				call_user_func([$model, sprintf('get%s', ucfirst($primaryKey))])
			);

		try {
			$queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}
	}

	private function handleManyToManyRelationalSave(ModelInterface $model)
	{
	}

	private function handleRelationalRemove(ModelInterface $model)
	{
		if (!$this->hasRelation($model)) {
			return;
		}

		switch ($model->getRelationType()) {
			case RelationType::ONE_TO_ONE:
				$this->handleOneToOneRelationalRemove($model);
				break;
			case RelationType::ONE_TO_MANY:
				$this->handleOneToManyRelationalRemove($model);
				break;
			case RelationType::MANY_TO_ONE:
				$this->handleManyToOneRelationalRemove($model);
				break;
			case RelationType::MANY_TO_MANY:
				$this->handleManyToManyRelationalRemove($model);
				break;
		}
	}

	private function handleOneToOneRelationalRemove(ModelInterface $model)
	{
	}

	/**
	 * Handle record removal on entity model object that
	 * have one-to-many relational mapping.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return void
	 * @throws \Throwable If query execution failed.
	 */
	private function handleOneToManyRelationalRemove(ModelInterface $model)
	{
		if ($model->isOrphanRemoval()) {
			$this->handleOrphanRemoval($model);
		}

		$inflector = $this->getInflectorFactory()
			->createSimpleInflector();
		$primaryKey = $inflector->snakeize($model->getPrimaryKey());
		$primaryKeyValue = call_user_func([
			$model,
			sprintf('get%s', ucfirst($model->getPrimaryKey()))
		]);

		if (null === $primaryKeyValue) {
			throw new RuntimeException(
				'Primary key value must not be null.'
			);
		}

		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->delete($model->getTable())
			->where(sprintf('%s = ?', $primaryKey))
			->setParameter(0, $primaryKeyValue);

		try {
			$queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}
	}

	/**
	 * Handle record removal on entity model object that
	 * have many-to-one relational mapping.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return void
	 * @throws \Throwable If query execution failed.
	 */
	private function handleManyToOneRelationalRemove(ModelInterface $model)
	{
		$inflector  = $this->getInflectorFactory()
			->createSimpleInflector();
		$primaryKey = $inflector->snakeize($model->getPrimaryKey());
		$primaryKeyValue = call_user_func([
			$model,
			sprintf('get%s', ucfirst($model->getPrimaryKey()))
		]);

		if (null === $primaryKeyValue) {
			throw new RuntimeException(
				'Primary key value must not be null.'
			);
		}

		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->delete($model->getTable())
			->where(sprintf('%s = ?', $primaryKey))
			->setParameter(0, $primaryKeyValue);

		try {
			$queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}
	}

	private function handleManyToManyRelationalRemove(ModelInterface $model)
	{
	}

	/**
	 * Remove all orphan-related entity class objects from removed
	 * parent entity class object.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return void
	 */
	private function handleOrphanRemoval(ModelInterface $model)
	{
		$inflector = $this->getInflectorFactory()
			->createSimpleInflector();
		$childs    = call_user_func([
			$model,
			sprintf('get%s', ucfirst($model->getRelationBindObject()))
		]);

		foreach ($childs as $child) {
			$primaryKey   = $inflector->snakeize($child->getPrimaryKey());
			$foreignKey   = $inflector->snakeize($child->getForeignKey());
			$queryBuilder = $this->getConnection()
				->createQueryBuilder()
				->delete($child->getTable())
				->where(sprintf('%s = ?', $primaryKey))
				->andWhere(sprintf('%s = ?', $foreignKey))
				->setParameter(0, call_user_func([
					$child,
					sprintf('get%s', ucfirst($child->getPrimaryKey()))
				]))
				->setParameter(1, call_user_func([
					$model,
					sprintf('get%s', ucfirst($model->getPrimaryKey()))
				]));

			try {
				$queryBuilder->execute();
			} catch (Throwable $e) {
				break;
			}
		}
	}
}
