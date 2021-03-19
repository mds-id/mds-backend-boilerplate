<?php

declare(strict_types=1);

namespace Modspace\Core\Model\Relation;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface RelationType
{
	/**
	 * @var int
	 */
	const ONE_TO_ONE = 1;

	/**
	 * @var int
	 */
	const ONE_TO_MANY = 2;

	/**
	 * @var int
	 */
	const MANY_TO_ONE = 4;

	/**
	 * @var int
	 */
	const MANY_TO_MANY = 8;
}
