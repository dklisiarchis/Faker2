<?php
declare(strict_types=1);

namespace Faker\Provider;

use function array_shift;
use function count;
use function preg_replace;
use function preg_match;
use function explode;
use function implode;
use function function_exists;
use function mb_strlen;
use function strlen;

abstract class Text extends Base
{

    /**
     * Base text
     *
     * @var string
     */
    protected static string $baseText = '';

    /**
     * @var string
     */
    protected static string $separator = ' ';

    /**
     * @var int
     */
    protected static int $separatorLen = 1;

    /**
     * @var string[]|null
     */
    protected array $explodedText;

    /**
     * @var string[]
     */
    protected array $consecutiveWords = [];

    /**
     * @var bool
     */
    protected static bool $textStartsWithUppercase = true;

    /**
     * Generate a text string by the Markov chain algorithm.
     *
     * Depending on the $maxNbChars, returns a random valid looking text. The algorithm
     * generates a weighted table with the specified number of words as the index and the
     * possible following words as the value.
     *
     * @example 'Alice, swallowing down her flamingo, and began by taking the little golden key'
     * @param   int $maxNbChars Maximum number of characters the text should contain (minimum: 10)
     * @param   int $indexSize  Determines how many words are considered for the generation of the next word.
     *                          The minimum is 1, and it produces a higher level of randomness, although the
     *                          generated text usually doesn't make sense. Higher index sizes (up to 5)
     *                          produce more correct text, at the price of less randomness.
     * @return  string
     */
    public function realText(int $maxNbChars = 200, int $indexSize = 2): string
    {
        if ($maxNbChars < 10) {
            throw new \InvalidArgumentException('maxNbChars must be at least 10');
        }

        if ($indexSize < 1) {
            throw new \InvalidArgumentException('indexSize must be at least 1');
        }

        if ($indexSize > 5) {
            throw new \InvalidArgumentException('indexSize must be at most 5');
        }

        $words = $this->getConsecutiveWords($indexSize);
        $result = [];
        $resultLength = 0;
        // take a random starting point
        $next = static::randomKey($words);
        while ($resultLength < $maxNbChars && isset($words[$next])) {
            // fetch a random word to append
            $word = static::randomElement($words[$next]);

            // calculate next index
            $currentWords = static::explode($next);
            $currentWords[] = $word;
            array_shift($currentWords);
            $next = static::implode($currentWords);

            // ensure text starts with an uppercase letter
            if ($resultLength == 0 && !static::validStart($word)) {
                continue;
            }

            // append the element
            $result[] = $word;
            $resultLength += static::strlen($word) + static::$separatorLen;
        }

        // remove the element that caused the text to overflow
        array_pop($result);

        // build result
        $result = static::implode($result);

        return static::appendEnd($result);
    }

    /**
     * @param  int $indexSize
     * @return array
     */
    protected function getConsecutiveWords(int $indexSize): array
    {
        if (!isset($this->consecutiveWords[$indexSize])) {
            $parts = $this->getExplodedText();
            $words = [];
            $index = [];
            for ($i = 0; $i < $indexSize; $i++) {
                $index[] = array_shift($parts);
            }

            for ($i = 0, $count = count($parts); $i < $count; $i++) {
                $stringIndex = static::implode($index);
                if (!isset($words[$stringIndex])) {
                    $words[$stringIndex] = [];
                }
                $word = $parts[$i];
                $words[$stringIndex][] = $word;
                array_shift($index);
                $index[] = $word;
            }
            // cache look up words for performance
            $this->consecutiveWords[$indexSize] = $words;
        }

        return $this->consecutiveWords[$indexSize];
    }

    /**
     * @return string[]
     */
    protected function getExplodedText(): array
    {
        if (!isset($this->explodedText)) {
            $this->explodedText = static::explode(preg_replace('/\s+/u', ' ', static::$baseText));
        }

        return $this->explodedText;
    }

    /**
     * @param  string $text
     * @return array|bool
     */
    protected static function explode(string $text): array|bool
    {
        return explode(static::$separator, $text);
    }

    /**
     * @param  string[]|null $words
     * @return string
     */
    protected static function implode(?array $words = null): string
    {
        return implode(static::$separator, $words);
    }

    /**
     * @param  string $text
     * @return int
     */
    protected static function strlen(string $text): int
    {
        return function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
    }

    /**
     * @param  string $word
     * @return bool
     */
    protected static function validStart(string $word): bool
    {
        $isValid = true;
        if (static::$textStartsWithUppercase) {
            $isValid = preg_match('/^\p{Lu}/u', $word);
            return (bool) $isValid;
        }
        return $isValid;
    }

    /**
     * @param  string $text
     * @return string
     */
    protected static function appendEnd(string $text): string
    {
        return preg_replace("/([ ,-:;\x{2013}\x{2014}]+$)/us", '', $text).'.';
    }
}
