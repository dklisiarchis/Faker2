<?php
declare(strict_types=1);

namespace Faker\Provider;

use DateInterval;
use DateTime as Dt;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use InvalidArgumentException;

use function is_numeric;
use function strtotime;
use function mt_rand;
use function min;
use function date_default_timezone_get;

class DateTime extends Base
{

    /**
     * @var string[]
     */
    protected static array $century = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII','XIII','XIV','XV','XVI','XVII','XVIII','XIX','XX','XXI'];

    /**
     * @var string|null
     */
    protected static ?string $defaultTimezone = null;

    /**
     * @param  Dt|string|float|int $max
     * @return int|false
     */
    protected static function getMaxTimestamp(Dt|string|float|int $max = 'now'): int|false
    {
        if (is_numeric($max)) {
            return (int) $max;
        }

        if ($max instanceof Dt) {
            return $max->getTimestamp();
        }

        return strtotime(empty($max) ? 'now' : $max);
    }

    /**
     * Get a timestamp between January 1, 1970 and now
     *
     * @param  Dt|int|string $max maximum timestamp used as random end limit, default to "now"
     * @return int
     *
     * @example 1061306726
     */
    public static function unixTime(Dt|int|string $max = 'now'): int
    {
        return mt_rand(0, static::getMaxTimestamp($max));
    }

    /**
     * Get a datetime object for a date between January 1, 1970 and now
     *
     * @param   Dt|int|string $max      maximum timestamp used as random end limit, default to "now"
     * @param   string|null   $timezone time zone in which the date time should be set, default to DateTime::$defaultTimezone, if set, otherwise the result of `date_default_timezone_get`
     * @return  Dt
     * @throws  Exception
     * @see     http://php.net/manual/en/timezones.php
     * @see     http://php.net/manual/en/function.date-default-timezone-get.php
     * @example DateTime('2005-08-16 20:39:21')
     */
    public static function dateTime(Dt|int|string $max = 'now', ?string $timezone = null): Dt
    {
        return static::setTimezone(
            new Dt('@' . static::unixTime($max)),
            $timezone
        );
    }

    /**
     * Get a datetime object for a date between January 1, 001 and now
     *
     * @param   Dt|int|string $max      maximum timestamp used as random end limit, default to "now"
     * @param   string|null   $timezone time zone in which the date time should be set, default to DateTime::$defaultTimezone, if set, otherwise the result of `date_default_timezone_get`
     * @return  Dt
     * @throws  Exception
     * @see     http://php.net/manual/en/timezones.php
     * @see     http://php.net/manual/en/function.date-default-timezone-get.php
     * @example DateTime('1265-03-22 21:15:52')
     */
    public static function dateTimeAD(Dt|int|string $max = 'now', ?string $timezone = null): Dt
    {
        $min = (PHP_INT_SIZE > 4 ? -62135597361 : -PHP_INT_MAX);
        return static::setTimezone(
            new Dt('@' . mt_rand($min, static::getMaxTimestamp($max))),
            $timezone
        );
    }

    /**
     * get a date string formatted with ISO8601
     *
     * @param   Dt|int|string $max maximum timestamp used as random end limit, default to "now"
     * @return  string
     * @throws  Exception
     * @example '2003-10-21T16:05:52+0000'
     */
    public static function iso8601(Dt|int|string $max = 'now'): string
    {
        return static::date(DateTimeInterface::ISO8601, $max);
    }

    /**
     * Get a date string between January 1, 1970 and now
     *
     * @param   string        $format
     * @param   Dt|int|string $max    maximum timestamp used as random end limit, default to "now"
     * @return  string
     * @throws  Exception
     * @example '2008-11-27'
     */
    public static function date(string $format = 'Y-m-d', Dt|int|string $max = 'now'): string
    {
        return static::dateTime($max)->format($format);
    }

    /**
     * Get a time string (24h format by default)
     *
     * @param   string        $format
     * @param   Dt|int|string $max    maximum timestamp used as random end limit, default to "now"
     * @return  string
     * @throws  Exception
     * @example '15:02:34'
     */
    public static function time(string $format = 'H:i:s', Dt|int|string $max = 'now'): string
    {
        return static::dateTime($max)->format($format);
    }

    /**
     * Get a DateTime object based on a random date between two given dates.
     * Accepts date strings that can be recognized by strtotime().
     *
     * @param   Dt|string   $startDate Defaults to 30 years ago
     * @param   Dt|string   $endDate   Defaults to "now"
     * @param   string|null $timezone  time zone in which the date time should be set, default to DateTime::$defaultTimezone, if set, otherwise the result of `date_default_timezone_get`
     * @return  Dt
     * @throws  Exception
     * @see     http://php.net/manual/en/timezones.php
     * @see     http://php.net/manual/en/function.date-default-timezone-get.php
     * @example DateTime('1999-02-02 11:42:52')
     */
    public static function dateTimeBetween(
        Dt|string $startDate = '-30 years',
        Dt|string $endDate = 'now',
        ?string $timezone = null
    ): Dt {
        $startTimestamp = $startDate instanceof Dt ? $startDate->getTimestamp() : strtotime($startDate);
        $endTimestamp = static::getMaxTimestamp($endDate);

        if ($startTimestamp > $endTimestamp) {
            throw new InvalidArgumentException('Start date must be anterior to end date.');
        }

        $timestamp = mt_rand($startTimestamp, $endTimestamp);

        return static::setTimezone(
            new Dt('@' . $timestamp),
            $timezone
        );
    }

    /**
     * Get a DateTime object based on a random date between one given date and
     * an interval
     * Accepts date string that can be recognized by strtotime().
     *
     * @param   Dt|string   $date     Defaults to 30 years ago
     * @param   string      $interval Defaults to 5 days after
     * @param   string|null $timezone time zone in which the date time should be set, default to DateTime::$defaultTimezone, if set, otherwise the result of `date_default_timezone_get`
     * @return  Dt
     * @throws  Exception
     * @see     http://php.net/manual/en/timezones.php
     * @see     http://php.net/manual/en/function.date-default-timezone-get.php
     * @example dateTimeInInterval('1999-02-02 11:42:52', '+ 5 days')
     */
    public static function dateTimeInInterval(
        Dt|string $date = '-30 years',
        string $interval = '+5 days',
        ?string $timezone = null
    ): Dt {
        $intervalObject = DateInterval::createFromDateString($interval);
        $datetime       = $date instanceof Dt ? $date : new Dt($date);
        $otherDatetime  = clone $datetime;
        $otherDatetime->add($intervalObject);

        $begin = min($datetime, $otherDatetime);
        $end = $datetime === $begin ? $otherDatetime : $datetime;

        return static::dateTimeBetween(
            $begin,
            $end,
            $timezone
        );
    }

    /**
     * @param   Dt|int|string $max      maximum timestamp used as random end limit, default to "now"
     * @param   string|null   $timezone time zone in which the date time should be set, default to DateTime::$defaultTimezone, if set, otherwise the result of `date_default_timezone_get`
     * @return  Dt
     * @throws  Exception
     * @example DateTime('1964-04-04 11:02:02')
     */
    public static function dateTimeThisCentury(Dt|int|string $max = 'now', ?string $timezone = null): Dt
    {
        return static::dateTimeBetween('-100 year', $max, $timezone);
    }

    /**
     * @param   Dt|int|string $max      maximum timestamp used as random end limit, default to "now"
     * @param   string|null   $timezone time zone in which the date time should be set, default to DateTime::$defaultTimezone, if set, otherwise the result of `date_default_timezone_get`
     * @return  Dt
     * @throws  Exception
     * @example DateTime('2010-03-10 05:18:58')
     */
    public static function dateTimeThisDecade(Dt|int|string $max = 'now', ?string $timezone = null): Dt
    {
        return static::dateTimeBetween('-10 year', $max, $timezone);
    }

    /**
     * @param   Dt|int|string $max      maximum timestamp used as random end limit, default to "now"
     * @param   string|null   $timezone time zone in which the date time should be set, default to DateTime::$defaultTimezone, if set, otherwise the result of `date_default_timezone_get`
     * @return  Dt
     * @throws  Exception
     * @example DateTime('2011-09-19 09:24:37')
     */
    public static function dateTimeThisYear(Dt|int|string $max = 'now', ?string $timezone = null): Dt
    {
        return static::dateTimeBetween('first day of january this year', $max, $timezone);
    }

    /**
     * @param   Dt|int|string $max      maximum timestamp used as random end limit, default to "now"
     * @param   string|null   $timezone time zone in which the date time should be set, default to DateTime::$defaultTimezone, if set, otherwise the result of `date_default_timezone_get`
     * @return  Dt
     * @throws  Exception
     * @example DateTime('2011-10-05 12:51:46')
     */
    public static function dateTimeThisMonth(Dt|int|string $max = 'now', ?string $timezone = null): Dt
    {
        return static::dateTimeBetween('-1 month', $max, $timezone);
    }

    /**
     * @param   Dt|int|string $max maximum timestamp used as random end limit, default to "now"
     * @return  string
     * @throws  Exception
     * @example 'am'
     */
    public static function amPm(Dt|int|string $max = 'now'): string
    {
        return static::dateTime($max)->format('a');
    }

    /**
     * @param   Dt|int|string $max maximum timestamp used as random end limit, default to "now"
     * @return  string
     * @throws  Exception
     * @example '22'
     */
    public static function dayOfMonth(Dt|int|string  $max = 'now'): string
    {
        return static::dateTime($max)->format('d');
    }

    /**
     * @param   Dt|int|string $max maximum timestamp used as random end limit, default to "now"
     * @return  string
     * @throws  Exception
     * @example 'Tuesday'
     */
    public static function dayOfWeek(Dt|int|string  $max = 'now'): string
    {
        return static::dateTime($max)->format('l');
    }

    /**
     * @param   Dt|int|string $max maximum timestamp used as random end limit, default to "now"
     * @return  string
     * @throws  Exception
     * @example '7'
     */
    public static function month(Dt|int|string $max = 'now'): string
    {
        return static::dateTime($max)->format('m');
    }

    /**
     * @param   Dt|int|string $max maximum timestamp used as random end limit, default to "now"
     * @return  string
     * @throws  Exception
     * @example 'September'
     */
    public static function monthName(Dt|int|string $max = 'now'): string
    {
        return static::dateTime($max)->format('F');
    }

    /**
     * @param   Dt|int|string $max maximum timestamp used as random end limit, default to "now"
     * @return  string
     * @throws  Exception
     * @example '1673'
     */
    public static function year(Dt|int|string $max = 'now'): string
    {
        return static::dateTime($max)->format('Y');
    }

    /**
     * @return  string
     * @example 'XVII'
     */
    public static function century(): string
    {
        return static::randomElement(static::$century);
    }

    /**
     * @return  string
     * @example 'Europe/Paris'
     */
    public static function timezone(): string
    {
        return static::randomElement(DateTimeZone::listIdentifiers());
    }

    /**
     * Internal method to set the time zone on a DateTime.
     *
     * @param Dt          $dt
     * @param string|null $timezone
     *
     * @return Dt
     */
    private static function setTimezone(Dt $dt, ?string $timezone = null): Dt
    {
        return $dt->setTimezone(new DateTimeZone(static::resolveTimezone($timezone)));
    }

    /**
     * Sets default time zone.
     *
     * @param string|null $timezone
     *
     * @return void
     */
    public static function setDefaultTimezone(?string $timezone = null): void
    {
        static::$defaultTimezone = $timezone;
    }

    /**
     * Gets default time zone.
     *
     * @return string|null
     */
    public static function getDefaultTimezone(): ?string
    {
        return static::$defaultTimezone;
    }

    /**
     * @param  string|null $timezone
     * @return null|string
     */
    private static function resolveTimezone(?string $timezone = null): ?string
    {
        return ((null === $timezone) ? ((null === static::$defaultTimezone) ? date_default_timezone_get() : static::$defaultTimezone) : $timezone);
    }
}
