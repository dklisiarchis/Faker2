<?php

namespace Faker\Calculator;

use Faker\Api\FakerCalculatorInterface;
use InvalidArgumentException;

use function strlen;
use function substr;
use function str_split;
use function array_map;
use function intval;

class TCNo implements FakerCalculatorInterface
{
    /**
     * Generates Turkish Identity Number Checksum
     * Gets first 9 digit as prefix and calculates checksum
     *
     * https://en.wikipedia.org/wiki/Turkish_Identification_Number
     *
     * @param  string $value
     * @return string Checksum (two digit)
     */
    public static function checksum(string $value): string
    {
        if (strlen($value) !== 9) {
            throw new InvalidArgumentException('Argument should be an integer and should be 9 digits.');
        }

        $oddSum = 0;
        $evenSum = 0;

        $identityArray = array_map('intval', str_split($value)); // Creates array from int
        foreach ($identityArray as $index => $digit) {
            if ($index % 2 == 0) {
                $evenSum += $digit;
            } else {
                $oddSum += $digit;
            }
        }

        $tenthDigit = (7 * $evenSum - $oddSum) % 10;
        $eleventhDigit = ($evenSum + $oddSum + $tenthDigit) % 10;

        return $tenthDigit . $eleventhDigit;
    }

    /**
     * Checks whether a TCNo has a valid checksum
     *
     * @param  string $value
     * @return boolean
     */
    public static function isValid(string $value): bool
    {
        return self::checksum(substr($value, 0, -2)) === substr($value, -2, 2);
    }
}
