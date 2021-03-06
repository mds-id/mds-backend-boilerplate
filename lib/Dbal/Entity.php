<?php

declare(strict_types=1);

namespace Bluepeer\Core\Dbal;

use Doctrine\DBAL\Connection;

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
}
