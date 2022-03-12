<?php
declare(strict_types=1);

namespace Faker\Provider;

use Faker\Calculator\Luhn;

class PhoneNumber extends Base
{

    /**
     * @var string[]
     */
    protected static array $formats = ['###-###-###'];

    /**
     * @example '555-123-546'
     * @return  string
     */
    public function phoneNumber(): string
    {
        return static::numerify($this->generator->parse(static::randomElement(static::$formats)));
    }

    /**
     * @example +27113456789
     * @return  string
     */
    public function e164PhoneNumber(): string
    {
        $formats = ['+%############'];
        return static::numerify($this->generator->parse(static::randomElement($formats)));
    }

    /**
     * International Mobile Equipment Identity (IMEI)
     *
     * @link    http://en.wikipedia.org/wiki/International_Mobile_Station_Equipment_Identity
     * @link    http://imei-number.com/imei-validation-check/
     * @example '720084494799532'
     * @return  string $imei
     */
    public function imei(): string
    {
        $imei = static::numerify('##############');
        $imei .= Luhn::computeCheckDigit($imei);
        return $imei;
    }
}
