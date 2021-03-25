<?php

declare(strict_types=1);

namespace Modspace\Core\Dbal;

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
		$normalized   = $this->transformEntity($model);
		$queryBuilder = $this->getConnection()
			->createQueryBuilder()
			->insert($model->getTable())
	}

	private function handleManyToManyRelationalPersist(ModelInterface $model)
	{
	}
}
