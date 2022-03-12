<?php
declare(strict_types=1);

namespace Faker\Provider\en_US;

use function str_repeat;
use function sprintf;

class Payment extends \Faker\Provider\Payment
{

    /**
     * @return string
     */
    public function bankAccountNumber(): string
    {
        // Length between 5 and 17, biased towards center
        $length = self::numberBetween(0, 3) + self::numberBetween(0, 3) + self::numberBetween(0, 3) + self::numberBetween(0, 3) + 5;

        return self::numerify(str_repeat('#', $length));
    }

    /**
     * @return string
     */
    public function bankRoutingNumber(): string
    {
        $district = self::numberBetween(1, 12);
        $type = self::randomElement([0, 0, 0, 0, 20, 20, 60]);
        $clearingCenter = self::randomDigitNotNull();
        $state = self::randomDigit();
        $institution = self::randomNumber(4, true);

        $result = sprintf('%02d%01d%01d%04d', $district + $type, $clearingCenter, $state, $institution);

        return $result . self::calculateRoutingNumberChecksum($result);
    }

    /**
     * @param  array|string $routing
     * @return int
     */
    public static function calculateRoutingNumberChecksum(array|string $routing = ['122105155']): int
    {
        return (
                7 * ($routing[0] + $routing[3] + $routing[6]) +
                3 * ($routing[1] + $routing[4] + $routing[7]) +
                9 * ($routing[2] + $routing[5])
            ) % 10;
    }
}