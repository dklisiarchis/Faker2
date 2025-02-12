<?php
declare(strict_types=1);

namespace Faker\Provider;

use function mt_rand;
use function date;
use function time;
use function strtotime;

class UserAgent extends Base
{

    /**
     * @var string[]
     */
    protected static array $userAgents = ['firefox', 'chrome', 'internetExplorer', 'opera', 'safari'];

    /**
     * @var string[]
     */
    protected static array $windowsPlatformTokens = [
        'Windows NT 6.2', 'Windows NT 6.1', 'Windows NT 6.0', 'Windows NT 5.2', 'Windows NT 5.1',
        'Windows NT 5.01', 'Windows NT 5.0', 'Windows NT 4.0', 'Windows 98; Win 9x 4.90', 'Windows 98',
        'Windows 95', 'Windows CE'
    ];

    /**
     * Possible processors on Linux
     *
     * @var string[]
     */
    protected static array $linuxProcessor = ['i686', 'x86_64'];

    /**
     * Mac processors (it also added U;)
     *
     * @var string[]
     */
    protected static array $macProcessor = ['Intel', 'PPC', 'U; Intel', 'U; PPC'];

    /**
     * Add as many languages as you like.
     *
     * @var string[]
     */
    protected static array $lang = ['en-US', 'sl-SI'];

    /**
     * Generate mac processor
     *
     * @return string
     */
    public static function macProcessor(): string
    {
        return static::randomElement(static::$macProcessor);
    }

    /**
     * Generate linux processor
     *
     * @return string
     */
    public static function linuxProcessor(): string
    {
        return static::randomElement(static::$linuxProcessor);
    }

    /**
     * Generate a random user agent
     *
     * @example 'Mozilla/5.0 (Windows CE) AppleWebKit/5350 (KHTML, like Gecko) Chrome/13.0.888.0 Safari/5350'
     * @return  string
     */
    public static function userAgent(): string
    {
        $userAgentName = static::randomElement(static::$userAgents);

        return static::$userAgentName();
    }

    /**
     * Generate Chrome user agent
     *
     * @example 'Mozilla/5.0 (Macintosh; PPC Mac OS X 10_6_5) AppleWebKit/5312 (KHTML, like Gecko) Chrome/14.0.894.0 Safari/5312'
     * @return  string
     */
    public static function chrome(): string
    {
        $saf = mt_rand(531, 536) . mt_rand(0, 2);

        $platforms = [
            '(' . static::linuxPlatformToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/" . mt_rand(36, 40) . '.0.' . mt_rand(800, 899) . ".0 Mobile Safari/$saf",
            '(' . static::windowsPlatformToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/" . mt_rand(36, 40) . '.0.' . mt_rand(800, 899) . ".0 Mobile Safari/$saf",
            '(' . static::macPlatformToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/" . mt_rand(36, 40) . '.0.' . mt_rand(800, 899) . ".0 Mobile Safari/$saf"
        ];

        return 'Mozilla/5.0 ' . static::randomElement($platforms);
    }

    /**
     * Generate Firefox user agent
     *
     * @example 'Mozilla/5.0 (X11; Linuxi686; rv:7.0) Gecko/20101231 Firefox/3.6'
     * @return  string
     */
    public static function firefox(): string
    {
        $ver = 'Gecko/' . date('Ymd', mt_rand(strtotime('2010-1-1'), time())) . ' Firefox/' . mt_rand(35, 37) . '.0';

        $platforms = [
            '(' . static::windowsPlatformToken() . '; ' . static::randomElement(static::$lang) . '; rv:1.9.' . mt_rand(0, 2) . '.20) ' . $ver,
            '(' . static::linuxPlatformToken() . '; rv:' . mt_rand(5, 7) . '.0) ' . $ver,
            '(' . static::macPlatformToken() . ' rv:' . mt_rand(2, 6) . '.0) ' . $ver
        ];

        return "Mozilla/5.0 " . static::randomElement($platforms);
    }

    /**
     * Generate Safari user agent
     *
     * @example 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_7_1 rv:3.0; en-US) AppleWebKit/534.11.3 (KHTML, like Gecko) Version/4.0 Safari/534.11.3'
     * @return  string
     */
    public static function safari(): string
    {
        $saf = mt_rand(531, 535) . '.' . mt_rand(1, 50) . '.' . mt_rand(1, 7);
        if (mt_rand(0, 1) == 0) {
            $ver = mt_rand(4, 5) . '.' . mt_rand(0, 1);
        } else {
            $ver = mt_rand(4, 5) . '.0.' . mt_rand(1, 5);
        }

        $mobileDevices = [
            'iPhone; CPU iPhone OS',
            'iPad; CPU OS'
        ];

        $platforms = [
            '(Windows; U; ' . static::windowsPlatformToken() . ") AppleWebKit/$saf (KHTML, like Gecko) Version/$ver Safari/$saf",
            '(' . static::macPlatformToken() . ' rv:' . mt_rand(2, 6) . '.0; ' . static::randomElement(static::$lang) . ") AppleWebKit/$saf (KHTML, like Gecko) Version/$ver Safari/$saf",
            '(' . static::randomElement($mobileDevices) . ' ' . mt_rand(7, 8) . '_' . mt_rand(0, 2) . '_' . mt_rand(1, 2) . ' like Mac OS X; ' . static::randomElement(static::$lang) . ") AppleWebKit/$saf (KHTML, like Gecko) Version/" . mt_rand(3, 4) . ".0.5 Mobile/8B" . mt_rand(111, 119) . " Safari/6$saf",
        ];

        return "Mozilla/5.0 " . static::randomElement($platforms);
    }

    /**
     * Generate Opera user agent
     *
     * @example 'Opera/8.25 (Windows NT 5.1; en-US) Presto/2.9.188 Version/10.00'
     * @return  string
     */
    public static function opera(): string
    {
        $platforms = [
            '(' . static::linuxPlatformToken() . '; ' . static::randomElement(static::$lang) . ') Presto/2.' . mt_rand(8, 12) . '.' . mt_rand(160, 355) . ' Version/' . mt_rand(10, 12) . '.00',
            '(' . static::windowsPlatformToken() . '; ' . static::randomElement(static::$lang) . ') Presto/2.' . mt_rand(8, 12) . '.' . mt_rand(160, 355) . ' Version/' . mt_rand(10, 12) . '.00'
        ];

        return "Opera/" . mt_rand(8, 9) . '.' . mt_rand(10, 99) . ' ' . static::randomElement($platforms);
    }

    /**
     * Generate Internet Explorer user agent
     *
     * @example 'Mozilla/5.0 (compatible; MSIE 7.0; Windows 98; Win 9x 4.90; Trident/3.0)'
     * @return  string
     */
    public static function internetExplorer(): string
    {
        return 'Mozilla/5.0 (compatible; MSIE ' . mt_rand(5, 11) . '.0; ' . static::windowsPlatformToken() . '; Trident/' . mt_rand(3, 5) . '.' . mt_rand(0, 1) . ')';
    }

    /**
     * @return string
     */
    public static function windowsPlatformToken(): string
    {
        return static::randomElement(static::$windowsPlatformTokens);
    }

    /**
     * @return string
     */
    public static function macPlatformToken(): string
    {
        return 'Macintosh; ' . static::randomElement(static::$macProcessor) . ' Mac OS X 10_' . mt_rand(5, 8) . '_' . mt_rand(0, 9);
    }

    /**
     * @return string
     */
    public static function linuxPlatformToken(): string
    {
        return 'X11; Linux ' . static::randomElement(static::$linuxProcessor);
    }
}
