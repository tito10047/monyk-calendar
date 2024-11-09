<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 9. 11. 2024
 * Time: 17:37
 */

namespace Tito10047\Calendar\Tests;

use PHPUnit\Framework\TestCase;
use Tito10047\Calendar\Calendar;
use Tito10047\Calendar\Enum\CalendarType;
use Tito10047\Calendar\Enum\Day;

class CalendarDaysTest extends TestCase
{
    public function testGetWorkWeekDays():void
    {
        $calendar = new Calendar(
            new \DateTimeImmutable('2024-11-04'),
            CalendarType::WorkWeek,
            Day::Monday,
        );
        $dayTable = $calendar->getDaysTable();
        $this->assertCount(1, $dayTable);
        $days = $dayTable[0];
        $dates = [
            '2024-11-04',
            '2024-11-05',
            '2024-11-06',
            '2024-11-07',
            '2024-11-08',
        ];
        var_dump($days);
        foreach ($days as $key => $day) {
            $this->assertEquals($dates[$key], $day->format('Y-m-d'));
        }
    }
    public function testGetWorkWeekDaysStartOfMonth():void
    {
        $calendar = new Calendar(
            new \DateTimeImmutable('2024-11-01'),
            CalendarType::WorkWeek,
            Day::Monday,
        );
        $daysTable = $calendar->getDaysTable();
        $this->assertCount(1, $daysTable);
        $days = $daysTable[0];
        $this->assertCount(5, $days);
        $dates = [
            '2024-10-28',
            '2024-10-29',
            '2024-10-30',
            '2024-10-31',
            '2024-11-01',
        ];
        var_dump($days);
        foreach ($days as $key => $day) {
            $this->assertEquals($dates[$key], $day->format('Y-m-d'));
        }
    }

    public function testGetMonthDays():void
    {
        $firstDay = new \DateTimeImmutable('2024-11-01');
        $calendar = new Calendar(
            $firstDay,
            CalendarType::Monthly,
            Day::Monday
        );
        $daysTable = $calendar->getDaysTable();
        $this->assertCount(5, $daysTable);
        $days = array_merge(...$daysTable);
        $firstDay = $firstDay->modify('first day of this month');
        $lastDay = $firstDay->modify('last day of this month');
        $currentDay = $firstDay;
        foreach ($days as $day) {
            $this->assertEquals($currentDay->format('Y-m-d'), $day->format('Y-m-d'));
            $currentDay = $currentDay->modify('+1 day');
        }
        $this->assertEquals($lastDay->format('Y-m-d'), $lastDay->format('Y-m-d'));
    }

    public function testWeekDays():void
    {
        $calendar = new Calendar(
            new \DateTimeImmutable('2024-11-05'),
            CalendarType::Weekly,
            Day::Monday,
        );
        $daysTable = $calendar->getDaysTable();
        $this->assertCount(1, $daysTable);
        $days = array_merge(...$daysTable);
        $start = new \DateTimeImmutable('2024-11-04');
        $currentDay = $start;
        $this->assertCount(7, $days);
        foreach ($days as $day) {
            $this->assertEquals($currentDay->format('Y-m-d'), $day->format('Y-m-d'));
            $currentDay = $currentDay->modify('+1 day');
        }
    }
}