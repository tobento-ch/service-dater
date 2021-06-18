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
use Tobento\Service\Dater\DaterInterface;
use Tobento\Service\Dater\DaterMutable;
use DateTime;

/**
 * DaterTest tests
 */
class DaterMutableTest extends TestCase
{    
    public function testDaterReturnsDateTimeAndDaterInterface()
    {
        $d = new DaterMutable();
        
        $this->assertInstanceOf(DateTime::class, $d);
        $this->assertInstanceOf(DaterInterface::class, $d);
    }
    
    public function testAddMinutesMethod()
    {
        $d = new DaterMutable('2021-05-23 13:20:34');
        
        $this->assertSame('2021-05-23 13:30:34', $d->addMinutes(10)->format('Y-m-d H:i:s'));
    }
    
    public function testSubMinutesMethod()
    {
        $d = new DaterMutable('2021-05-23 13:20:34');
        
        $this->assertSame('2021-05-23 13:10:34', $d->subMinutes(10)->format('Y-m-d H:i:s'));
    }
    
    public function testAddDaysMethod()
    {
        $d = new DaterMutable('2021-05-23 13:20:34');
        
        $this->assertSame('2021-05-28 13:20:34', $d->addDays(5)->format('Y-m-d H:i:s'));
    }

    public function testSubDaysMethod()
    {
        $d = new DaterMutable('2021-05-23 13:20:34');
        
        $this->assertSame('2021-05-18 13:20:34', $d->subDays(5)->format('Y-m-d H:i:s'));
    }     
}