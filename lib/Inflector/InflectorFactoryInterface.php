<?php

declare(strict_types=1);

namespace Modspace\Core\Inflector;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface InflectorFactoryInterface
{
	/**
	 * Get an instance of simple inflector class
	 * object.
	 *
	 * @return \Modspace\Core\Inflector\InflectorInterface
	 */
	public function createSimpleInflector(): InflectorInterface;
}
