<?php

namespace Tito10047\Calendar\Tests;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tito10047\Calendar\Calendar;
use Tito10047\Calendar\Enum\CalendarType;
use Tito10047\Calendar\Enum\DayName;
use Tito10047\Calendar\Renderer;


class CalendarRenderTest extends TestCase
{
    public function testRenderDays():void
    {
        $date = new DateTimeImmutable();
        $date = $date->setDate($date->format('Y'), $date->format('m'), 5);
        $calendar = new Calendar(
            $date,
            CalendarType::Monthly,
            DayName::Monday,
        );
        $renderer = Renderer::factory(CalendarType::Monthly,'calendar');
        $html = $renderer->render($calendar);
        $this->assertNotEmpty($html);
        echo($html);
        $this->assertSame(6,substr_count($html, '<tr>'));
        $this->assertSame(35,substr_count($html, '<td'));
        $this->assertSame(1,substr_count($html, 'today'));
    }
}