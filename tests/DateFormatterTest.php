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

namespace Tobento\Service\Dater\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Dater\DateFormatter;
use Tobento\Service\Dater\DaterInterface;
use Tobento\Service\Dater\Dater;
use Tobento\Service\Dater\DaterMutable;
use DateTimeImmutable;
use DateTime;
use DateInterval;

/**
 * DateFormatterTest tests
 */
class DateFormatterTest extends TestCase
{
    public function testWithLocaleMethodReturnsNewInstance()
    {
        $df = new DateFormatter();
        
        $newDf = $df->withLocale('en_US');
        
        $this->assertFalse($df === $newDf);
    }
    
    public function testGetLocaleMethod()
    {
        $df = new DateFormatter(locale: 'de');
        
        $newDf = $df->withLocale('en_US');
        
        $this->assertSame('de', $df->getLocale());
        
        $this->assertSame('de', $df->getLocale('-'));
        
        $this->assertSame('en_US', $newDf->getLocale());
        
        $this->assertSame('en-US', $newDf->getLocale('-'));
    }

    public function testWithDateFormatMethodReturnsNewInstance()
    {
        $df = new DateFormatter();
        
        $newDf = $df->withDateFormat('EE, dd. MMMM yyyy');
        
        $this->assertFalse($df === $newDf);
    }
    
    public function testGetDateFormatMethod()
    {
        $df = new DateFormatter(dateFormat: 'EEEE, dd. MMMM yyyy');
        
        $newDf = $df->withDateFormat('EE, dd. MMMM yyyy');
        
        $this->assertSame('EEEE, dd. MMMM yyyy', $df->getDateFormat());
        
        $this->assertSame('EE, dd. MMMM yyyy', $newDf->getDateFormat());
    }

    public function testWithDateTimeFormatMethodReturnsNewInstance()
    {
        $df = new DateFormatter();
        
        $newDf = $df->withDateTimeFormat('EE, dd. MMMM yyyy, HH:mm');
        
        $this->assertFalse($df === $newDf);
    }
    
    public function testGetDateTimeFormatMethod()
    {
        $df = new DateFormatter(dateTimeFormat: 'EEEE, dd. MMMM yyyy, HH:mm');
        
        $newDf = $df->withDateTimeFormat('EE, dd. MMMM yyyy, HH:mm');
        
        $this->assertSame('EEEE, dd. MMMM yyyy, HH:mm', $df->getDateTimeFormat());
        
        $this->assertSame('EE, dd. MMMM yyyy, HH:mm', $newDf->getDateTimeFormat());
    }
    
    public function testWithTimezoneMethodReturnsNewInstance()
    {
        $df = new DateFormatter();
        
        $newDf = $df->withTimezone('America/Chicago');
        
        $this->assertFalse($df === $newDf);
    }
    
    public function testGetTimezoneMethod()
    {
        $df = new DateFormatter(timezone: 'Europe/Berlin');
        
        $newDf = $df->withTimezone('America/Chicago');
        
        $this->assertSame('Europe/Berlin', $df->getTimezone()->getName());
        
        $this->assertSame('America/Chicago', $newDf->getTimezone()->getName());
    }
    
    public function testWithMutableMethodReturnsNewInstance()
    {
        $df = new DateFormatter();
        
        $newDf = $df->withMutable();
        
        $this->assertFalse($df === $newDf);
    }
    
    public function testMutableReturnsDateTime()
    {
        $df = new DateFormatter(mutable: true);
        $newDf = $df->withMutable(true);
        
        $this->assertInstanceOf(DateTime::class, $df->toDateTime('now'));
        $this->assertInstanceOf(DateTime::class, $df->toDateTime('now'));
    }
    
    public function testImmutableReturnsDateTimeImmutable()
    {
        $df = new DateFormatter(mutable: false);
        $newDf = new DateFormatter();
        $d = $df->withMutable(false);
        
        $this->assertInstanceOf(DateTimeImmutable::class, $df->toDateTime('now'));
        $this->assertInstanceOf(DateTimeImmutable::class, $newDf->toDateTime('now'));
        $this->assertInstanceOf(DateTimeImmutable::class, $d->toDateTime('now'));
    }

    public function testDateMethod()
    {
        $df = new DateFormatter();
        
        $this->assertSame('Friday, 16. April 2021', $df->date('2021-04-16'));
        
        $this->assertSame(
            'Fri, 16. April 2021',
            $df->date('2021-04-16', format: 'EE, dd. MMMM yyyy')
        );

        $this->assertSame(
            'Freitag, 16. April 2021',
            $df->date('2021-04-16', locale: 'de_DE')
        );
    }
    
    public function testDateTimeMethod()
    {
        $df = new DateFormatter();
        
        $this->assertSame('Friday, 16. April 2021, 00:00', $df->dateTime('2021-04-16'));
        
        $this->assertSame(
            'Fri, 16. April 2021 00:00',
            $df->dateTime('2021-04-16', format: 'EE, dd. MMMM yyyy HH:mm')
        );

        $this->assertSame(
            'Freitag, 16. April 2021, 00:00',
            $df->dateTime('2021-04-16', locale: 'de_DE')
        );
    }
    
    public function testFormatMethod()
    {
        $df = new DateFormatter();
        
        $this->assertSame('16.04.2021 00:00', $df->format('2021-04-16'));
        
        $this->assertSame(
            '16.04.2021',
            $df->format('2021-04-16', format: 'd.m.Y')
        );
        
        // fallsback to now on fail
        $this->assertSame(
            $df->toDateTime('now')->format('d.m.Y H:i'),
            $df->format('16-06-19457')
        );
    }

    public function testInPastMethod()
    {
        $df = new DateFormatter();
        
        $this->assertTrue(
            $df->inPast(date: '12.05.2021', currentDate: '13.05.2021')
        );
        
        $this->assertFalse(
            $df->inPast(date: '12.05.2021', currentDate: '11.05.2021')
        );
        
        $this->assertTrue(
            $df->inPast(date: '12.05.2021 13:00', currentDate: '12.05.2021 13:00', sameTimeIsPast: true)
        );
        
        $this->assertFalse(
            $df->inPast(date: '12.05.2021 13:00', currentDate: '12.05.2021 13:00', sameTimeIsPast: false)
        );
        
        $this->assertTrue(
            $df->inPast(date: '06.12.2021', currentDate: '2021-12-07')
        );        
    }
    
    public function testInBetweenMethod()
    {
        $df = new DateFormatter();
        
        $this->assertTrue(
            $df->inBetween(dateFrom: '18.05.2021', dateTo: '20.05.2021', currentDate: '19.05.2021')
        );

        $this->assertTrue(
            $df->inBetween(dateFrom: '18.05.2021', dateTo: '20.05.2021', currentDate: '20.05.2021')
        );
        
        $this->assertTrue(
            $df->inBetween(dateFrom: '18.05.2021', dateTo: '20.05.2021 15:00', currentDate: '20.05.2021 14:59')
        );
        
        // exact time to
        $this->assertTrue(
            $df->inBetween(dateFrom: '18.05.2021', dateTo: '20.05.2021 15:00', currentDate: '20.05.2021 15:00')
        );
        
        $this->assertFalse(
            $df->inBetween(dateFrom: '18.05.2021', dateTo: '20.05.2021 15:00', currentDate: '20.05.2021 15:01')
        );
        
        // exact time from
        $this->assertTrue(
            $df->inBetween(dateFrom: '18.05.2021 15:00', dateTo: '20.05.2021', currentDate: '18.05.2021 15:00')
        );
        
        $this->assertTrue(
            $df->inBetween(dateFrom: '18.05.2021 15:00', dateTo: '20.05.2021', currentDate: '18.05.2021 15:01')
        );
        
        $this->assertFalse(
            $df->inBetween(dateFrom: '18.05.2021 15:00', dateTo: '20.05.2021', currentDate: '18.05.2021 14:59')
        );
        
        // yearly
        $this->assertFalse(
            $df->inBetween(dateFrom: '18.05.2021', dateTo: '20.05.2021', currentDate: '19.05.2022', yearly: false)
        );
        
        $this->assertTrue(
            $df->inBetween(dateFrom: '18.05.2021', dateTo: '20.05.2021', currentDate: '19.05.2022', yearly: true)
        );
    }
    
    public function testDiffMethodReturnsDateInterval()
    {
        $df = new DateFormatter();
        
        $this->assertInstanceOf(DateInterval::class, $df->diff(date: '17.05.2000', currentDate: '18.05.2001'));
    }

    public function testNowMethodReturnsDater()
    {
        $df = new DateFormatter();
        
        $this->assertInstanceOf(Dater::class, $df->now());
    }

    public function testNowMethodReturnsDaterMutable()
    {
        $df = new DateFormatter(mutable: true);
        
        $this->assertInstanceOf(DaterMutable::class, $df->now());
    }
    
    public function testToDateTimeMethodReturnsDateTimeImmutable()
    {    
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            (new DateFormatter())->toDateTime('2021-06-24')
        );
        
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            (new DateFormatter(mutable: false))->toDateTime('2021-06-24')
        );
    
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            (new DateFormatter())->withMutable(false)->toDateTime('2021-06-24')
        );        
    }
    
    public function testToDateTimeMethodReturnsDateTimeImmutableIfValueIsDateTime()
    {
        $df = new DateFormatter();
        $value = new DateTime('2021-06-24');
        $dt = $df->toDateTime($value);
        
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            $dt
        );
        
        $this->assertSame(
            $value->format('d.m.Y H:i'),
            $dt->format('d.m.Y H:i')
        );
    }
    
    public function testToDateTimeMethodReturnsDateTimeImmutableIfValueIsDateTimeImmutable()
    {
        $df = new DateFormatter();
        $value = new DateTimeImmutable('2021-06-24');
        $dt = $df->toDateTime($value);
        
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            $dt
        );
        
        $this->assertSame(
            $value->format('d.m.Y H:i'),
            $dt->format('d.m.Y H:i')
        );
    }

    public function testToDateTimeMethodWithMutableReturnsDateTimeIfValueIsDateTimeImmutable()
    {
        $df = new DateFormatter(mutable: true);
        $value = new DateTimeImmutable('2021-06-24');
        $dt = $df->toDateTime($value);
        
        $this->assertInstanceOf(
            DateTime::class,
            $dt
        );
        
        $this->assertSame(
            $value->format('d.m.Y H:i'),
            $dt->format('d.m.Y H:i')
        );
    }
    
    public function testToDateTimeMethodWithMutableReturnsDateTimeIfValueIsDateTime()
    {
        $df = new DateFormatter(mutable: true);
        $value = new DateTime('2021-06-24');
        $dt = $df->toDateTime($value);
        
        $this->assertInstanceOf(
            DateTime::class,
            $dt
        );
        
        $this->assertSame(
            $value->format('d.m.Y H:i'),
            $dt->format('d.m.Y H:i')
        );
    }    
    
    public function testToDateTimeMethodReturnsDateTime()
    {        
        $this->assertInstanceOf(
            DateTime::class,
            (new DateFormatter(mutable: true))->toDateTime('2021-06-24')
        );
    
        $this->assertInstanceOf(
            DateTime::class,
            (new DateFormatter())->withMutable(true)->toDateTime('2021-06-24')
        );        
    }
    
    public function testToDateTimeMethodWithTimestamp()
    {
        $this->assertSame(
            1708708399,
            (new DateFormatter())->toDateTime('1708708399')->getTimestamp()
        );
        $this->assertSame(
            1708708399,
            (new DateFormatter())->toDateTime(1708708399)->getTimestamp()
        );
    }

    public function testToDateTimeMethodWithAnotherTimezone()
    {
        $df = new DateFormatter(timezone: 'Europe/Berlin');
        
        $this->assertSame(
            'America/Chicago',
            $df->toDateTime('2021-06-24', timezone: 'America/Chicago')->getTimezone()->getName()
        );
        
        // invalid timezone fallsback to default
        $this->assertSame(
            'Europe/Berlin',
            $df->toDateTime('2021-06-24', timezone: 'Bar/Foo')->getTimezone()->getName()
        );
    }
    
    public function testToDateTimeMethodWithInvalidDateFallsbackToNow()
    {
        $df = new DateFormatter();
        
        $this->assertSame(
            $df->toDateTime('now')->format('d.m.Y H:i'),
            $df->toDateTime('a-06-24')->format('d.m.Y H:i')
        );
    }
    
    public function testToDateTimeMethodWithInvalidDateFallsbackToTheFallback()
    {
        $df = new DateFormatter();
        
        $this->assertSame(
            $df->toDateTime('2021-02-15')->format('d.m.Y H:i'),
            $df->toDateTime('a-06-24', fallback: '2021-02-15')->format('d.m.Y H:i')
        );
    }    

    public function testToDateTimeMethodWithInvalidDateFallsbackToNullIfSet()
    {
        $df = new DateFormatter();
        
        $this->assertSame(
            null,
            $df->toDateTime('a-06-24', fallback: null)
        );
    }
    
    public function testToDateTimeMethodWithValueFormat()
    {
        $df = new DateFormatter();
        
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            $df->toDateTime('2021-02-30', fallback: null, valueFormat: null)
        );
        
        $this->assertSame(
            null,
            $df->toDateTime('2021-02-30', fallback: null, valueFormat: 'd.m.Y')
        );        
        
        $this->assertSame(
            null,
            $df->toDateTime('2021-02-30', fallback: null, valueFormat: 'Y-m-d')
        );
        
        $this->assertSame(
            null,
            $df->toDateTime('2021-02-15', fallback: null, valueFormat: 'd.m.Y')
        );
        
        $this->assertSame(
            null,
            $df->toDateTime('2021-02-15', fallback: null, valueFormat: 'Y-m-d H:i:s')
        );
        
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            $df->toDateTime('2021-02-15', fallback: null, valueFormat: 'Y-m-d')
        );
        
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            $df->toDateTime('2021-02-15 13:05', fallback: null, valueFormat: 'Y-m-d H:i')
        );
    } 
    
    public function testToDaterMethod()
    {
        $df = new DateFormatter();

        $this->assertInstanceOf(
            Dater::class,
            $df->toDater('2021-06-24')
        );
        
        $this->assertInstanceOf(
            DaterInterface::class,
            $df->toDater('2021-06-24')
        );
    }
    
    public function testToDaterMethodReturnsDaterMutable()
    {
        $df = new DateFormatter(mutable: true);

        $this->assertInstanceOf(
            DaterMutable::class,
            $df->toDater('2021-06-24')
        );
        
        $this->assertInstanceOf(
            DaterInterface::class,
            $df->toDater('2021-06-24')
        );
    }
    
    public function testToCurrentYearMethod()
    {
        $df = new DateFormatter();
        
        $this->assertSame(
            '2022-06-24',
            $df->toCurrentYear('2021-06-24', currentDate: '2022-02-24')->format('Y-m-d')
        );
        
        $this->assertSame(
            '2022-06-24 13:02:12',
            $df->toCurrentYear('2021-06-24 13:02:12', currentDate: '2022-02-24 15:01:23')->format('Y-m-d H:i:s')
        );
    }
    
    public function testToMonthMethod()
    {
        $df = new DateFormatter();
        
        $this->assertSame('May', $df->toMonth(5));
    }

    public function testToMonthMethodWithLocale()
    {
        $df = new DateFormatter();
        
        $this->assertSame('Mai', $df->toMonth(5, locale: 'de'));
    }

    public function testToMonthMethodWithPattern()
    {
        $df = new DateFormatter();
        
        $this->assertSame('05', $df->toMonth(5, pattern: 'MM'));
    }
    
    public function testToMonthNumberMethod()
    {
        $df = new DateFormatter();
        
        $this->assertSame(2, $df->toMonthNumber('Feb'));
        $this->assertSame(2, $df->toMonthNumber('feb'));
        $this->assertSame(2, $df->toMonthNumber('FEB'));
    }

    public function testToMonthNumberMethodReturnsNullOnFail()
    {
        $df = new DateFormatter();
        
        $this->assertSame(null, $df->toMonthNumber('febi'));
        $this->assertSame(null, $df->toMonthNumber(15));
    }

    public function testToMonthNumberStringMethod()
    {
        $df = new DateFormatter();
        
        $this->assertSame('02', $df->toMonthNumberString('Feb'));
        $this->assertSame('02', $df->toMonthNumberString('feb'));
        $this->assertSame('02', $df->toMonthNumberString('FEB'));
    }
    
    public function testToMonthNumberStringMethodReturnsNullOnFail()
    {
        $df = new DateFormatter();
        
        $this->assertSame(null, $df->toMonthNumberString('Febi'));
        $this->assertSame(null, $df->toMonthNumberString(12));
    }

    public function testToWeekdayMethod()
    {
        $df = new DateFormatter();
        
        $this->assertSame('Friday', $df->toWeekday(5));
    }

    public function testToWeekdayMethodWithLocale()
    {
        $df = new DateFormatter();
        
        $this->assertSame('Freitag', $df->toWeekday(5, locale: 'de'));
    }

    public function testToWeekdayMethodWithPattern()
    {
        $df = new DateFormatter();
        
        $this->assertSame('Fri', $df->toWeekday(5, pattern: 'EEE'));
    }

    public function testToWeekdayMethodReturnsNullOnFail()
    {
        $df = new DateFormatter();
        
        $this->assertSame(null, $df->toWeekday(15));
        $this->assertSame(null, $df->toWeekday('15'));
        $this->assertSame(null, $df->toWeekday('5'));
    }
    
    public function testToWeekdayNumberMethod()
    {
        $df = new DateFormatter();
        
        $this->assertSame(2, $df->toWeekdayNumber('Tue'));
        $this->assertSame(2, $df->toWeekdayNumber('tue'));
        $this->assertSame(2, $df->toWeekdayNumber('TUE'));
        $this->assertSame(2, $df->toWeekdayNumber('Tuesday'));
        $this->assertSame(2, $df->toWeekdayNumber('tuesday'));
        $this->assertSame(2, $df->toWeekdayNumber(2));
    }
    
    public function testToWeekdayNumberMethodReturnsNullOnFail()
    {
        $df = new DateFormatter();
        
        $this->assertSame(null, $df->toWeekdayNumber('Tues'));
    }    
}