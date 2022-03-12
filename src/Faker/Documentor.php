<?php
declare(strict_types=1);

namespace Faker;

use DateTime;
use InvalidArgumentException;
use ReflectionMethod;
use ReflectionObject;

use Faker\Api\FakerGeneratorInterface;
use Faker\Provider\Base as BaseProvider;

use function array_reverse;
use function get_class;
use function var_export;
use function join;

class Documentor
{

    /**
     * @param FakerGeneratorInterface $generator
     */
    public function __construct(
        protected FakerGeneratorInterface $generator
    ) {}

    /**
     * @return array
     */
    public function getFormatters(): array
    {
        $formatters = [];
        $providers = array_reverse($this->generator->getProviders());
        $providers[] = new BaseProvider($this->generator);
        foreach ($providers as $provider) {
            $providerClass = get_class($provider);
            $formatters[$providerClass] = [];
            $reflectionObject = new ReflectionObject($provider);
            foreach ($reflectionObject->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
                if ($reflectionMethod->getDeclaringClass()->getName() == 'Faker\Provider\Base' && $providerClass != 'Faker\Provider\Base') {
                    continue;
                }

                $methodName = $reflectionMethod->name;
                if ($reflectionMethod->isConstructor()) {
                    continue;
                }

                $parameters = [];
                foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                    $parameter = '$'. $reflectionParameter->getName();
                    if ($reflectionParameter->isDefaultValueAvailable()) {
                        $parameter .= ' = ' . var_export($reflectionParameter->getDefaultValue(), true);
                    }

                    $parameters []= $parameter;
                }

                $parameters = $parameters ? '('. join(', ', $parameters) . ')' : '';

                try {
                    $example = $this->generator->format($methodName);
                } catch (InvalidArgumentException $e) {
                    $example = '';
                }

                if (is_array($example)) {
                    $example = "array('". join("', '", $example) . "')";
                } elseif ($example instanceof DateTime) {
                    $example = "DateTime('" . $example->format('Y-m-d H:i:s') . "')";
                } elseif ($example instanceof Generator || $example instanceof UniqueGenerator) { // modifier
                    $example = '';
                } else {
                    $example = var_export($example, true);
                }
                $formatters[$providerClass][$methodName . $parameters] = $example;
            }
        }

        return $formatters;
    }
}
