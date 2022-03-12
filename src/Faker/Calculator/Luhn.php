<?php
declare(strict_types=1);

namespace Faker\Calculator;

use Faker\Api\FakerCalculatorInterface;
use InvalidArgumentException;

use function strlen;
use function str_split;
use function strval;
use function array_sum;
use function preg_match;

/**
 * Utility class for generating and validating Luhn numbers.
 *
 * Luhn algorithm is used to validate credit card numbers, IMEI numbers, and
 * National Provider Identifier numbers.
 *
 * @see http://en.wikipedia.org/wiki/Luhn_algorithm
 */
class Luhn implements FakerCalculatorInterface
{
    /**
     * @param string|int $value
     * @return int
     */
    public static function checksum(string|int $value): int
    {
        $number = (string) $value;
        $length = strlen($number);
        $sum = 0;
        for ($i = $length - 1; $i >= 0; $i -= 2) {
            $sum += (int) $number[$i];
        }
        for ($i = $length - 2; $i >= 0; $i -= 2) {
            $sum += array_sum(str_split(strval($number[$i] * 2)));
        }

        return $sum % 10;
    }

    /**
     * @param string|int $partialNumber
     * @return string
     */
    public static function computeCheckDigit(string|int $partialNumber): string
    {
        $checkDigit = self::checksum($partialNumber . '0');
        if ($checkDigit === 0) {
            return '0';
        }

        return (string) (10 - $checkDigit);
    }

    /**
     * Checks whether a number (partial number + check digit) is Luhn compliant
     *
     * @param string $value
     * @return bool
     */
    public static function isValid(string $value): bool
    {
        return self::checksum($value) === 0;
    }

    /**
     * Generate a Luhn compliant number.
     *
     * @param string $partialValue
     *
     * @return string
     */
    public static function generateLuhnNumber(string $partialValue): string
    {
        if (!preg_match('/^\d+$/', $partialValue)) {
            throw new InvalidArgumentException('Argument should be an integer.');
        }

        return $partialValue . Luhn::computeCheckDigit($partialValue);
    }
}
