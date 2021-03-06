<?php

declare(strict_types=1);

namespace Bluepeer\Core;

use Doctrine\DBAL\Connection;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface EntityInterface
{
	public function getConnection(): Connection;

	public function setConnection(Connection $connection);
}
