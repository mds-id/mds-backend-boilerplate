<?php

declare(strict_types=1);

namespace Modspace\Core\Repository;

use RuntimeException;
use Doctrine\DBAL\Driver\ResultStatement;
use Modspace\Core\Common\Collections\ArrayCollection;
use Modspace\Core\Dbal\FetchQuantization;
use Modspace\Core\Dbal\EntityInterface;
use Modspace\Core\Model\ModelInterface;
use Modspace\Core\Model\Relation\RelationType;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait EntityRepositoryTrait
{
	/**
	 * Check if current entity class object has
	 * relation or otherwise.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return bool
	 */
	private function hasRelation(ModelInterface $model): bool
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

	/**
	 * Validate relation type.
	 *
	 * @param int $relationType
	 * @return bool
	 */
	private function validateRelationType(int $relationType)
	{
		switch ($relationType) {
			case RelationType::ONE_TO_ONE:
			case RelationType::ONE_TO_MANY:
			case RelationType::MANY_TO_ONE:
			case RelationType::MANY_TO_MANY:
				return true;
		}

		return false;
	}

	/**
	 * Handle model relation.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @param mixed|null $id
	 * @return ModelInterface|array|null
	 */
	private function handleRelation(ModelInterface $model, $id = null)
	{
		if (!$this->hasRelation($model)) {
			return null === $id ? [] : null;
		}

		switch ($model->getRelationType()) {
			case RelationType::ONE_TO_ONE:
				return $this->handleOneToOneRelation($model, $id);
			case RelationType::ONE_TO_MANY:
				return $this->handleOneToManyRelation($model, $id);
			case RelationType::MANY_TO_ONE:
				return $this->handleManyToOneRelation($model, $id);
			case RelationType::MANY_TO_MANY:
				return $this->handleManyToManyRelation($model, $id);
		}
	}

	/**
	 * Handle one-to-one model relation.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @param mixed|null $id
	 * @return ModelInterface|array|null
	 */
	private function handleOneToOneRelation(ModelInterface $model, $id = null)
	{
		$quantizationMode = null === $id
			? FetchQuantization::QUANTIZATION_MULTIPLE
			: FetchQuantization::QUANTIZATION_SINGLE;
		$inflector = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$queryBuilder = $this->getQueryBuilder()
			->select('*')
			->from($model->getTable());

		if ($id !== null) {
			$queryBuilder = $queryBuilder
				->where(sprintf('%s = ?', $inflector->snakeize($model->getPrimaryKey())))
				->setParameter(0, $id);
		}

		try {
			$statement = $queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$parent = $quantizationMode === FetchQuantization::QUANTIZATION_SINGLE
			? $this->fetchQuantizationSingle($statement, $model)
			: $this->fetchQuantizationMultiple($statement, $model);

		if ($parent === null) {
			return null;
		}

		if ($parent instanceof ModelInterface) {
			return $this->relationResolver($parent, RelationType::ONE_TO_ONE);
		}

		array_walk($parent, function(&$val) {
			$val = $this->relationResolver($val, RelationType::ONE_TO_ONE);
		});

		return $parent;
	}

	/**
	 * Handle one-to-many model relation.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @param mixed|null $id
	 * @return ModelInterface|array|null
	 */
	private function handleOneToManyRelation(ModelInterface $model, $id = null)
	{
		$quantizationMode = null === $id
			? FetchQuantization::QUANTIZATION_MULTIPLE
			: FetchQuantization::QUANTIZATION_SINGLE;
		$inflector = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$queryBuilder = $this->getQueryBuilder()
			->select('*')
			->from($model->getTable());

		if ($id !== null) {
			$queryBuilder = $queryBuilder
				->where(sprintf('%s = ?', $inflector->snakeize($model->getPrimaryKey())))
				->setParameter(0, $id);
		}

		try {
			$statement = $queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$parent = $quantizationMode === FetchQuantization::QUANTIZATION_SINGLE
			? $this->fetchQuantizationSingle($statement, $model)
			: $this->fetchQuantizationMultiple($statement, $model);

		if ($parent === null) {
			return null;
		}

		if ($parent instanceof ModelInterface) {
			return $this->relationResolver($parent, RelationType::ONE_TO_MANY);
		}

		array_walk($parent, function(&$val) {
			$val = $this->relationResolver($val, RelationType::ONE_TO_MANY);
		});

		return $parent;
	}

	/**
	 * Handle many-to-one model relation.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @param mixed|null $id
	 * @return \Modspace\Core\Model\ModelInterface|array|null
	 */
	private function handleManyToOneRelation(ModelInterface $model, $id = null)
	{
		$quantizationMode = null === $id
			? FetchQuantization::QUANTIZATION_MULTIPLE
			: FetchQuantization::QUANTIZATION_SINGLE;
		$inflector = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$queryBuilder = $this->getQueryBuilder()
			->select('*')
			->from($model->getTable());

		if ($id !== null) {
			$queryBuilder = $queryBuilder
				->where(sprintf('%s = ?', $inflector->snakeize($model->getPrimaryKey())))
				->setParameter(0, $id);
		}

		try {
			$statement = $queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$parent = $quantizationMode === FetchQuantization::QUANTIZATION_SINGLE
			? $this->fetchQuantizationSingle($statement, $model)
			: $this->fetchQuantizationMultiple($statement, $model);

		if (null === $parent) {
			return null;
		}

		if ($parent instanceof ModelInterface) {
			return $this->relationResolver($parent, RelationType::MANY_TO_ONE);
		}

		array_walk($parent, function(&$val) {
			$val = $this->relationResolver($val, RelationType::MANY_TO_ONE);
		});

		return $parent;
	}

	/**
	 * Handle many-to-many model relation.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @param mixed|null $id
	 * @return \Modspace\Core\Model\ModelInterface|array|null
	 */
	private function handleManyToManyRelation(ModelInterface $model, $id = null)
	{
		$quantizationMode = null === $id
			? FetchQuantization::QUANTIZATION_MULTIPLE
			: FetchQuantization::QUANTIZATION_SINGLE;
		$inflector = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$queryBuilder = $this->getQueryBuilder()
			->select('*')
			->from($model->getTable());

		if ($id !== null) {
			$queryBuilder = $queryBuilder
				->where(sprintf('%s = ?', $inflector->snakeize($model->getPrimaryKey())))
				->setParameter(0, $id);
		}

		try {
			$statement = $queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$parent = $quantizationMode === FetchQuantization::QUANTIZATION_SINGLE
			? $this->fetchQuantizationSingle($statement, $model)
			: $this->fetchQuantizationMultiple($statement, $model);

		if ($parent instanceof ModelInterface) {
			return $this->relationResolver($parent, RelationType::MANY_TO_MANY);
		}

		array_walk($parent, function(&$val) {
			$val = $this->relationResolver($val, RelationType::MANY_TO_MANY);
		});

		return $parent;
	}

	/**
	 * Fetch single record from given prepared statement object.
	 *
	 * @param \Doctrine\DBAL\Driver\ResultStatement $statement
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return \Modspace\Core\Model\ModelInterface|null
	 */
	private function fetchQuantizationSingle(
		ResultStatement $statement,
		ModelInterface $model
	) {
		$row = $statement->fetchAssociative();

		if ($row === false) {
			return null;
		}

		$inflector  = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$properties = $this->getEntity()
			->getModelClassProperties($model);
		$resultObj  = clone $model;

		if ($model->getForeignKey() !== '') {
			$snakeized = $inflector->snakeize($model->getForeignKey());
			array_push($this->savedForeignKeyValue, $row[$snakeized]);
		}

		foreach ($properties as $name) {
			$snakeized = $inflector->snakeize($name);

			if (!isset($row[$snakeized])) {
				continue;
			}

			if ($model->getForeignKey() !== '' &&
				$snakeized === $inflector->snakeize($model->getForeignKey())) {
				$this->setSavedForeignKeyValue($row[$snakeized]);
				continue;
			}

			$this->getEntity()
				->modelPropertyAccessor(
					$resultObj,
					$name,
					EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
					$row[$snakeized]
				);
		}

		return $resultObj;
	}

	/**
	 * Fetch multiple record from given prepared statement object.
	 *
	 * @param \Doctrine\DBAL\Driver\Statement $statement
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return array
	 */
	private function fetchQuantizationMultiple(
		ResultStatement $statement,
		ModelInterface $model
	): array {
		$aggregated = [];
		$inflector  = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$properties = $this->getEntity()
			->getModelClassProperties($model);
		$resultObj  = clone $model;

		while (($row = $statement->fetchAssociative()) !== false) {
			$resultObj = clone $resultObj;

			if ($model->getForeignKey() !== '') {
				$normalized = $inflector->snakeize($model->getForeignKey());
				array_push($this->savedForeignKeyValue, $row[$normalized]);
			}

			foreach ($properties as $name) {
				$snakeized = $inflector->snakeize($name);

				if (!isset($row[$snakeized])) {
					continue;
				}

				$this->getEntity()
					->modelPropertyAccessor(
						$resultObj,
						$name,
						EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
						$row[$snakeized]
					);
			}

			$aggregated[] = $resultObj;
		}

		return $aggregated;
	}

	/**
	 * Resolve entity model object relation.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @param int $relationType
	 * @return \Modspace\Core\Model\ModelInterface|null
	 */
	private function relationResolver(
		ModelInterface $model,
		int $relationType
	) {
		if (!$this->hasRelation($model)) {
			throw new InvalidArgumentException(
				"Given entity class model have no relation."
			);
		}

		if (!$this->validateRelationType($relationType)) {
			throw new InvalidArgumentException(
				"Invalid relation type."
			);
		}

		if ($model->getRelationType() !== $relationType) {
			throw new RuntimeException(
				"Given relation type and entity model relation type " .
				"does not match."
			);
		}

		switch ($relationType) {
			case RelationType::ONE_TO_ONE:
				return $this->oneToOneRelationResolver($model);
			case RelationType::ONE_TO_MANY:
				return $this->oneToManyRelationResolver($model);
			case RelationType::MANY_TO_ONE:
				return $this->manyToOneRelationResolver($model);
			case RelationType::MANY_TO_MANY:
				return $this->manyToManyRelationResolver($model);
		}
	}

	/**
	 * Resolve entity model object with one-to-one
	 * relationship.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return \Modspace\Core\Model\ModelInterface|null
	 */
	private function oneToOneRelationResolver(ModelInterface $model)
	{
		if ($model->getForeignKey() !== '') {
			return $this->oneToOneInversedRelationResolver($model);
		}

		$relationTarget    = $model->getRelationTargetClass();
		$relationTargetObj = new $relationTarget();
		$inflector         = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$queryBuilder      = $this->getQueryBuilder()
			->select(sprintf('%s.*', $relationTargetObj->getTable()[0]))
			->from($relationTargetObj->getTable(), $relationTargetObj->getTable()[0])
			->join(
				$relationTargetObj->getTable()[0],
				$model->getTable(),
				$model->getTable()[0],
				sprintf(
					'%s.%s = %s.%s',
					$relationTargetObj->getTable()[0],
					$inflector->snakeize($relationTargetObj->getForeignKey()),
					$model->getTable()[0],
					$inflector->snakeize($model->getPrimaryKey())
				)
			)
			->where(sprintf(
				'%s.%s = ?',
				$relationTargetObj->getTable()[0],
				$inflector->snakeize($relationTargetObj->getForeignKey())
			))
			->setParameter(
				0,
				call_user_func([
					$model,
					sprintf("get%s", $inflector->camelize($model->getPrimaryKey()))
				])
			);

		try {
			$statement = $queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$relationTargetObj = $this->fetchQuantizationSingle(
			$statement,
			$relationTargetObj
		);

		if (null === $relationTargetObj) {
			return $model;
		}

		$this->getEntity()
			->modelPropertyAccessor(
				$model,
				$model->getRelationBindObject(),
				EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
				$relationTargetObj
			);

		return $model;
	}

	private function oneToOneInversedRelationResolver(ModelInterface $model)
	{
		$relationTarget = $model->getRelationTargetClass();
		$relationTargetObj = new $relationTarget();
		$inflector = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();

		$queryBuilder = $this->getQueryBuilder()
			->select(sprintf('%s.*', $relationTargetObj->getTable()[0]))
			->from($relationTargetObj->getTable(), $relationTargetObj->getTable()[0])
			->join(
				$relationTargetObj->getTable()[0],
				$model->getTable(),
				$model->getTable()[0],
				sprintf(
					'%s.%s = %s.%s',
					$relationTargetObj->getTable()[0],
					$inflector->snakeize($relationTargetObj->getPrimaryKey()),
					$model->getTable()[0],
					$inflector->snakeize($model->getForeignKey())
				)
			)
			->where(sprintf(
				'%s.%s = ?',
				$model->getTable()[0],
				$inflector->snakeize($model->getPrimaryKey())
			))
			->setParameter(
				0,
				call_user_func([
					$model,
					sprintf('get%s', ucfirst($model->getPrimaryKey()))
				])
			);

		try {
			$statement = $queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$relationTargetObj = $this->fetchQuantizationSingle(
			$statement,
			$relationTargetObj
		);

		if (null === $relationTargetObj) {
			return $model;
		}

		$this->getEntity()
			->modelPropertyAccessor(
				$model,
				$model->getRelationBindObject(),
				EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
				$relationTargetObj
			);

		return $model;
	}

	/**
	 * Resolve entity model object with one-to-many
	 * relationship.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return \Modspace\Core\Model\ModelInterface
	 */
	private function oneToManyRelationResolver(ModelInterface $model)
	{
		$relationTarget    = $model->getRelationTargetClass();
		$relationTargetObj = new $relationTarget();
		$inflector         = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$queryBuilder      = $this->getQueryBuilder()
			->select(sprintf('%s.*', $relationTargetObj->getTable()[0]))
			->from($relationTargetObj->getTable(), $relationTargetObj->getTable()[0])
			->join(
				$relationTargetObj->getTable()[0],
				$model->getTable(),
				$model->getTable()[0],
				sprintf(
					'%s.%s = %s.%s',
					$relationTargetObj->getTable()[0],
					$inflector->snakeize($relationTargetObj->getForeignKey()),
					$model->getTable()[0],
					$model->getPrimaryKey()
				)
			)
			->where(sprintf(
				'%s.%s = ?',
				$model->getTable()[0],
				$model->getPrimaryKey()
			))
			->setParameter(
				0,
				call_user_func([
					$model,
					sprintf('get%s', $inflector->camelize($model->getPrimaryKey()))
				])
			);

		try {
			$statement = $queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$relationTargetObjs = $this->fetchQuantizationMultiple(
			$statement,
			$relationTargetObj
		);

		$normalizedResult = new ArrayCollection();

		foreach ($relationTargetObjs as $elt) {
			$normalizedResult->append($elt);
		}

		$this->getEntity()
			->modelPropertyAccessor(
				$model,
				$model->getRelationBindObject(),
				EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
				$normalizedResult
			);

		return $model;
	}

	/**
	 * Resolve entity model object with many-to-one
	 * relationship.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return \Modspace\Core\Model\ModelInterface
	 */
	private function manyToOneRelationResolver(ModelInterface $model)
	{
		$relationTarget = $model->getRelationTargetClass();
		$relationTargetObj = new $relationTarget();
		$inflector = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$queryBuilder = $this->getQueryBuilder()
			->select(sprintf('%s.*', $relationTargetObj->getTable()[0]))
			->from($model->getTable(), $model->getTable()[0])
			->join(
				$model->getTable()[0],
				$relationTargetObj->getTable(),
				$relationTargetObj->getTable()[0],
				sprintf(
					'%s.%s = %s.%s',
					$model->getTable()[0],
					$inflector->snakeize($model->getForeignKey()),
					$relationTargetObj->getTable()[0],
					$relationTargetObj->getPrimaryKey()
				)
			)
			->where(sprintf(
				'%s.%s = ?',
				$model->getTable()[0],
				$inflector->snakeize($model->getForeignKey())
			))
			->setParameter(
				0,
				array_shift($this->savedForeignKeyValue)
			);

		try {
			$statement = $queryBuilder->execute();
		} catch (Throwable $e) {
			throw $e;
		}

		$relationTargetObj = $this->fetchQuantizationSingle(
			$statement,
			$relationTargetObj
		);

		$this->getEntity()
			->modelPropertyAccessor(
				$model,
				$model->getRelationBindObject(),
				EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
				$relationTargetObj
			);

		return $model;
	}

	/**
	 * Resolve entity model object with many-to-many
	 * relationship.
	 *
	 * @param \Modspace\Core\Model\ModelInterface $model
	 * @return \Modspace\Core\Model\ModelInterface
	 */
	private function manyToManyRelationResolver(ModelInterface $model)
	{
		return $this->oneToManyRelationResolver($model);
	}
}
