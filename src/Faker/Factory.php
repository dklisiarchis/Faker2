<?php
declare(strict_types=1);

namespace Faker;

use InvalidArgumentException;

use Faker\Api\FakerGeneratorInterface;

use function class_exists;
use function sprintf;
use function strlen;

class Factory
{

    /**
     * @var string
     */
    public const DEFAULT_LOCALE = 'en_US';

    /**
     * @var string[]
     */
    protected static array $defaultProviders = [
        'Address',
        'Barcode',
        'Biased',
        'Color',
        'Company',
        'DateTime',
        'File',
        'HtmlLorem',
        'Image',
        'Internet',
        'Lorem',
        'Miscellaneous',
        'Payment',
        'Person',
        'PhoneNumber',
        'Text',
        'UserAgent',
        'Uuid'
    ];

    /**
     * Create a new generator
     *
     * @param string $locale
     * @return Generator
     */
    public static function create(string $locale = self::DEFAULT_LOCALE): FakerGeneratorInterface
    {
        $generator = new Generator();
        foreach (static::$defaultProviders as $provider) {
            $providerClassName = self::getProviderClassname($provider, $locale);
            $generator->addProvider(new $providerClassName($generator));
        }

        return $generator;
    }

    /**
     * @param string $provider
     * @param string $locale
     * @return string
     */
    protected static function getProviderClassname(string $provider, string $locale = ''): string
    {
        if ($providerClass = self::findProviderClassname($provider, $locale)) {
            return $providerClass;
        }

        // fallback to no locale
        if ($providerClass = self::findProviderClassname($provider)) {
            return $providerClass;
        }

        // fallback to default locale
        if ($providerClass = self::findProviderClassname($provider, static::DEFAULT_LOCALE)) {
            return $providerClass;
        }

        throw new InvalidArgumentException(sprintf('Unable to find provider "%s" with locale "%s"', $provider, $locale));
    }

    /**
     * @param string $provider
     * @param string $locale
     * @return string
     */
    protected static function findProviderClassname(string $provider, string $locale = ''): string
    {
        $providerClass = strlen($locale)
            ? sprintf('Faker\Provider\%s\%s', $locale, $provider)
            : sprintf('Faker\Provider\%s', $provider);
        if (class_exists($providerClass, true)) {
            return $providerClass;
        }

        $providerClass = sprintf('Faker\Provider\%s', $provider);
        if (class_exists($providerClass, true)) {
            return $providerClass;
        }

        throw new InvalidArgumentException(sprintf('Unable to find provider "%s" with locale "%s" class: %s', $provider, $locale, $providerClass));
    }
}
