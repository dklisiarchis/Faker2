<?php
declare(strict_types=1);

namespace Faker\Test\Provider;

use Faker\Provider\en_US\Text;
use Faker\Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TextTest extends TestCase
{
    /**
     * @var Generator
     */
    private Generator $generator;

    /**
     * @before
     * @return void
     */
    public function buildGenerator(): void
    {
        $generator = new Generator();
        $generator->addProvider(new Text($generator));
        $generator->seed(0);

        $this->generator = $generator;
    }

    /**
     * @testWith [10]
     *           [20]
     *           [50]
     *           [70]
     *           [90]
     *           [120]
     *           [150]
     *           [200]
     *           [500]
     */
    public function testTextMaxLength(int $length): void
    {
        $this->assertLessThanOrEqual($length, strlen($this->generator->realText($length)));
    }

    /**
     * @return void
     */
    public function testTextMaxIndex(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('indexSize must be at most 5');
        $this->generator->realText(200, 11);
    }

    /**
     * @return void
     */
    public function testTextMinIndex(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('indexSize must be at least 1');
        $this->generator->realText(200, 0);
    }

    /**
     * @return void
     */
    public function testTextMinLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('maxNbChars must be at least 10');
        $this->generator->realText(9);
    }
}
