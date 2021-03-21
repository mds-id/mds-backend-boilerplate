<?php

declare(strict_types=1);

namespace Modspace\Core\Repository;

use Doctrine\DBAL\Driver\ResultStatement;
use Modspace\Core\Dbal\FetchQuantization;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait EntityRepositoryTrait
{
	/**
	 * Check if current entity class object has
	 * relation or otherwise.
	 *
	 * @param \Modspace\Core\Model\ModelInterface
	 * @return bool
	 */
	private function hasRelation(ModelInterface $model): bool
	{
		if (!$model->getRelationType()) {
			return false;
		}

		switch ($model->getRelationType()) {
			case RelationType::ONE_TO_ONE:
			case RelationType::ONE_TO_MANY:
			case RelationType::MANY_TO_ONE:
			case RelationType::MANY_TO_MANY:
				return true;
		}

		return false;
	}

	private function validateRelationType(int $relationMode)
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

	private function handleOneToOneRelation(ModelInterface $model, $id = null)
	{
		$relationMetadata = $model->getRelationMetadata();
		$quantizationMode = null === $id
			? FetchQuantization::QUANTIZATION_SINGLE
			: FetchQuantization::QUANTIZATION_MULTIPLE;

		$inflector    = $this->getEntity()
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
			return $this->relationResolver($parent, RelationType::ONE_TO_ONE);
		}

		array_walk($parent, function(&$val) {
			$val = $this->relationResolver($val, RelationType::ONE_TO_ONE);
		});

		return $parent;
	}

	public function fetchQuantizationSingle(
		ResultStatement $statement,
		ModelInterface $model
	): ModelInterface {
		$row = $statement->fetchAssociative();

		if ($row === null) {
			return null;
		}

		$properties = $this->getEntity()
			->getModelClassProperties($model);
		$resultObj  = clone $model;

		foreach ($properties as $name) {
			$this->getEntity()
				->modelPropertyAccessor(
					$resultObj,
					$name,
					EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
					$row[$inflector->snakeize($name)]
				);
		}

		return $resultObj;
	}

	public function fetchQuantizationMultiple(
		ResultStatement $statement,
		ModelInterface $model
	): array {
		$aggregated = [];
		$inflector  = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$properties = $this->getEntity()
			->getModelClassProperties($model);

		while (($row = $statement->fetchAssociative()) !== false) {
			$resultObj = clone $model;

			foreach ($properties as $name) {
				$this->getEntity()
					->modelPropertyAccessor(
						$resultObj,
						$name,
						EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
						$row[$inflector->snakeize($name)]
					);
			}

			$aggregated[] = $obj;
		}

		return $aggregated;
	}

	private function relationResolver(
		ModelInterface $model,
		int $relationType
	): ModelInterface {
		if (!$this->hasRelation($model)) {
			throw new InvalidArgumentException(
				"Given entity class model have no relation."
			);
		}

		if (!$this->validateRelationType($relation)) {
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

	private function oneToOneRelationResolver(ModelInterface $model)
	{
		$relationTarget    = $model->getRelationTargetClass();
		$relationTargetObj = new $relationTarget();
		$inflector         = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$queryBuilder      = $this->getQueryBuilder()
			->select(sprintf('%s.*', $model->getTable()[0]))
			->from($model->getTable(), $model->getTable()[0])
			->join(
				$model->getTable()[0],
				$relationTargetObj->getTable(),
				$relationTargetObj->getTable()[0],
				sprintf(
					'%s.%s = %s.%s',
					$relationTargetObj->getTable()[0],
					$relationTargetObj->getForeignKey(),
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

		$this->getEntity()
			->modelPropertyAccessor(
				$model,
				$relationTargetObj->getRelationBindObject(),
				EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
				$relationTargetObj
			);

		return $model;
	}

	private function oneToManyRelationResolver(ModelInterface $model)
	{
		$relationTarget    = $model->getRelationTargetClass();
		$relationTargetObj = new $relationTarget();
		$inflector         = $this->getEntity()
			->getInflectorFactory()
			->createSimpleInflector();
		$queryBuilder      = $this->getQueryBuilder()
			->select(sprintf('%s.*', $model->getTable()[0]))
			->from($model->getTable(), $model->getTable()[0])
			->join(
				$model->getTable()[0],
				$relationTargetObj->getTable(),
				$relationTargetObj->getTable()[0],
				sprintf(
					'%s.%s = %s.%s',
					$relationTargetObj->getTable()[0],
					$relationTargetObj->getPrimaryKey(),
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

		$this->getEntity()
			->modelPropertyAccessor(
				$model,
				$relationTargetObj->getRelationBindObject(),
				EntityInterface::MODEL_PROPERTY_ACCESS_WRITE,
				$relationTargetObjs
			);

		return $model;
	}
}
