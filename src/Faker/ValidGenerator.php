<?php

namespace Faker;

use Closure;
use OverflowException;

use Faker\Api\FakerGeneratorInterface;
use Faker\Api\FakerProviderInterface;

use function is_null;
use function is_callable;
use function call_user_func;
use function call_user_func_array;
use function sprintf;

/**
 * Proxy for other generators, to return only valid values. Works with
 * Faker\Generator\Base->valid()
 */
class ValidGenerator implements FakerGeneratorInterface
{

    /**
     * @param Generator     $generator
     * @param Closure|null  $validator
     * @param integer       $maxRetries
     */
    public function __construct(
        protected FakerGeneratorInterface $generator,
        protected ?Closure                $validator = null,
        protected int                     $maxRetries = 10000
    ) {
        if (is_null($validator)) {
            $this->validator = function () {
                return true;
            };
        } elseif (!is_callable($validator)) {
            throw new \InvalidArgumentException('valid() only accepts callables as first argument');
        }
    }

    /**
     * @param FakerProviderInterface $provider
     * @return void
     */
    public function addProvider(FakerProviderInterface $provider): void
    {}

    /**
     * @return FakerProviderInterface[]
     */
    public function getProviders(): array
    {
        return [];
    }

    /**
     * Catch and proxy all generator calls but return only valid values
     * @param string $attribute
     *
     * @return mixed
     */
    public function __get(string $attribute): mixed
    {
        return $this->__call($attribute, []);
    }

    /**
     * Catch and proxy all generator calls with arguments but return only valid values
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        $i = 0;
        do {
            $res = call_user_func_array([$this->generator, $name], $arguments);
            $i++;
            if ($i > $this->maxRetries) {
                throw new OverflowException(sprintf('Maximum retries of %d reached without finding a valid value', $this->maxRetries));
            }
        } while (!call_user_func($this->validator, $res));

        return $res;
    }
}
