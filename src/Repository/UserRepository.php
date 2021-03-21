<?php

declare(strict_types=1);

namespace Modspace\Repository;

use Modspace\Core\Dbal\EntityInterface;
use Modspace\Core\Repository\EntityRepository;

class UserRepository extends EntityRepository
{
	public function customFindAll()
	{
		return [];
	}
}
