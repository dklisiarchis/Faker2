<?php
declare(strict_types=1);

namespace Faker\Calculator;

use Faker\Api\FakerCalculatorInterface;

use function strlen;
use function preg_match;
use function intval;
use function substr;

/**
 * Utility class for validating EAN-8 and EAN-13 numbers
 *
 * @package Faker\Calculator
 */
class Ean implements FakerCalculatorInterface
{
    /**
     * @var string EAN validation pattern 
     */
    public const PATTERN = '/^(?:\d{8}|\d{13})$/';

    /**
     * Computes the checksum of an EAN number.
     *
     * @see https://en.wikipedia.org/wiki/International_Article_Number
     *
     * @param  string $value
     * @return int
     */
    public static function checksum(string $value): int
    {
        $length = strlen($value);

        $even = 0;
        for ($i = $length - 1; $i >= 0; $i -= 2) {
            $even += (int) $value[$i];
        }

        $odd = 0;
        for ($i = $length - 2; $i >= 0; $i -= 2) {
            $odd += (int) $value[$i];
        }

        return (10 - ((3 * $even + $odd) % 10)) % 10;
    }

    /**
     * Checks whether the provided number is an EAN compliant number and that
     * the checksum is correct.
     *
     * @param  string $value An EAN number
     * @return bool
     */
    public static function isValid(string $value): bool
    {
        if (!preg_match(self::PATTERN, $value)) {
            return false;
        }

        return self::checksum(substr($value, 0, -1)) === intval(substr($value, -1));
    }
}
