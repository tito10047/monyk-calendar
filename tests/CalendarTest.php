<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 29. 11. 2024
 * Time: 21:24
 */

namespace Tito10047\Calendar\Tests;

use Tito10047\Calendar\Calendar;
use Tito10047\Calendar\Enum\CalendarType;
use Tito10047\Calendar\Enum\DayName;

class CalendarTest extends \PHPUnit\Framework\TestCase
{
    public function testStartDay()
    {
        $calendar = new Calendar(
            new \DateTimeImmutable('2024-11-04'),
            CalendarType::WorkWeek,
            DayName::Monday,
        );
        $this->assertFalse($calendar->isFirstDay(new \DateTimeImmutable('2024-10-01')));
        $this->assertTrue($calendar->isFirstDay(new \DateTimeImmutable('2024-11-01')));
        $this->assertFalse($calendar->isFirstDay(new \DateTimeImmutable('2024-11-02')));
        $this->assertFalse($calendar->isFirstDay(new \DateTimeImmutable('2024-11-15')));
        $this->assertFalse($calendar->isFirstDay(new \DateTimeImmutable('2024-11-30')));
    }
    public function testEndDay()
    {
        $calendar = new Calendar(
            new \DateTimeImmutable('2024-11-04'),
            CalendarType::WorkWeek,
            DayName::Monday,
        );
        $this->assertFalse($calendar->isLastDay(new \DateTimeImmutable('2024-11-01')));
        $this->assertFalse($calendar->isLastDay(new \DateTimeImmutable('2024-11-02')));
        $this->assertFalse($calendar->isLastDay(new \DateTimeImmutable('2024-11-15')));
        $this->assertTrue($calendar->isLastDay(new \DateTimeImmutable('2024-11-30')));
        $this->assertFalse($calendar->isLastDay(new \DateTimeImmutable('2024-10-30')));
    }
}