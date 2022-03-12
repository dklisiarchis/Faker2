<?php
declare(strict_types=1);

namespace Faker;

use OverflowException;

use Faker\Api\FakerGeneratorInterface;
use Faker\Api\FakerProviderInterface;

use function array_key_exists;
use function call_user_func_array;
use function serialize;
use function sprintf;

/**
 * Proxy for other generators, to return only unique values. Works with
 * Faker\Generator\Base->unique()
 */
class UniqueGenerator implements FakerGeneratorInterface
{

    /**
     * @var array
     */
    protected array $uniques;

    /**
     * @param Generator $generator
     * @param integer   $maxRetries
     */
    public function __construct(
        protected FakerGeneratorInterface $generator,
        protected int $maxRetries = 10000
    ) {
        $this->uniques = [];
    }

    /**
     * Catch and proxy all generator calls but return only unique values
     *
     * @param  string $attribute
     * @return mixed
     */
    public function __get(string $attribute): mixed
    {
        return $this->__call($attribute, []);
    }

    /**
     * Catch and proxy all generator calls with arguments but return only unique values
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (!isset($this->uniques[$name])) {
            $this->uniques[$name] = [];
        }
        $i = 0;
        do {
            $res = call_user_func_array([$this->generator, $name], $arguments);
            $i++;
            if ($i > $this->maxRetries) {
                throw new OverflowException(sprintf('Maximum retries of %d reached without finding a unique value', $this->maxRetries));
            }
        } while (array_key_exists(serialize($res), $this->uniques[$name]));
        $this->uniques[$name][serialize($res)]= null;

        return $res;
    }

    /**
     * @inheritDoc
     */
    public function addProvider(FakerProviderInterface $provider): void
    {
    }

    /**
     * @inheritDoc
     */
    public function getProviders(): array
    {
        return [];
    }
}
