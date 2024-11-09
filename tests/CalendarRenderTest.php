<?php

namespace Tito10047\Calendar\Tests;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tito10047\Calendar\Calendar;
use Tito10047\Calendar\Enum\CalendarType;
use Tito10047\Calendar\Enum\Day;


class CalendarRenderTest extends TestCase
{
    public function testRenderDays():void
    {
        $calendar = new Calendar(
            new DateTimeImmutable('2024-11-05'),
            CalendarType::Monthly,
            Day::Monday,
        );
        $html = $calendar->render();
        $this->assertNotEmpty($html);
        $this->assertSame(6,substr_count($html, '<tr>'));
        $this->assertSame(35,substr_count($html, '<td>'));
    }
}