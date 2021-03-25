<?php

declare(strict_types=1);

namespace Modspace\Core\Dbal;

use Throwable;
use Modspace\Core\Dbal\EntityInterface;
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

	private function handleOneToOneRelationalPersist(ModelInterface $model)
	{
	}

	private function handleOneToManyRelationalPersist(ModelInterface $model)
	{
	}

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

	private function handleOneToOneRelationalSave(ModelInterface $model)
	{
	}

	private function handleOneToManyRelationalSave(ModelInterface $model)
	{
	}

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

	private function handleOneToManyRelationalRemove(ModelInterface $model)
	{
	}

	private function handleManyToOneRelationalRemove(ModelInterface $model)
	{
	}

	private function handleManyToManyRelationalRemove(ModelInterface $model)
	{
	}
}
