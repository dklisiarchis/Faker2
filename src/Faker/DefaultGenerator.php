<?php

namespace Faker;

use Faker\Api\FakerGeneratorInterface;
use Faker\Api\FakerProviderInterface;

/**
 * This generator returns a default value for all called properties
 * and methods. It works with Faker\Generator\Base->optional().
 */
class DefaultGenerator implements FakerGeneratorInterface
{

    /**
     * @param string|int|float|null $default
     */
    public function __construct(
        protected string|int|float|null $default = null
    ) {
    }

    /**
     * @param string $attribute
     *
     * @return string|int|float|null
     */
    public function __get(string $attribute): string|int|float|null
    {
        return $this->default;
    }

    /**
     * @param string $method
     * @param array  $attributes
     *
     * @return string|int|float|null
     */
    public function __call(string $method, array $attributes): string|int|float|null
    {
        return $this->default;
    }

    public function addProvider(FakerProviderInterface $provider): void
    {
    }

    public function getProviders(): array
    {
        return [];
    }
}
