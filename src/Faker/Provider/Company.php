<?php

namespace Faker\Provider;

class Company extends Base
{
    /**
     * @var string[]
     */
    protected static array $formats = [
        '{{lastName}} {{companySuffix}}',
    ];

    /**
     * @var string[]
     */
    protected static array $companySuffix = ['Ltd'];

    /**
     * @var string[]
     */
    protected static array $jobTitleFormat = [
        '{{word}}',
    ];

    /**
     * @example 'Acme Ltd'
     *
     * @return string
     */
    public function company(): string
    {
        $format = static::randomElement(static::$formats);

        return $this->generator->parse($format);
    }

    /**
     * @example 'Ltd'
     *
     * @return string
     */
    public static function companySuffix(): string
    {
        return static::randomElement(static::$companySuffix);
    }

    /**
     * @example 'Job'
     *
     * @return string
     */
    public function jobTitle(): string
    {
        $format = static::randomElement(static::$jobTitleFormat);

        return $this->generator->parse($format);
    }
}
