<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);
 
namespace Tobento\Service\Dater;

//use Tobento\Service\Macro\Macroable;
use DateTimeInterface;
use DateTimeImmutable;
use DateTime;
use DateTimeZone;
use DateInterval;
use IntlDateFormatter;
use Throwable;
use Exception;

/**
 * DateFormatter
 */
class DateFormatter
{    
    /**
     * @var DateTimeZone
     */    
    protected DateTimeZone $dateTimeZone;
    
    /**
     * Create a new DateFormatter.
     *
     * @param string $locale The default locale such as 'de_DE'.
     * @param string $dateFormat The default date format such as 'EEEE, dd. MMMM yyyy'.
     * @param string $dateTimeFormat The default date time format such as 'EEEE, dd. MMMM yyyy, HH:mm'.
     * @param null|string|DateTimeZone $timezone The default date time zone.
     * @param bool $mutable If to use mutable or immutable DateTime objects.
     */    
    public function __construct(
        protected string $locale = 'en_US',
        protected string $dateFormat = 'EEEE, dd. MMMM yyyy',
        protected string $dateTimeFormat = 'EEEE, dd. MMMM yyyy, HH:mm',
        null|string|DateTimeZone $timezone = null,
        protected bool $mutable = false,
    ){
        $timezone = $timezone ?: date_default_timezone_get();
            
        $this->dateTimeZone = is_string($timezone) ? $this->getTimezone($timezone) : $timezone;
    }

    /**
     * Returns an instance with the locale.
     *
     * @param string $locale The locale such as 'de_DE' or 'de-DE'
     * @return static
     */    
    public function withLocale(string $locale): static
    {
        $new = clone $this;
        $new->locale = $locale;
        return $new;
    }
    
    /**
     * Get the locale such as de_DE.
     *
     * @param string $delimiter The delimiter such as '_', '-'.
     * @return string The locale.
     */        
    public function getLocale(string $delimiter = '_'): string
    {
        $search  = ['_', '-'];
        return str_replace($search, $delimiter, $this->locale);
    }
    
    /**
     * Returns an instance with the date format.
     *
     * @param string $format Such as 'EEEE, dd. MMMM yyyy'
     * @return static
     */    
    public function withDateFormat(string $format): static
    {
        $new = clone $this;
        $new->dateFormat = $format;
        return $new;
    }

    /**
     * Get the date format.
     *
     * @return string Such as 'EEEE, dd. MMMM yyyy'
     */    
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * Returns an instance with the date time format.
     *
     * @param string $format Such as 'EEEE, dd. MMMM yyyy, HH:mm'
     * @return static
     */    
    public function withDateTimeFormat(string $format): static
    {
        $new = clone $this;
        $new->dateTimeFormat = $format;
        return $new;
    }

    /**
     * Get the date time format.
     *
     * @return string Such as 'EEEE, dd. MMMM yyyy, HH:mm'
     */    
    public function getDateTimeFormat(): string
    {
        return $this->dateTimeFormat;
    }        
        
    /**
     * Returns an instance with the date time zone.
     *
     * @param string|DateTimeZone $timezone The time zone such as 'Europe/Berlin' or DateTimeZone
     * @return static
     */    
    public function withTimezone(string|DateTimeZone $timezone): static
    {
        $new = clone $this;
        $new->dateTimeZone = is_string($timezone) ? $this->getTimezone($timezone) : $timezone;
        return $new;
    }

    /**
     * Get the DateTimeZone.
     *
     * @param null|string $timezone The time zone such as 'Europe/Berlin' or null to get the default set.
     * @return DateTimeZone
     */    
    public function getTimezone(?string $timezone = null): DateTimeZone
    {
        if (is_null($timezone))
        {
            return $this->dateTimeZone;
        }

        try {
            return new DateTimeZone($timezone);
        } 
        catch (Throwable $t)
        {
            return $this->dateTimeZone ?: new DateTimeZone('Europe/Berlin');
        }
    }
    
    /**
     * Returns an instance with the new mutable setting.
     *
     * @param bool $mutable If to use mutable objects.
     * @return static
     */    
    public function withMutable(bool $mutable = true): static
    {
        $new = clone $this;
        $new->mutable = $mutable;
        return $new;
    }    

    /**
     * Intl. format date.
     *
     * @param mixed $value The value.
     * @param null|string $format The format such as 'EEEE, dd. MMMM yyyy, HH:mm'. Null uses default pattern set.
     *                            See: http://userguide.icu-project.org/formatparse/datetime
     * @param null|string $locale The locale such as de_DE or null to use default set.
     * @return string The formatted date.
     */
    public function date(mixed $value, ?string $format = null, ?string $locale = null): string
    {
        return $this->formatDateTime($value, $locale, false, $format);
    }

    /**
     * Intl. format date time.
     *
     * @param mixed $value The value. See: http://php.net/manual/de/intldateformatter.format.php
     * @param null|string $format The format such as 'EEEE, dd. MMMM yyyy, HH:mm'. Null uses default pattern set.
     *                            See: http://userguide.icu-project.org/formatparse/datetime
     * @param null|string $locale The locale such as de_DE or null to use default set.
     * @return string The formatted date.
     */
    public function dateTime(mixed $value, ?string $format = null, ?string $locale = null): string
    {
        return $this->formatDateTime($value, $locale, true, $format);
    }
    
    /**
     * Convert value to a DateTime or DateTimeImmutable object.
     *
     * @param mixed $value The value.
     * @param null|string $timezone The time zone such as 'Europe/Berlin' or null to use the default.
     *                              If a DateTime object is passed, time zone is kept untouched.
     * @param null|string $fallback The fallback to be returned on failure or null if no fallback.
     * @param null|string $valueFormat The value format such as 'Y-m-d H:i:s'. If set it verifies the date.
     *                                 Because '2019-02-30' would return '2019-03-02'
     * @return null|DateTimeInterface Null on failure, otherwise DateTime
     */
    public function toDateTime(
        mixed $value,
        ?string $timezone = null,
        ?string $fallback = 'now',
        ?string $valueFormat = null
    ): ?DateTimeInterface {

        if ($value instanceof DateTimeImmutable)
        {            
            if ($this->mutable)
            {
                return DateTime::createFromImmutable($value);
            }

            return $value;
        }
        
        if ($value instanceof DateTime)
        {            
            if (! $this->mutable)
            {                
                return DateTimeImmutable::createFromMutable($value);
            }
            
            return $value;
        }
                
        try {
            
            $value = is_string($value) ? $value : 'now';
            
            if ($this->mutable) {
                $d = new DateTime($value, $this->getTimezone($timezone));  
            } else {
                $d = new DateTimeImmutable($value, $this->getTimezone($timezone));    
            }
            
            if ($valueFormat === null)
            {
                return $d;
            } else {
                
                if ($d->format($valueFormat) === $value)
                {
                    return $d;
                }
                
                // just throw exception as to call catch.
                throw new Exception('Invalid date!');
            }
            
        } 
        catch (Throwable $t)
        {
            if ($fallback === null)
            {
                return null;
            }
 
            if ($this->mutable) {
                return new DateTime($fallback, $this->getTimezone($timezone));  
            } else {
                return new DateTimeImmutable($fallback, $this->getTimezone($timezone));    
            }
        }
    }

    /**
     * Returns a new instance of the extended DateTime
     *
     * @param null|string $timeZone The time zone such as 'Europe/Berlin' or null to use the default.
     * @return DaterInterface
     */
    public function now(?string $timeZone = null): DaterInterface
    {
        if ($this->mutable)
        {
            return new DaterMutable('now', $this->getTimezone($timeZone));
        }
        
        return new Dater('now', $this->getTimezone($timeZone));
    }
    
    /**
     * Convert value to a Dater object.
     *
     * @param mixed $value The value.
     * @param null|string $timezone The time zone such as 'Europe/Berlin' or null to use the default.
     *                              If a DateTime object is passed, time zone is kept untouched.
     * @param null|string $fallback The fallback to be returned on failure or null if no fallback.
     * @param null|string $valueFormat The value format such as 'Y-m-d H:i:s'. If set it verifies the date.
     *                                 Because '2019-02-30' would return '2019-03-02'
     * @return null|DaterInterface Null on failure, otherwise DaterInterface
     */
    public function toDater(
        mixed $value,
        ?string $timezone = null,
        ?string $fallback = 'now',
        ?string $valueFormat = null
    ): ?DaterInterface {
      
        $date = $this->toDateTime($value, $timezone, $fallback, $valueFormat);
        
        if (is_null($date))
        {
            return null;
        }
        
        if ($date instanceof DateTime)
        { 
            return DaterMutable::createFromInterface($date);
        }
        
        return Dater::createFromInterface($date);
    }    
    
    /**
     * Format value to the given date time format.
     *
     * @param mixed $value The value.
     * @param string $format The format such as 'd.m.Y H:i'.
     * @return string The formatted date.
     */
    public function format(mixed $value, string $format = 'd.m.Y H:i'): string
    {
        return $this->toDateTime($value)->format($format);
    }    

    /**
     * If the date is in the past.
     *
     * @param mixed $date The date.
     * @param mixed $currentDate Relative calculation date.
     * @param bool $sameTimeIsPast If true and it is exactly the same time, it is past, oterwise set false.
     * @return bool True if date is in past, otherwise false.
     */
    public function inPast(mixed $date, mixed $currentDate = 'now', bool $sameTimeIsPast = false): bool
    {
        $date = $this->toDateTime($date);
        $currentDate = $this->toDateTime($currentDate);
        
        if ($sameTimeIsPast)
        {
            return $date <= $currentDate;
        } else {
            return $date < $currentDate;
        }
    }

    /**
     * If the dates are beteween the current date.
     *
     * @param mixed $dateFrom The date from.
     * @param mixed $dateTo The date to.
     * @param mixed $currentDate Relative calculation date.
     * @param bool $yearly If to use yearly independent calculation.
     * @return bool True if in between, otherwise false.
     */
    public function inBetween(
        mixed $dateFrom,
        mixed $dateTo,
        mixed $currentDate = 'now',
        bool $yearly = false
    ): bool {
        $currentDate = $this->toDateTime($currentDate);
        
        if ($yearly) {
            
            $dateFrom = $this->toDateTime($dateFrom);
            $dateTo = $this->toDateTime($dateTo);

            // check if we crossing years.
            $years = (int)$dateTo->format('Y') - (int)$dateFrom->format('Y');
            
            if ($years !== 0) {
                // crossing years
                $dateFrom = $this->toCurrentYear($dateFrom, $currentDate);
                $dateTo = $this->toCurrentYear($dateTo, $currentDate);
                
                $firstDateOfYear = $this->toDateTime(clone $currentDate)
                                        ->modify('january')
                                        ->modify('first day of this month')
                                        ->modify('midnight');
                
                $lastDateOfYear = $this->toDateTime(clone $currentDate)
                                       ->modify('december')
                                       ->modify('midnight')
                                       ->modify('-1 minutes')
                                       ->modify('last day of this month');                
            
                if (
                    $this->inBetween($dateFrom, $lastDateOfYear, $currentDate)
                    || $this->inBetween($firstDateOfYear, $dateTo, $currentDate)
                ) {
                    return true;
                }
                
                return false;

            } else {
                $dateFrom = $this->toCurrentYear($dateFrom, $currentDate);
                $dateTo = $this->toCurrentYear($dateTo, $currentDate);
            }
        }

        if (!$this->inPast($dateFrom, $currentDate, true)) {
            return false;
        }
        
        if ($this->inPast($dateTo, $currentDate)) {
            return false;
        }        
        
        return true;
    }    

    /**
     * Difference between to dates.
     *
     * @param mixed $date The date.
     * @param mixed $currentDate Relative calculation date
     * @return DateInterval
     */
    public function diff(mixed $date, mixed $currentDate = 'now'): DateInterval
    {
        $date = $this->toDateTime($date);
        $currentDate = $this->toDateTime($currentDate);
    
        try {
            return $currentDate->diff($date);
        } 
        catch (Throwable $t)
        {
            return new DateInterval('P0M');
        }
    }
    
    /**
     * Adjust date with current year.
     *
     * @param mixed $date The date.
     * @param mixed $currentDate Relative calculation date.
     * @return DateTimeInterface
     */
    public function toCurrentYear(mixed $date, mixed $currentDate = 'now'): DateTimeInterface
    {        
        $date = $this->toDateTime($date);
        $currentYear = $this->toDateTime($currentDate)->format('Y');
        
        return $this->toDateTime($date)->setDate(
            (int)$currentYear,
            (int)$date->format('m'),
            (int)$date->format('d')
        );
    }    

    /**
     * Convert month name to its number.
     *
     * @param mixed $month The month such as 'Jan', 'January', 'JAN', 'JANUARY', 'january', 'jan'
     * @return null|int The number 1-12, or null if not valid.
     */
    public function toMonthNumber(mixed $month): ?int
    {
        if (!is_string($month))
        {
            return null;
        }
        
        $month = strtolower(trim($month));
        
        $months = [
            'january' => 1, 'jan' => 1,
            'february' => 2, 'feb' => 2,
            'march' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'may' => 5,
            'june' => 6, 'jun' => 6,
            'july' => 7, 'jul' => 7,
            'august' => 8, 'aug' => 8,
            'september' => 9, 'sep' => 9,
            'october' => 10, 'oct' => 10,
            'november' => 11, 'nov' => 11,
            'december' => 12, 'dec' => 12
        ];
        
        return array_key_exists($month, $months) ? $months[$month] : null;
    }

    /**
     * Convert month name to its number as string.
     *
     * @param mixed $month The month such as 'Jan', 'January', 'JAN', 'JANUARY', 'january', 'jan'
     * @return null|string The number from '01' to '12', or null if not valid.
     */
    public function toMonthNumberString(mixed $month): ?string
    {
        $number = $this->toMonthNumber($month);
        return $number === null ? null : str_pad((string)$number, 2, '0', STR_PAD_LEFT);
    }    

    /**
     * Convert month number to its name internationally.
     *
     * @param mixed $number The month number such as '01', 1, ...
     * @param string $pattern The pattern. 'M' = '1', 'MM' = '09', 'MMM' = 'Jan', 'MMMM' = 'January'
     * @param null|string $locale The locale such as de_DE or null to use default set.
     * @return null|string The name such as 'January', or null if not valid.
     */
    public function toMonth(mixed $number, string $pattern = 'MMMM', ?string $locale = null): ?string
    {
        if (!is_numeric($number))
        {
            return null;
        }
        
        $number = (int) $number;
        
        if ($number > 12 || $number < 1)
        {
            return null;
        }
        
        $number = '2000-'.$number;
        
        $locale = $locale ?: $this->getLocale();
        
        if (!in_array($pattern, ['M', 'MM', 'MMM', 'MMMM']))
        {
            $pattern = 'MMMM';
        }
        
        $fmt = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            $this->getTimezone()->getName(),
            IntlDateFormatter::GREGORIAN,
            $pattern
        );

        try {
            $month = $fmt->format($this->toDateTime($number));
            return $pattern === 'MMM' ? rtrim($month, '.') : $month;
        } 
        catch (Throwable $t)
        {
            return null;
        }
    }

    /**
     * Convert weekday name to its number.
     *
     * @param mixed $weekday The weekday such as 'Tuesday', 'Tue', 'Tu' (lower- and uppercase)
     * @return null|int The number (1 - 7)
     */
    public function toWeekdayNumber(mixed $weekday): ?int
    {
        if (is_numeric($weekday))
        {
            $weekday = (int) $weekday;

            return ($weekday >= 0 && $weekday <= 7) ? $weekday : null;
        }
            
        if (!is_string($weekday))
        {
            return null;
        }
        
        $weekday = strtolower(trim($weekday));
        
        $weekdays = [
            'monday' => 1, 'mon' => 1, 'mo' => 1,
            'tuesday' => 2, 'tue' => 2, 'tu' => 2,
            'wedesday' => 3, 'wed' => 3, 'we' => 3,
            'thursday' => 4, 'thu' => 4, 'th' => 4,
            'friday' => 5, 'fri' => 5, 'fr' => 5,
            'saturday' => 6, 'sat' => 6, 'sa' => 6,
            'sunday' => 7, 'sun' => 7, 'su' => 7
        ];
        
        return array_key_exists($weekday, $weekdays) ? $weekdays[$weekday] : null;
    }

    /**
     * Convert weekday number to its internationally name.
     *
     * @param mixed $number Day of week (0 - 7) (Sunday=0 or 7)
     * @param string $pattern The pattern. E, EE, or EEE = 'Tue', 'EEEE' = 'Tuesday', 'EEEEE' = 'T', 'EEEEEE' = 'Tu'
     * @param null|string $locale The locale such as de_DE or null to use default set.
     * @return null|string The name such as 'January', or null if not valid.
     */
    public function toWeekday(mixed $number, string $pattern = 'EEEE', ?string $locale = null): ?string
    {
        if (!is_int($number))
        {
            return null;
        }
        
        if ($number > 7 || $number < 0)
        {
            return null;
        }
        
        $number = '4.1.1970 +'.$number.' Days';
        
        $locale = $locale ?: $this->getLocale();
        
        if (!in_array($pattern, ['E', 'EE', 'EEE', 'EEEE', 'EEEEE']))
        {
            $pattern = 'EEEE';
        }
        
        $fmt = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            $this->getTimezone()->getName(),
            IntlDateFormatter::GREGORIAN,
            $pattern
        );
        
        try {
            $weekday = $fmt->format($this->toDateTime($number));
            return rtrim($weekday, '.');
        } 
        catch (Throwable $t)
        {
            return null;
        }
    }    
                
    /**
     * Format date time.
     *
     * @param mixed $value The value. See: http://php.net/manual/de/intldateformatter.format.php
     * @param null|string $locale The locale such as de_DE or null to use default set.
     * @param bool $withTime True with time, otherwise false.
     * @param null|string $pattern The format such as 'EEEE, dd. MMMM yyyy, HH:mm'. Null uses default pattern set.
     * @return string The formatted date time.
     */
    protected function formatDateTime(
        mixed $value,
        ?string $locale = null,
        bool $withTime = false,
        ?string $pattern = null
    ): string {
        $locale = $locale ?: $this->getLocale();
        
        if ($withTime === true) {
            $timetype = IntlDateFormatter::FULL;
            $pattern = $pattern ?: $this->getDateTimeFormat();
        } else {
            $timetype = IntlDateFormatter::NONE;
            $pattern = $pattern ?: $this->getDateFormat();
        }
        
        $fmt = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::FULL,
            $timetype,
            $this->getTimezone()->getName(),
            IntlDateFormatter::GREGORIAN,
            $pattern
        );
        
        try {
            return $fmt->format($this->toDateTime($value));
        } 
        catch (Throwable $t)
        {
            return $fmt->format(time());
        }
    }
}