<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 10. 11. 2024
 * Time: 8:23
 */

namespace Tito10047\Calendar\Interface;

use DateTimeImmutable;
use Tito10047\Calendar\Day;
use Tito10047\Calendar\Enum\DayName;

interface CalendarInterface
{
    public function getDate(): DateTimeImmutable;

    public function disableDaysRange(
        DateTimeImmutable $from = null,
        DateTimeImmutable $to = null
    ): self;

    public function disableDays(DateTimeImmutable ...$days): self;

    public function disableDayName(DayName ...$daysToDisable): self;

    public function disableWeek(int $weekNum): self;

    public function nextMonth(): self;

    public function prevMonth(): self;

    /**
     * @return Day[][]
     */
    public function getDaysTable(): array;

    public function isDayDisabled(DateTimeImmutable|Day $day): bool;

    public function getStartDay(): DayName;

    public function getDisabledDays(): array;
}