<?php

declare(strict_types=1);

namespace Modspace\Core;

use Psr\Container\ContainerInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface ContainerAwareInterface
{
	/**
	 * Get container object.
	 *
	 * @return \Psr\Container\ContainerInterface
	 */
	public function getContainer(): ContainerInterface;
}
