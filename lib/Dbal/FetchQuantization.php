<?php

declare(strict_types=1);

namespace Modspace\Core\Dbal;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface FetchQuantization
{
	/**
	 * @var int
	 */
	const QUANTIZATION_SINGLE = 1;

	/**
	 * @var int
	 */
	const QUANTIZATION_MULTIPLE = 2;
}
