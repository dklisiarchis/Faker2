<?php
declare(strict_types=1);

namespace Faker\Provider;

class Person extends Base
{

    /**
     * @var string
     */
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    /**
     * @var string[]
     */
    protected static array $titleFormat = [
      '{{titleMale}}',
      '{{titleFemale}}',
    ];

    /**
     * @var string[]
     */
    protected static array $firstNameFormat = [
      '{{firstNameMale}}',
      '{{firstNameFemale}}',
    ];

    /**
     * @var string[]
     */
    protected static array $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
    ];

    /**
     * @var string[]
     */
    protected static array $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
    ];

    /**
     * @var string[]
     */
    protected static array $firstNameMale = [
        'John',
    ];

    /**
     * @var string[]
     */
    protected static array $firstNameFemale = [
        'Jane',
    ];

    /**
     * @var string[]
     */
    protected static array $lastName = ['Doe'];

    /**
     * @var string[]
     */
    protected static array $titleMale = ['Mr.', 'Dr.', 'Prof.'];

    /**
     * @var string[]
     */
    protected static array $titleFemale = ['Mrs.', 'Ms.', 'Miss', 'Dr.', 'Prof.'];

    /**
     * @param   string|null $gender 'male', 'female' or null for any
     * @return  string
     * @example 'John Doe'
     */
    public function name(?string $gender = null): string
    {
        if ($gender === static::GENDER_MALE) {
            $format = static::randomElement(static::$maleNameFormats);
        } elseif ($gender === static::GENDER_FEMALE) {
            $format = static::randomElement(static::$femaleNameFormats);
        } else {
            $format = static::randomElement(array_merge(static::$maleNameFormats, static::$femaleNameFormats));
        }

        return $this->generator->parse($format);
    }

    /**
     * @param   string|null $gender 'male', 'female' or null for any
     * @return  string
     * @example 'John'
     */
    public function firstName(?string $gender = null): string
    {
        if ($gender === static::GENDER_MALE) {
            return static::firstNameMale();
        } elseif ($gender === static::GENDER_FEMALE) {
            return static::firstNameFemale();
        }

        return $this->generator->parse(static::randomElement(static::$firstNameFormat));
    }

    /**
     * @return string
     */
    public static function firstNameMale(): string
    {
        return static::randomElement(static::$firstNameMale);
    }

    /**
     * @return string
     */
    public static function firstNameFemale(): string
    {
        return static::randomElement(static::$firstNameFemale);
    }

    /**
     * @example 'Doe'
     * @return  string
     */
    public function lastName(): string
    {
        return static::randomElement(static::$lastName);
    }

    /**
     * @example 'Mrs.'
     * @param   string|null $gender 'male', 'female' or null for any
     * @return  string
     */
    public function title(?string $gender = null): string
    {
        if ($gender === static::GENDER_MALE) {
            return static::titleMale();
        } elseif ($gender === static::GENDER_FEMALE) {
            return static::titleFemale();
        }

        return $this->generator->parse(static::randomElement(static::$titleFormat));
    }

    /**
     * @example 'Mr.'
     * @return  string
     */
    public static function titleMale(): string
    {
        return static::randomElement(static::$titleMale);
    }

    /**
     * @example 'Mrs.'
     * @return  string
     */
    public static function titleFemale(): string
    {
        return static::randomElement(static::$titleFemale);
    }
}
