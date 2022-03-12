<?php
declare(strict_types=1);

namespace Faker\Test\Calculator;

use Faker\Calculator\Ean;
use PHPUnit\Framework\TestCase;

final class EanTest extends TestCase
{

    /**
     * @return string[][]
     */
    public function Ean8checksumProvider(): array
    {
        return [
            ['1234567', '0'],
            ['2345678', '5'],
            ['3456789', '0'],
        ];
    }

    /**
     * @return array[]
     */
    public function ean8ValidationProvider(): array
    {
        return [
            ['1234567891231', true],
            ['2354698521469', true],
            ['3001092650834', false],
            ['3921092190838', false],
        ];
    }

    /**
     * @dataProvider Ean8checksumProvider
     * @param string $partial
     * @param string $checksum
     */
    public function testChecksumEan8(string $partial, string $checksum): void
    {
        $this->assertEquals($checksum, Ean::checksum($partial));
    }

    /**
     * @dataProvider ean8ValidationProvider
     * @param string $ean8
     * @param bool $valid
     */
    public function testEan8Validation(string $ean8, bool $valid): void
    {
        $this->assertTrue(Ean::isValid($ean8) === $valid);
    }

    /**
     * @return string[][]
     */
    public function Ean13checksumProvider(): array
    {
        return [
            ['123456789123', '1'],
            ['978020137962', '4'],
            ['235469852146', '9'],
            ['300109265083', '5'],
            ['392109219083', '7'],
        ];
    }

    /**
     * @return array[]
     */
    public function ean13ValidationProvider(): array
    {
        return [
            ['1234567891231', true],
            ['2354698521469', true],
            ['3001092650834', false],
            ['3921092190838', false],
        ];
    }

    /**
     * @dataProvider Ean13checksumProvider
     * @param string $partial
     * @param string $checksum
     */
    public function testChecksumEan13(string $partial, string $checksum): void
    {
        $this->assertEquals($checksum, Ean::checksum($partial));
    }

    /**
     * @dataProvider ean13ValidationProvider
     * @param string $ean13
     * @param bool $valid
     */
    public function testEan13Validation(string $ean13, bool $valid): void
    {
        $this->assertTrue(Ean::isValid($ean13) === $valid);
    }
}