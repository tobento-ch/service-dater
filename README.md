# Dater Service

The Dater Service provides useful methods to format and display dates in an application.

## Table of Contents

- [Getting started](#getting-started)
    - [Requirements](#requirements)
    - [Highlights](#highlights)
    - [Simple Example](#simple-example)
- [Documentation](#documentation)
    - [Date Formatter](#date-formatter)
        - [Display Date](#date)
        - [Display Date and Time](#date-and-time)
        - [Format Date](#format-date)
        - [In Past](#in-past)
        - [In Between](#in-between)
        - [Diff](#diff)
        - [Now](#now)
        - [Convert](#convert)
            - [To DateTime](#to-datetime)
            - [To Dater](#to-dater)
            - [To Current Year](#to-current-year)
            - [To Month](#to-month)
            - [To Month Number](#to-month-number)
            - [To Month Number String](#to-month-number-string)
            - [To Weekday](#to-weekday)
            - [To Weekday Number](#to-weekday-number)
    - [Dater](#dater)
- [Credits](#credits)
___

# Getting started

Add the latest version of the dater service running this command.

```
composer require tobento/service-dater
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design

## Simple Example

Here is a simple example of how to use the Dater Service.

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

// Intl. format date.
var_dump($df->date('now'));
// string(22) "Friday, 16. April 2021"

// Intl. format date and time.
var_dump($df->dateTime('now'));
// string(29) "Friday, 16. April 2021, 15:08"

// Intl. format date and time with specific timezone and locale.
var_dump($df->withTimezone('America/Chicago')->withLocale('de')->dateTime('now'));
// string(30) "Freitag, 16. April 2021, 08:08"
```

# Documentation

## Date Formatter

```php
use Tobento\Service\Dater\DateFormatter;

// Define the default settings:
$df = new DateFormatter(
    locale: 'en_US',
    dateFormat: 'EEEE, dd. MMMM yyyy',
    dateTimeFormat: 'EEEE, dd. MMMM yyyy, HH:mm',
    timezone: 'America/Chicago',
    mutable: true,
);

// or use the methods to change default settings.
$df = $df->withLocale('en_US');
var_dump($df->getLocale());
// string(5) "en_US"

var_dump($df->getLocale(delimiter: '-'));
// string(5) "en-US"

$df = $df->withDateFormat('EE, dd. MMMM yyyy');
var_dump($df->getDateFormat());
// string(17) "EE, dd. MMMM yyyy"

$df = $df->withDateTimeFormat('EEEE, dd. MMMM yyyy, HH:mm');
var_dump($df->getDateTimeFormat());
// string(26) "EEEE, dd. MMMM yyyy, HH:mm"

$df = $df->withTimezone('America/Chicago');
var_dump($df->getTimezone());
// object(DateTimeZone)#9 (2) { ["timezone_type"]=> int(3) ["timezone"]=> string(15) "America/Chicago" }

$df = $df->withMutable(true);
```

### Display Date

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

// with default date format and locale
var_dump($df->date('now'));
// string(22) "Friday, 16. April 2021"

// with custom format and locale
var_dump($df->date(value: 'now', format: 'EE, dd. MMMM yyyy', locale: 'de_DE'));
string(19) "Fr., 16. April 2021" 
```

### Display Date and Time

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

// with default date format and locale
var_dump($df->dateTime('now'));
// string(29) "Friday, 16. April 2021, 15:33"

// with custom format and locale
var_dump($df->dateTime(value: 'now', format: 'EE, dd. MMMM yyyy, HH:mm', locale: 'de_DE'));
// string(26) "Fr., 16. April 2021, 15:33"
```

### Format Date

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

var_dump($df->format('16-06-2021'));
// string(16) "16.06.2021 00:00"

// with custom
var_dump($df->format(value: 'now', format: 'd.m.Y H:i'));
// string(16) "16.06.2021 12:59"

// if the date provided is invalid it fallsback to 'now'
var_dump($df->format('16-06-19457'));
// string(16) "16.06.2021 13:01"
```

### In Past

Determine if a given date is in the past.

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

var_dump($df->inPast(date: '12.05.2000', currentDate: '13.05.2000', sameTimeIsPast: false));
// bool(true)

var_dump($df->inPast(date: '12.05.2000', currentDate: '11.05.2000', sameTimeIsPast: false));
// bool(false)
```

### In Between

Determine if the dates are between the current date.

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

var_dump($df->inBetween(
    dateFrom: '18.05.2000',
    dateTo: '20.05.2000',
    currentDate: '19.05.2000'
));
// bool(true)

var_dump($df->inBetween(
    dateFrom: '18.05.2000',
    dateTo: '20.05.2000',
    currentDate: '21.05.2000'
));
// bool(false)

// determine yearly independent
var_dump($df->inBetween(
    dateFrom: '18.05.2000',
    dateTo: '20.05.2000',
    currentDate: '19.05.2001',
    yearly: true
));
// bool(true)
```

### Diff

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

var_dump($df->diff(date: '17.05.2000', currentDate: '18.05.2001'));
// object(DateInterval)#10 (16) { ["y"]=> int(1) ["m"]=> int(0) ["d"]=> int(1) ["h"]=> int(0) ["i"]=> int(0) ["s"]=> int(0) ["f"]=> float(0) ["weekday"]=> int(0) ["weekday_behavior"]=> int(0) ["first_last_day_of"]=> int(0) ["invert"]=> int(1) ["days"]=> int(366) ["special_type"]=> int(0) ["special_amount"]=> int(0) ["have_weekday_relative"]=> int(0) ["have_special_relative"]=> int(0) }
```

### Now

A fluent way modifying the current date.

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

// uses default timezone
$date = $df->now()->addMinutes(5)
                  ->subMinutes(3)
                  ->addDays(10)
                  ->subDays(2);

// use custom timezone
$date = $df->now('Europe/Berlin')->addMinutes(5);
```

### Convert

#### To DateTime

Convert value to a DateTime or DateTimeImmutable object.

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

var_dump($df->toDateTime('2019-06-24'));
// object(DateTimeImmutable)#8 (3) { ["date"]=> string(26) "2019-06-24 00:00:00.000000" ["timezone_type"]=> int(3) ["timezone"]=> string(13) "Europe/Berlin" }

var_dump($df->withMutable(true)->toDateTime('2019-06-24'));
// object(DateTime)#6 (3) { ["date"]=> string(26) "2019-06-24 00:00:00.000000" ["timezone_type"]=> int(3) ["timezone"]=> string(13) "Europe/Berlin" }

// with another timezone
var_dump($df->toDateTime(value: '2019-02-23', timezone: 'America/Chicago'));
// object(DateTimeImmutable)#8 (3) { ["date"]=> string(26) "2019-02-23 00:00:00.000000" ["timezone_type"]=> int(3) ["timezone"]=> string(15) "America/Chicago" }

// if an invalid date value is provided, it fallsback to 'now' as default.
var_dump($df->toDateTime(value: 'a-06-24', fallback: 'now'));
// object(DateTimeImmutable)#8 (3) { ["date"]=> string(26) "2021-06-16 14:08:34.699816" ["timezone_type"]=> int(3) ["timezone"]=> string(13) "Europe/Berlin" }

// if you do not want a fallback.
var_dump($df->toDateTime(value: 'a-06-24', fallback: null));
// NULL

// set a value format for date verification.
var_dump($df->toDateTime(value: '2019-02-30'));
// object(DateTimeImmutable)#8 (3) { ["date"]=> string(26) "2019-03-02 00:00:00.000000" ["timezone_type"]=> int(3) ["timezone"]=> string(13) "Europe/Berlin" }

// fallsback to the fallback date provided.
var_dump($df->toDateTime(value: '2019-02-30', valueFormat: 'Y-m-d'));
// object(DateTimeImmutable)#10 (3) { ["date"]=> string(26) "2021-06-16 14:12:00.043682" ["timezone_type"]=> int(3) ["timezone"]=> string(13) "Europe/Berlin" }
```

#### To Dater

Convert value to a Dater or DaterMutable object.
This has same parameters and works like the toDateTime method.

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

var_dump($df->toDater('2019-06-24'));
// object(Tobento\Service\Dater\Dater)#9 (3) { ["date"]=> string(26) "2019-06-24 00:00:00.000000" ["timezone_type"]=> int(3) ["timezone"]=> string(13) "Europe/Berlin" }
```

#### To Current Year

Converts the given date to the current date year.

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

var_dump($df->toCurrentYear('2019-06-24'));
// object(DateTimeImmutable)#9 (3) { ["date"]=> string(26) "2021-06-24 00:00:00.000000" ["timezone_type"]=> int(3) ["timezone"]=> string(13) "Europe/Berlin" }

var_dump($df->toCurrentYear(date: '2019-06-24', currentDate: '2015-06-24'));
// object(DateTimeImmutable)#9 (3) { ["date"]=> string(26) "2015-06-24 00:00:00.000000" ["timezone_type"]=> int(3) ["timezone"]=> string(13) "Europe/Berlin" }
```

#### To Month

Returns the month or null on failure.

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

var_dump($df->toMonth(5));
// string(3) "May"

var_dump($df->toMonth(number: 5, locale: 'de'));
// string(3) "Mai"

// pattern: 'M' = '1', 'MM' = '09', 'MMM' = 'Jan', 'MMMM' = 'January'
var_dump($df->toMonth(number: 5, pattern: 'MM'));
// string(2) "05"
```

#### To Month Number

Returns the month number or null on failure.

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

var_dump($df->toMonthNumber('Feb'));
// int(2)

var_dump($df->toMonthNumber('Foo'));
// NULL
```

#### To Month Number String

Returns the month number as string or null on failure.

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

var_dump($df->toMonthNumberString('Jan'));
// string(2) "01"
```

#### To Weekday

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

var_dump($df->toWeekday(5));
// string(6) "Friday"

var_dump($df->toWeekday(number: 5, locale: 'de'));
// string(7) "Freitag"

// pattern: E, EE, or EEE = 'Tue', 'EEEE' = 'Tuesday', 'EEEEE' = 'T', 'EEEEEE' = 'Tu'
var_dump($df->toWeekday(number: 5, pattern: 'EEE'));
// string(3) "Fri"
```

#### To Weekday Number

Returns the weekday number or null on failure.

```php
use Tobento\Service\Dater\DateFormatter;

$df = new DateFormatter();

// 'Tuesday', 'Tue', 'Tu' (lower- and uppercase)
var_dump($df->toWeekdayNumber('Tue'));
// int(2)
```

### Dater

A fluent way modifying dates.

```php
use Tobento\Service\Dater\Dater;
use DateTimeImmutable;

$dater = new Dater('now');

var_dump($dater instanceof DateTimeImmutable); // bool(true)

$dater = $dater->addMinutes(5)
               ->subMinutes(3)
               ->addDays(10)
               ->subDays(2);
```

```php
use Tobento\Service\Dater\DaterMutable;
use DateTime;

$dater = new DaterMutable('now');

var_dump($dater instanceof DateTime); // bool(true)

$dater = $dater->addMinutes(5)
               ->subMinutes(3)
               ->addDays(10)
               ->subDays(2);
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)