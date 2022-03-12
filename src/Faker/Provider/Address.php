<?php
declare(strict_types=1);

namespace Faker\Provider;

class Address extends Base
{

    /**
     * @var string[]
     */
    protected static array $citySuffix = ['Ville'];

    /**
     * @var string[]
     */
    protected static array $streetSuffix = ['Street'];

    /**
     * @var string[]
     */
    protected static array $cityFormats = [
        '{{firstName}}{{citySuffix}}'
    ];

    /**
     * @var string[]
     */
    protected static array $streetNameFormats = [
        '{{lastName}} {{streetSuffix}}'
    ];

    /**
     * @var string[]
     */
    protected static array $streetAddressFormats = [
        '{{buildingNumber}} {{streetName}}'
    ];

    /**
     * @var string[]
     */
    protected static array $addressFormats = [
        '{{streetAddress}} {{postcode}} {{city}}',
    ];

    /**
     * @var string[]
     */
    protected static array $buildingNumber = ['%#'];

    /**
     * @var string[]
     */
    protected static array $postcode = ['#####'];

    /**
     * @var string[]
     */
    protected static array $country = [];

    /**
     * @example 'town'
     * @return  string
     */
    public static function citySuffix(): string
    {
        return static::randomElement(static::$citySuffix);
    }

    /**
     * @example 'Avenue'
     * @return  string
     */
    public static function streetSuffix(): string
    {
        return static::randomElement(static::$streetSuffix);
    }

    /**
     * @example '791'
     * @return  string
     */
    public static function buildingNumber(): string
    {
        return static::numerify(static::randomElement(static::$buildingNumber));
    }

    /**
     * @example 'Sashabury'
     * @return  string
     */
    public function city(): string
    {
        $format = static::randomElement(static::$cityFormats);

        return $this->generator->parse($format);
    }

    /**
     * @example 'Crist Parks'
     * @return  string
     */
    public function streetName(): string
    {
        $format = static::randomElement(static::$streetNameFormats);

        return $this->generator->parse($format);
    }

    /**
     * @example '791 Crist Parks'
     * @return  string
     */
    public function streetAddress(): string
    {
        $format = static::randomElement(static::$streetAddressFormats);

        return $this->generator->parse($format);
    }

    /**
     * @example 86039-9874
     * @return  string
     */
    public static function postcode(): string
    {
        return static::toUpper(static::bothify(static::randomElement(static::$postcode)));
    }

    /**
     * @example '791 Crist Parks, Sashabury, IL 86039-9874'
     * @return  string
     */
    public function address(): string
    {
        $format = static::randomElement(static::$addressFormats);

        return $this->generator->parse($format);
    }

    /**
     * @example 'Japan'
     * @return  string
     */
    public static function country(): string
    {
        return static::randomElement(static::$country);
    }

    /**
     * @example '77.147489'
     * @param   float|int $min
     * @param   float|int $max
     * @return  float Uses signed degrees format (returns a float number between -90 and 90)
     */
    public static function latitude(float|int $min = -90, float|int $max = 90): float
    {
        return static::randomFloat(6, $min, $max);
    }

    /**
     * @example '86.211205'
     * @param   float|int $min
     * @param   float|int $max
     * @return  float Uses signed degrees format (returns a float number between -180 and 180)
     */
    public static function longitude(float|int $min = -180, float|int $max = 180): float
    {
        return static::randomFloat(6, $min, $max);
    }

    /**
     * @example array('77.147489', '86.211205')
     * @return  array  latitude, longitude
     */
    public static function localCoordinates(): array
    {
        return array(
            'latitude' => static::latitude(),
            'longitude' => static::longitude()
        );
    }
}
