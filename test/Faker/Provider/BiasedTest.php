<?php
namespace Faker\Test\Provider;

use Faker\Provider\Biased;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class BiasedTest extends TestCase
{
    const MAX = 10;
    const NUMBERS = 25000;
    protected $generator;
    protected $results = array();

    protected function setUp(): void
    {
        $this->generator = new Generator();
        $this->generator->addProvider(new Biased($this->generator));

        $this->results = array_fill(1, self::MAX, 0);
    }

    public function performFake($function)
    {
        for($i = 0; $i < self::NUMBERS; $i++) {
            $this->results[$this->generator->biasedNumberBetween(1, self::MAX, $function)]++;
        }
    }

    public function testUnbiased()
    {
        $this->performFake(array('\Faker\Provider\Biased', 'unbiased'));

        // assert that all numbers are near the expected unbiased value
        foreach ($this->results as $number => $amount) {
            // integral
            $assumed = (1 / self::MAX * $number) - (1 / self::MAX * ($number - 1));
            // calculate the fraction of the whole area
            $assumed /= 1;
            $this->assertGreaterThan(self::NUMBERS * $assumed * .95, $amount, "Value was more than 5 percent under the expected value");
            $this->assertLessThan(self::NUMBERS * $assumed * 1.05, $amount, "Value was more than 5 percent over the expected value");
        }
    }
}
