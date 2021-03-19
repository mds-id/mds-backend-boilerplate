<?php

declare(strict_types=1);

namespace Modspace\Core\Inflector;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait InflectorTrait
{
	/**
	 * Check if character at index '0' from given
	 * string is lowercase.
	 *
	 * @var string $str
	 * @return bool
	 */
	private function isLower(string $str): bool
	{
		return ord($str[0]) >= 97 && ord($str[0]) <= 122;
	}

	/**
	 * Check if character at index '0' from given
	 * string is uppercase.
	 *
	 * @var string $str
	 * @return bool
	 */
	private function isUpper(string $str): bool
	{
		return ord($str[0]) >= 65 && ord($str[0]) <= 90;
	}
}
