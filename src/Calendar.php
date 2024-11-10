<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 9. 11. 2024
 * Time: 15:54
 */

namespace Tito10047\Calendar;

use DateTimeImmutable;
use Tito10047\Calendar\Enum\CalendarType;
use Tito10047\Calendar\Enum\DayName;
use Tito10047\Calendar\Interface\CalendarInterface;
use Tito10047\Calendar\Interface\DaysGeneratorInterface;

final readonly class Calendar implements CalendarInterface
{
    /** @var DateTimeImmutable[] */
    private array $days;


    public function __construct(
        private DateTimeImmutable $date,
        private DaysGeneratorInterface $type = CalendarType::Monthly,
        private DayName $startDay = DayName::Monday,
        /** @var DateTimeImmutable[] $disabledDays */
        private array $disabledDays = []
    ) {
        $this->days = $this->type->getDays($this->date, $this->startDay);
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }


    public function disableDaysRange(DateTimeImmutable $from = null, DateTimeImmutable $to = null): self
    {
        if (!$from) {
            $from = $this->type->getStartDate($this->date, $this->startDay);
        }
        if (!$to) {
            $to = $this->type->getEndDate($this->date, $this->startDay);
        }
        $current = clone $from;
        $days = [];
        while ($current <= $to) {
            $days[] = $current;
            $current = $current->modify('+1 day');
        }
        return $this->disableDays(...$days);
    }


    public function disableDays(DateTimeImmutable ...$days): self
    {
        return new self(
            date: $this->date,
            type: $this->type,
            startDay: $this->startDay,
            disabledDays: array_unique(array_merge($this->disabledDays, $days), SORT_REGULAR)
        );
    }

    public function disableDayName(DayName ...$daysToDisable): static
    {
        $days = $this->days;
        $disabled = [];
        while ($day = array_shift($days)){
            foreach($daysToDisable as $dayName){
                if (DayName::fromDate($day) === $dayName){
                    $disabled[] = $day;
                }
            }
        }
        return $this->disableDays(...$disabled);
    }

    public function disableWeek(int $weekNum): self
    {
        $days = $this->days;
        $disabled = [];
        while ($day = array_shift($days)){
            if ((int)$day->format('W') === $weekNum) {
                $disabled[] = $day;
            }
        }
        return $this->disableDays(...$disabled);
    }


    public function nextMonth(): self
    {
        $date = $this->date->modify('+1 month');
        return new self(
            date: $date,
            type: $this->type,
            startDay: $this->startDay,
            disabledDays: []
        );
    }

    public function prevMonth(): self
    {
        $date = $this->date->modify('-1 month');
        return new self(
            date: $date,
            type: $this->type,
            startDay: $this->startDay,
            disabledDays: []
        );
    }

    /**
     * @return Day[][]
     */
    public function getDaysTable(): array
    {
        $days = $this->days;
        $rows = [];
        while (count($days) > 0) {
            $row = [];
            $weekNum = (int)$days[0]->format('W');
            for ($i = 0; $i < 7 and count($days) > 0; $i++) {
                $day = array_shift($days);
                $row[] = new Day(
                    date: $day,
                    ghost: $day->format('m') !== $this->date->format('m'),
                    today: $day->format('Y-m-d') === date('Y-m-d'),
                    enabled: !in_array($day, $this->disabledDays)
                );
            }
            $rows[$weekNum] = $row;
        }
        return $rows;
    }

    public function isDayDisabled(DateTimeImmutable|Day $day): bool
    {
        if ($day instanceof Day) {
            $day = $day->date;
        }
        foreach($this->disabledDays as $disabledDay){
            if($disabledDay->format('Y-m-d') === $day->format('Y-m-d')){
                return true;
            }
        }
        return false;
    }

    public function getStartDay():DayName
    {
        return $this->startDay;
    }

    public function getDisabledDays(): array
    {
        return $this->disabledDays;
    }

}