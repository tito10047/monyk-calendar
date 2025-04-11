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
use Tito10047\Calendar\Interface\DayDataLoaderInterface;
use Tito10047\Calendar\Interface\DaysGeneratorInterface;

final class Calendar implements CalendarInterface
{
    /** @var DateTimeImmutable[] */
    private array $days;
    private ?DayDataLoaderInterface $dataLoader = null;


    public function __construct(
        private readonly DateTimeImmutable $date,
        private readonly DaysGeneratorInterface $daysGenerator = CalendarType::Monthly,
        private readonly DayName $startDay = DayName::Monday,
        /** @var DateTimeImmutable[] $disabledDays */
        private readonly array $disabledDays = []
    ) {
        $this->days = $this->daysGenerator->getDays($this->date, $this->startDay);
        if (count($this->days) === 0) {
            throw new \InvalidArgumentException('Day generator returned no days');
        }
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }


    public function disableDaysRange(DateTimeImmutable $from = null, DateTimeImmutable $to = null): self
    {
        if (!$from) {
            $from = $this->days[0];
        }
        if (!$to) {
            $to = end($this->days);
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
        $disabledDays = $this->disabledDays;
        foreach($days as $day){
            $disabledDays[$day->format('Y-m-d')] = $day;
        }
        return new self(
            date: $this->date,
            daysGenerator: $this->daysGenerator,
            startDay: $this->startDay,
            disabledDays: $disabledDays
        );
    }

    public function disableDaysByName(DayName ...$daysToDisable): static
    {
        $disabled = [];
        foreach($this->days as $day){
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
        $disabled = [];
        foreach($this->days as $day){
            if ((int)$day->format('W') === $weekNum) {
                $disabled[] = $day;
            }
        }
        return $this->disableDays(...$disabled);
    }

    public function setDataLoader(DayDataLoaderInterface $dataLoader): self
    {
        $clone = clone $this;
        $clone->dataLoader = $dataLoader;
        return $clone;
    }


    public function nextMonth(): self
    {
        $date = $this->date->modify('+1 month')->modify("first day of this month");
        return new self(
            date: $date,
            daysGenerator: $this->daysGenerator,
            startDay: $this->startDay,
            disabledDays: []
        );
    }

    public function prevMonth(): self
    {
        $date = $this->date->modify('-1 month')->modify("first day of this month");
        return new self(
            date: $date,
            daysGenerator: $this->daysGenerator,
            startDay: $this->startDay,
            disabledDays: []
        );
    }

    /**
     * @return Day[][]
     */
    public function getDaysTable(): array
    {
        $thisMonthNum = $this->date->format('m');
        $days = $this->days;
        $today = date('Y-m-d');
        $rows = [];
        $firstDay = $days[0];
        $lastDay = end($days);
        $this->dataLoader?->load($firstDay,$lastDay);
        while (count($days) > 0) {
            $row = [];
            $firstDay = $days[0];
            $weekNum = (int)$firstDay->format('W');
            for ($i = (int)$firstDay->format("N"); $i <=7 and count($days) > 0; $i++) {
                $day = array_shift($days);
                $dayElm = new Day(
                    date: $day,
                    ghost: $day->format('m') !== $thisMonthNum,
                    today: $day->format('Y-m-d') === $today,
                    enabled: !array_key_exists($day->format('Y-m-d'), $this->disabledDays),
                );
                if ($this->dataLoader){
                    $dayElm = $dayElm->withData(
                        data: $this->dataLoader?->getData($day)
                    );
                }
                $row[$i] = $dayElm;
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
        return array_key_exists($day->format('Y-m-d'), $this->disabledDays);
    }

    public function getStartDay():DayName
    {
        return $this->startDay;
    }


    public function getDisabledDays(): array
    {
        return array_values($this->disabledDays);
    }

    public function isFirstDay(\DateTimeInterface|Day $day): bool
    {
        if ($day instanceof Day) {
            $day = $day->date;
        }
        return $this->date->modify("first day of this month")->format('Y-m-d') === $day->format('Y-m-d');
    }

    public function isLastDay(\DateTimeInterface|Day $day): bool
    {
        if ($day instanceof Day) {
            $day = $day->date;
        }
        return $this->date->modify("last day of this month")->format('Y-m-d') === $day->format('Y-m-d');
    }

}