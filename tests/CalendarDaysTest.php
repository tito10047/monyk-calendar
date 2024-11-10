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
use Tito10047\Calendar\Day;
use Tito10047\Calendar\Enum\CalendarType;
use Tito10047\Calendar\Enum\DayName;

class CalendarDaysTest extends TestCase
{
    public function testGetWorkWeekDays():void
    {
        $calendar = new Calendar(
            new \DateTimeImmutable('2024-11-04'),
            CalendarType::WorkWeek,
            DayName::Monday,
        );
        $calendar = $calendar
            ->disableDaysByName(DayName::Saturday,DayName::Sunday);
        $dayTable = $calendar->getDaysTable();
        $this->assertCount(1, $dayTable);
        $days = array_shift($dayTable);
        $days = array_filter($days, fn(Day $day) => $day->enabled);
        $dates = [
            '2024-11-04',
            '2024-11-05',
            '2024-11-06',
            '2024-11-07',
            '2024-11-08',
        ];
        $days = array_filter($days, fn(Day $day) => $day->enabled);
        foreach ($days as $key => $day) {
            $this->assertEquals($dates[$key], $day->date->format('Y-m-d'));
        }
    }
    public function testGetWorkWeekDaysStartOfMonth():void
    {
        $calendar = new Calendar(
            new \DateTimeImmutable('2024-11-01'),
            CalendarType::WorkWeek,
            DayName::Monday,
        );
        $calendar = $calendar->disableDaysByName(DayName::Saturday,DayName::Sunday);
        $daysTable = $calendar->getDaysTable();
        $this->assertCount(1, $daysTable);
        $days = array_shift($daysTable);
        $dates = [
            '2024-10-28',
            '2024-10-29',
            '2024-10-30',
            '2024-10-31',
            '2024-11-01',
        ];
        $this->assertCount(7, $days);
        $days = array_filter($days, fn(Day $day) => $day->enabled);
        $this->assertCount(5, $days);
        foreach ($days as $key => $day) {
            $this->assertEquals($dates[$key], $day->date->format('Y-m-d'));
        }
    }

    public function testGetMonthDays():void
    {
        $firstDay = new \DateTimeImmutable('2024-11-01');
        $calendar = new Calendar(
            $firstDay,
            CalendarType::Monthly,
            DayName::Monday
        );
        $daysTable = $calendar->getDaysTable();
        $this->assertCount(5, $daysTable);
        /** @var \Tito10047\Calendar\Day[] $days */
        $days = array_merge(...$daysTable);
        $firstDay = $firstDay->modify('first day of this month');
        $firstDay = $firstDay->modify("monday this week");
        $lastDay = $firstDay->modify('last day of this month');
        $lastDay = $lastDay->modify("sunday this week");
        $currentDay = $firstDay;
        foreach ($days as $day) {
            $this->assertEquals($currentDay->format('Y-m-d'), $day->date->format('Y-m-d'));
            $currentDay = $currentDay->modify('+1 day');
        }
        $this->assertEquals($lastDay->format('Y-m-d'), $lastDay->format('Y-m-d'));
    }

    public function testWeekDays():void
    {
        $calendar = new Calendar(
            new \DateTimeImmutable('2024-11-05'),
            CalendarType::Weekly,
            DayName::Monday,
        );
        $daysTable = $calendar->getDaysTable();
        $this->assertCount(1, $daysTable);
        /** @var \Tito10047\Calendar\Day[] $days */
        $days = array_merge(...$daysTable);
        $start = new \DateTimeImmutable('2024-11-04');
        $currentDay = $start;
        $this->assertCount(7, $days);
        foreach ($days as $day) {
            $this->assertEquals($currentDay->format('Y-m-d'), $day->date->format('Y-m-d'));
            $currentDay = $currentDay->modify('+1 day');
        }
    }
}