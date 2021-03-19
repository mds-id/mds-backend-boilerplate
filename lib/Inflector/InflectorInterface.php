<?php

declare(strict_types=1);

namespace Modspace\Core\Inflector;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface InflectorInterface
{
	/**
	 * Convert given string into snake-case form.
	 *
	 * @param string $str
	 * @return string
	 */
	public function snakeize(string $str): string;
}
