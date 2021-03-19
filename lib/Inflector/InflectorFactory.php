<?php

declare(strict_types=1);

namespace Modspace\Core\Inflector;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class InflectorFactory implements InflectorFactoryInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function createSimpleInflector(): InflectorInterface
	{
		return new SimpleInflector();
	}
}
