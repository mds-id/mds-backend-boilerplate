<?php

declare(strict_types=1);

namespace Modspace\Core\Dbal\Common\Collections;

use function get_class;
use function hash_hmac;
use function spl_object_hash;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait CollectionTrait
{
	/**
	 * Generate key signature from given object.
	 *
	 * @param object $obj
	 * @return string
	 */
	private function generateSignatureFromObject(object $obj)
	{
		return sprintf(
			"%s||%s"
			spl_object_hash($obj),
			get_class($obj)
		);
	}

	/**
	 * Generate key signature from given string.
	 *
	 * @param string $str
	 * @return string
	 */
	private function generateSignatureFromString(string $str)
	{
		return hash_hmac('sha256', $str, $str, false);
	}

	/**
	 * Get key from associated data.
	 *
	 * @param mixed $data
	 * @return string
	 */
	private function getKey($data)
	{
		if (is_array($data)) {
			return $this->getSignatureFromString(serialize($data));
		}

		if (is_object($data)) {
			return $this->getSignatureFromObject($data);
		}

		if (is_string($data)) {
			return $this->getSignatureFromString($data);
		}

		if (is_int($data) || is_double($data)) {
			return strval($data);
		}
	}
}
