<?php

declare(strict_types=1);

namespace Modspace\Core\Dbal\Common\Collections;

use ArrayAccess;
use Countable;
use IteratorAggregate;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface Collection extends Countable, IteratorAggregate, ArrayAccess
{
	/**
	 * Append given data to the collection.
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function append($data);

	/**
	 * Check if given elements is exists in the
	 * collection.
	 *
	 * @param mixed $data
	 * @return bool
	 */
	public function contains($data);

	/**
	 * Remove given elements from the collection.
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function remove($data);
}
