<?php
declare(strict_types=1);

namespace Faker\Provider;

use Faker\Api\FakerGeneratorInterface;
use Faker\Api\FakerProviderInterface;
use Faker\Generator;
use Faker\DefaultGenerator;
use Faker\UniqueGenerator;
use Faker\ValidGenerator;
use InvalidArgumentException;
use LengthException;
use OverflowException;

use function mt_rand;
use function mt_getrandmax;
use function pow;
use function round;
use function min;
use function max;
use function is_bool;
use function is_array;
use function is_string;
use function is_int;
use function count;
use function array_keys;
use function implode;
use function explode;
use function reset;
use function chr;
use function sprintf;
use function function_exists;
use function str_split;
use function strrpos;
use function call_user_func;
use function str_pad;
use function str_repeat;
use function str_replace;
use function mb_strtolower;
use function mb_strlen;
use function mb_substr;
use function mb_strtoupper;
use function strtolower;
use function strtoupper;
use function preg_replace;
use function preg_replace_callback;
use function range;
use function extension_loaded;

class Base implements FakerProviderInterface
{

    /**
     * @var UniqueGenerator|null
     */
    protected ?UniqueGenerator $unique;

    /**
     * @param FakerGeneratorInterface $generator
     */
    public function __construct(
        protected FakerGeneratorInterface $generator
    ) {
        $this->unique = null;
    }

    /**
     * Returns a random number between 0 and 9
     *
     * @return int
     */
    public static function randomDigit(): int
    {
        return mt_rand(0, 9);
    }

    /**
     * Returns a random number between 1 and 9
     *
     * @return int
     */
    public static function randomDigitNotNull(): int
    {
        return mt_rand(1, 9);
    }

    /**
     * Generates a random digit, which cannot be $except
     *
     * @param  int $except
     * @return int
     */
    public static function randomDigitNot(int $except = 0): int
    {
        $result = self::numberBetween(0, 8);
        if ($result >= $except) {
            $result++;
        }
        return $result;
    }

    /**
     * Returns a random integer with 0 to $nbDigits digits.
     *
     * The maximum value returned is mt_getrandmax()
     *
     * @param   int|null $nbDigits Defaults to a random number between 1 and 9
     * @param   bool     $strict   Whether the returned number should have exactly $nbDigits
     * @example 79907610
     *
     * @throws InvalidArgumentException
     * @return int
     */
    public static function randomNumber(?int $nbDigits = null, bool $strict = false): int
    {
        if (!is_bool($strict)) {
            throw new InvalidArgumentException('randomNumber() generates numbers of fixed width. To generate numbers between two boundaries, use numberBetween() instead.');
        }
        if (null === $nbDigits) {
            $nbDigits = static::randomDigitNotNull();
        }
        $max = pow(10, $nbDigits) - 1;
        if ($max > mt_getrandmax()) {
            throw new InvalidArgumentException('randomNumber() can only generate numbers up to mt_getrandmax()');
        }
        if ($strict) {
            return mt_rand(pow(10, $nbDigits - 1), $max);
        }

        return mt_rand(0, $max);
    }

    /**
     * Return a random float number
     *
     * @param   int|null       $nbMaxDecimals
     * @param   int|float|null $min
     * @param   int|float|null $max
     * @example 48.8932
     *
     * @return float
     */
    public static function randomFloat(
        ?int $nbMaxDecimals = null,
        int|float|null $min = 0,
        int|float|null $max = null
    ): float {
        if (null === $nbMaxDecimals) {
            $nbMaxDecimals = static::randomDigit();
        }

        if (null === $max) {
            $max = static::randomNumber();
            if ($min > $max) {
                $max = $min;
            }
        }

        if ($min > $max) {
            $tmp = $min;
            $min = $max;
            $max = $tmp;
        }

        return round($min + mt_rand() / mt_getrandmax() * ($max - $min), $nbMaxDecimals);
    }

    /**
     * Returns a random number between $int1 and $int2 (any order)
     *
     * @param   int $int1 default to 0
     * @param   int $int2 defaults to 32 bit max integer, ie 2147483647
     * @example 79907610
     *
     * @return int
     */
    public static function numberBetween(int $int1 = 0, int $int2 = 2147483647): int
    {
        $min = min($int1, $int2);
        $max = max($int1, $int2);
        return mt_rand($min, $max);
    }
    
    /**
     * Returns the passed value
     *
     * @param mixed $value
     *
     * @return mixed
     */
    //    public static function passthrough($value)
    //    {
    //        return $value;
    //    }

    /**
     * Returns a random letter from a to z
     *
     * @return string
     */
    public static function randomLetter(): string
    {
        return chr(mt_rand(97, 122));
    }

    /**
     * Returns a random ASCII character (excluding accents and special chars)
     *
     * @return string
     */
    public static function randomAscii(): string
    {
        return chr(mt_rand(33, 126));
    }

    /**
     * Returns randomly ordered subsequence of $count elements from a provided array
     *
     * @param  string[] $array           Array to take elements from. Defaults to a-c
     * @param  int      $count           Number of elements to take.
     * @param  bool     $allowDuplicates Allow elements to be picked several times. Defaults to false
     * @throws LengthException When requesting more elements than provided
     *
     * @return array New array with $count elements from $array
     */
    public static function randomElements(
        array $array = ['a', 'b', 'c'],
        int $count = 1,
        bool $allowDuplicates = false
    ): array {
        $traversables = [];

        foreach ($array as $element) {
            $traversables[] = $element;
        }

        $arr = count($traversables) ? $traversables : $array;

        $allKeys = array_keys($arr);
        $numKeys = count($allKeys);

        if (!$allowDuplicates && $numKeys < $count) {
            throw new LengthException(sprintf('Cannot get %d elements, only %d in array', $count, $numKeys));
        }

        $highKey = $numKeys - 1;
        $keys = $elements = [];
        $numElements = 0;

        while ($numElements < $count) {
            $num = mt_rand(0, $highKey);

            if (!$allowDuplicates) {
                if (isset($keys[$num])) {
                    continue;
                }
                $keys[$num] = true;
            }

            $elements[] = $arr[$allKeys[$num]];
            $numElements++;
        }

        return $elements;
    }

    /**
     * Returns a random element from a passed array
     *
     * @param  array $array
     * @return string|int|array|null
     */
    public static function randomElement(array $array = ['a', 'b', 'c']): string|int|array|null
    {
        if (empty($array)) {
            return null;
        }

        $elements = static::randomElements($array);

        return $elements[0];
    }

    /**
     * Returns a random key from a passed associative array
     *
     * @param  array $array
     * @return string|int|null
     */
    public static function randomKey(array $array = []): string|int|null
    {
        if (empty($array)) {
            return null;
        }

        $keys = array_keys($array);
        return $keys[mt_rand(0, count($keys) - 1)];
    }

    /**
     * Returns a shuffled version of the argument.
     *
     * This function accepts either an array, or a string.
     *
     * @example $faker->shuffle([1, 2, 3]); // [2, 1, 3]
     * @example $faker->shuffle('hello, world'); // 'rlo,h eold!lw'
     *
     * @see shuffleArray()
     * @see shuffleString()
     *
     * @param  array|string $arg The set to shuffle
     * @return array|string The shuffled set
     */
    public static function shuffle(array|string $arg = ''): array|string
    {
        if (is_array($arg)) {
            return static::shuffleArray($arg);
        }

        if (is_string($arg)) {
            return static::shuffleString($arg);
        }

        throw new InvalidArgumentException('shuffle() only supports strings or arrays');
    }

    /**
     * Returns a shuffled version of the array.
     *
     * This function does not mutate the original array. It uses the
     * Fisher–Yates algorithm, which is unbiased, together with a Mersenne
     * twister random generator. This function is therefore more random than
     * PHP's shuffle() function, and it is seedable.
     *
     * @link http://en.wikipedia.org/wiki/Fisher%E2%80%93Yates_shuffle
     *
     * @example $faker->shuffleArray([1, 2, 3]); // [2, 1, 3]
     *
     * @param  array $array The set to shuffle
     * @return array The shuffled set
     */
    public static function shuffleArray(array $array = []): array
    {
        $shuffledArray = [];
        $i = 0;
        reset($array);
        foreach ($array as $key => $value) {
            if ($i == 0) {
                $j = 0;
            } else {
                $j = mt_rand(0, $i);
            }
            if ($j == $i) {
                $shuffledArray[]= $value;
            } else {
                $shuffledArray[]= $shuffledArray[$j];
                $shuffledArray[$j] = $value;
            }
            $i++;
        }
        return $shuffledArray;
    }

    /**
     * Returns a shuffled version of the string.
     *
     * This function does not mutate the original string. It uses the
     * Fisher–Yates algorithm, which is unbiased, together with a Mersenne
     * twister random generator. This function is therefore more random than
     * PHP's shuffle() function, and it is seedable. Additionally, it is
     * UTF8 safe if the mb extension is available.
     *
     * @link http://en.wikipedia.org/wiki/Fisher%E2%80%93Yates_shuffle
     *
     * @example $faker->shuffleString('hello, world'); // 'rlo,h eold!lw'
     *
     * @param  string $string   The set to shuffle
     * @param  string $encoding The string encoding (defaults to UTF-8)
     * @return string The shuffled set
     */
    public static function shuffleString(string $string = '', string $encoding = 'UTF-8'): string
    {
        if (function_exists('mb_strlen')) {
            // UTF8-safe str_split()
            $array = [];
            $strlen = mb_strlen($string, $encoding);
            for ($i = 0; $i < $strlen; $i++) {
                $array []= mb_substr($string, $i, 1, $encoding);
            }
        } else {
            $array = str_split($string, 1);
        }
        return implode('', static::shuffleArray($array));
    }

    /**
     * @param  string                $string
     * @param  string                $wildcard
     * @param  string|callable|array $callback
     * @return string
     */
    private static function replaceWildcard(
        string $string,
        string $wildcard = '#',
        string|callable|array $callback = 'static::randomDigit'
    ): string {
        if (($pos = strpos($string, $wildcard)) === false) {
            return $string;
        }
        for ($i = $pos, $last = strrpos($string, $wildcard, $pos) + 1; $i < $last; $i++) {
            if ($string[$i] === $wildcard) {
                $string[$i] = call_user_func($callback);
            }
        }
        return $string;
    }

    /**
     * Replaces all hash sign ('#') occurrences with a random number
     * Replaces all percentage sign ('%') occurrences with a not null number
     *
     * @param  string $string String that needs to bet parsed
     * @return string
     */
    public static function numerify(string $string = '###'): string
    {
        // instead of using randomDigit() several times, which is slow,
        // count the number of hashes and generate once a large number
        $toReplace = [];
        if (($pos = strpos($string, '#')) !== false) {
            for ($i = $pos, $last = strrpos($string, '#', $pos) + 1; $i < $last; $i++) {
                if ($string[$i] === '#') {
                    $toReplace[] = $i;
                }
            }
        }
        if ($nbReplacements = count($toReplace)) {
            $maxAtOnce = strlen((string) mt_getrandmax()) - 1;
            $numbers = '';
            $i = 0;
            while ($i < $nbReplacements) {
                $size = min($nbReplacements - $i, $maxAtOnce);
                $numbers .= str_pad((string) static::randomNumber($size), $size, '0', STR_PAD_LEFT);
                $i += $size;
            }
            for ($i = 0; $i < $nbReplacements; $i++) {
                $string[$toReplace[$i]] = $numbers[$i];
            }
        }

        return self::replaceWildcard($string, '%', 'static::randomDigitNotNull');
    }

    /**
     * Replaces all question mark ('?') occurrences with a random letter
     *
     * @param  string $string String that needs to bet parsed
     * @return string
     */
    public static function lexify(string $string = '????'): string
    {
        return self::replaceWildcard($string, '?', 'static::randomLetter');
    }

    /**
     * Replaces hash signs ('#') and question marks ('?') with random numbers and letters
     * An asterisk ('*') is replaced with either a random number or a random letter
     *
     * @param  string $string String that needs to bet parsed
     * @return string
     */
    public static function bothify(string $string = '## ??'): string
    {
        $string = self::replaceWildcard(
            $string, '*', function () {
                return mt_rand(0, 1) ? '#' : '?';
            }
        );
        return static::lexify(static::numerify($string));
    }

    /**
     * Replaces * signs with random numbers and letters and special characters
     *
     * @example $faker->asciify(''********'); // "s5'G!uC3"
     *
     * @param  string $string String that needs to bet parsed
     * @return string
     */
    public static function asciify(string $string = '****'): string
    {
        return preg_replace_callback('/\*/u', 'static::randomAscii', $string);
    }

    /**
     * Transforms a basic regular expression into a random string satisfying the expression.
     *
     * @example $faker->regexify('[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}'); // sm0@y8k96a.ej
     *
     * Regex delimiters '/.../' and begin/end markers '^...$' are ignored.
     *
     * Only supports a small subset of the regex syntax. For instance,
     * unicode, negated classes, unbounded ranges, subpatterns, back references,
     * assertions, recursive patterns, and comments are not supported. Escaping
     * support is extremely fragile.
     *
     * This method is also VERY slow. Use it only when no other formatter
     * can generate the fake data you want. For instance, prefer calling
     * `$faker->email` rather than `regexify` with the previous regular
     * expression.
     *
     * Also note than `bothify` can probably do most of what this method does,
     * but much faster. For instance, for a dummy email generation, try
     * `$faker->bothify('?????????@???.???')`.
     *
     * @see https://github.com/icomefromthenet/ReverseRegex for a more robust implementation
     *
     * @param  string $regex A regular expression (delimiters are optional)
     * @return string
     */
    public static function regexify(string $regex = ''): string
    {
        // ditch the anchors
        $regex = preg_replace('/^\/?\^?/', '', $regex);
        $regex = preg_replace('/\$?\/?$/', '', $regex);
        // All {2} become {2,2}
        $regex = preg_replace('/\{(\d+)\}/', '{\1,\1}', $regex);
        // Single-letter quantifiers (?, *, +) become bracket quantifiers ({0,1}, {0,rand}, {1, rand})
        $regex = preg_replace('/(?<!\\\)\?/', '{0,1}', $regex);
        $regex = preg_replace('/(?<!\\\)\*/', '{0,' . static::randomDigitNotNull() . '}', $regex);
        $regex = preg_replace('/(?<!\\\)\+/', '{1,' . static::randomDigitNotNull() . '}', $regex);
        // [12]{1,2} becomes [12] or [12][12]
        $regex = preg_replace_callback(
            '/(\[[^\]]+\])\{(\d+),(\d+)\}/', function ($matches) {
                return str_repeat($matches[1], (int) Base::randomElement(range($matches[2], $matches[3])));
            }, $regex
        );
        // (12|34){1,2} becomes (12|34) or (12|34)(12|34)
        $regex = preg_replace_callback(
            '/(\([^\)]+\))\{(\d+),(\d+)\}/', function ($matches) {
                return str_repeat($matches[1], (int) Base::randomElement(range($matches[2], $matches[3])));
            }, $regex
        );
        // A{1,2} becomes A or AA or \d{3} becomes \d\d\d
        $regex = preg_replace_callback(
            '/(\\\?.)\{(\d+),(\d+)\}/', function ($matches) {
                return str_repeat($matches[1], (int) Base::randomElement(range($matches[2], $matches[3])));
            }, $regex
        );
        // (this|that) becomes 'this' or 'that'
        $regex = preg_replace_callback(
            '/\((.*?)\)/', function ($matches) {
                return Base::randomElement(explode('|', str_replace(array('(', ')'), '', $matches[1])));
            }, $regex
        );
        // All A-F inside of [] become ABCDEF
        $regex = preg_replace_callback(
            '/\[([^\]]+)\]/', function ($matches) {
                return '[' . preg_replace_callback(
                    '/(\w|\d)\-(\w|\d)/', function ($range) {
                        return implode('', range($range[1], $range[2]));
                    }, $matches[1]
                ) . ']';
            }, $regex
        );
        // All [ABC] become B (or A or C)
        $regex = preg_replace_callback(
            '/\[([^\]]+)\]/', function ($matches) {
                return Base::randomElement(str_split($matches[1]));
            }, $regex
        );
        // replace \d with number and \w with letter and . with ascii
        $regex = preg_replace_callback('/\\\w/', 'static::randomLetter', $regex);
        $regex = preg_replace_callback('/\\\d/', 'static::randomDigit', $regex);
        $regex = preg_replace_callback('/(?<!\\\)\./', 'static::randomAscii', $regex);
        // remove remaining backslashes
        // phew
        return str_replace('\\', '', $regex);
    }

    /**
     * Converts string to lowercase.
     * Uses mb_string extension if available.
     *
     * @param  string $string String that should be converted to lowercase
     * @return string
     */
    public static function toLower(string $string = ''): string
    {
        return extension_loaded('mbstring') ? mb_strtolower($string, 'UTF-8') : strtolower($string);
    }

    /**
     * Converts string to uppercase.
     * Uses mb_string extension if available.
     *
     * @param  string $string String that should be converted to uppercase
     * @return string
     */
    public static function toUpper(string $string = ''): string
    {
        return extension_loaded('mbstring') ? mb_strtoupper($string, 'UTF-8') : strtoupper($string);
    }

    /**
     * Chainable method for making any formatter optional.
     *
     * @param  float|integer         $weight  Set the probability of receiving a null value.
     *                                        "0" will always return null, "1" will always
     *                                        return the generator. If $weight is an integer
     *                                        value, then the same system works between 0
     *                                        (always get false) and 100 (always get true).
     * @param  string|int|float|null $default
     * @return FakerGeneratorInterface
     */
    public function optional(float|int $weight = 0.5, string|int|float|null $default = null): FakerGeneratorInterface
    {
        if ($weight > 0 && $weight < 1 && mt_rand() / mt_getrandmax() <= $weight) {
            return $this->generator;
        }

        // new system with percentage
        if (is_int($weight) && mt_rand(1, 100) <= $weight) {
            return $this->generator;
        }

        return new DefaultGenerator($default);
    }

    /**
     * Chainable method for making any formatter unique.
     *
     * <code>
     * // will never return twice the same value
     * $faker->unique()->randomElement(array(1, 2, 3));
     * </code>
     *
     * @param  bool $reset      If set to true, resets the list of existing values
     * @param  int  $maxRetries Maximum number of retries to find a unique value,
     *                          After which an OverflowException is thrown.
     * @throws OverflowException When no unique value can be found by iterating $maxRetries times
     *
     * @return UniqueGenerator A proxy class returning only non-existing values
     */
    public function unique(bool $reset = false, int $maxRetries = 10000): UniqueGenerator
    {
        if ($reset || $this->unique === null) {
            $this->unique = new UniqueGenerator($this->generator, $maxRetries);
        }

        return $this->unique;
    }

    /**
     * Chainable method for forcing any formatter to return only valid values.
     *
     * The value validity is determined by a function passed as first argument.
     *
     * <code>
     * $values = array();
     * $evenValidator = function ($digit) {
     *   return $digit % 2 === 0;
     * };
     * for ($i=0; $i < 10; $i++) {
     *   $values []= $faker->valid($evenValidator)->randomDigit;
     * }
     * print_r($values); // [0, 4, 8, 4, 2, 6, 0, 8, 8, 6]
     * </code>
     *
     * @param  callable|null $validator  A function returning true for valid values
     * @param  integer       $maxRetries Maximum number of retries to find a unique value,
     *                                   After which an OverflowException is thrown.
     * @throws OverflowException When no valid value can be found by iterating $maxRetries times
     *
     * @return ValidGenerator A proxy class returning only valid values
     */
    public function valid(?callable $validator = null, int $maxRetries = 10000): ValidGenerator
    {
        return new ValidGenerator($this->generator, $validator, $maxRetries);
    }
}
