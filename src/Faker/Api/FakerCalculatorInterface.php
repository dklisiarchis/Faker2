<?php
declare(strict_types=1);

namespace Faker\Api;

/**
 * Base interface for calculators
 */
interface FakerCalculatorInterface
{

    /**
     * Checksum value
     * @param string $value
     * @return mixed
     */
    public static function checksum(string $value): mixed;

    /**
     * Validate value
     * @param string $value
     * @return bool
     */
    public static function isValid(string $value): bool;
}