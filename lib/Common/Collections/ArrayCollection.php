<?php

declare(strict_types=1);

namespace Modspace\Core\Common\Collections;

use ArrayIterator;
use Traversable;

use function iterator_to_array;
use function sizeof;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ArrayCollection implements Collection
{
	use CollectionTrait;

	/**
	 * @var array
	 */
	private $collection;

	/**
	 * @param array $collection
	 * @return static
	 */
	public function __construct(array $collection = [])
	{
		$this->collection = $collection;
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetExists($offset): bool
	{
		return isset($this->collection[$offset]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($offset)
	{
		return !$this->offsetExists($offset)
			? null
			: $this->collection[$offset];
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetSet($offset, $value)
	{
		$this->collection[$offset] = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetUnset($offset)
	{
		if (!$this->offsetExists($offset)) {
			return;
		}

		unset($this->collection[$offset]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->collection);
	}

	/**
	 * {@inheritdoc}
	 */
	public function count(): int
	{
		return sizeof($this->collection);
	}

	/**
	 * {@inheritdoc}
	 */
	public function append($data)
	{
		$this->offsetSet($this->getKey($data), $data);
	}

	/**
	 * {@inheritdoc}
	 */
	public function contains($data)
	{
		return $this->offsetExists($this->getKey($data));
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove($data)
	{
		if (!$this->contains($data)) {
			return;
		}

		$this->offsetUnset($this->getKey($data));
	}

	/**
	 * {@inheritdoc}
	 */
	public function toArray(): array
	{
		return iterator_to_array($this, false);
	}
}
