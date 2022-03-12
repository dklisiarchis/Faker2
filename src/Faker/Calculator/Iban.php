<?php
declare(strict_types=1);

namespace Faker\Calculator;

use Faker\Api\FakerCalculatorInterface;

use function substr;
use function preg_replace_callback;
use function str_pad;
use function ord;
use function strlen;

class Iban implements FakerCalculatorInterface
{
    /**
     * Generates IBAN Checksum
     *
     * @param string $value
     * @return string Checksum (numeric string)
     */
    public static function checksum(string $value): string
    {
        // Move first four digits to end and set checksum to '00'
        $checkString = substr($value, 4) . substr($value, 0, 2) . '00';

        // Replace all letters with their number equivalents
        $checkString = preg_replace_callback('/[A-Z]/', ['self','alphaToNumberCallback'], $checkString);

        // Perform mod 97 and subtract from 98
        $checksum = 98 - self::mod97($checkString);

        return str_pad((string) $checksum, 2, '0', STR_PAD_LEFT);
    }

    /**
     * @param string|array $match
     *
     * @return int
     */
    private static function alphaToNumberCallback(array|string $match): int
    {
        return self::alphaToNumber($match[0]);
    }

    /**
     * Converts letter to number
     *
     * @param string $char
     * @return int
     */
    public static function alphaToNumber(string $char): int
    {
        return ord($char) - 55;
    }

    /**
     * Calculates mod97 on a numeric string
     *
     * @param string $number Numeric string
     * @return int
     */
    public static function mod97(string $number): int
    {
        $checksum = (int)$number[0];
        for ($i = 1, $size = strlen($number); $i < $size; $i++) {
            $checksum = (10 * $checksum + (int) $number[$i]) % 97;
        }
        return $checksum;
    }

    /**
     * Checks whether an IBAN has a valid checksum
     *
     * @param string $value
     * @return boolean
     */
    public static function isValid(string $value): bool
    {
        return self::checksum($value) === substr($value, 2, 2);
    }
}
