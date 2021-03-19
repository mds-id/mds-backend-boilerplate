<?php

declare(strict_types=1);

namespace Modspace\Core\Inflector;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class SimpleInflector implements InflectorInterface
{
	use InflectorTrait;

	/**
	 * {@inheritdoc}
	 */
	public function snakeize(string $str): string
	{
		$result = '';

		for ($i = 0; $i < strlen($str); $i++) {
			$result .= $this->isUpper($str[$i])
				? '_' . strtolower($str[$i])
				: $str[$i];
		}

		return $result;
	}
}
