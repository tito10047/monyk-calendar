<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 9. 11. 2024
 * Time: 15:54
 */

namespace Tito10047\Calendar;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tito10047\Calendar\Enum\CalendarType;
use Tito10047\Calendar\Enum\DayName;
use Tito10047\Calendar\Interface\DayNameRendererInterface;
use Tito10047\Calendar\Interface\DayRendererInterface;
use Tito10047\Calendar\Interface\EventRendererInterface;
use Tito10047\Calendar\Interface\MonthRendererInterface;
use Tito10047\Calendar\Interface\WeekRowRendererInterface;

class Calendar
{
    public TranslatorInterface $translator;
    public string $translationDomain = 'calendar';
    private DayNameRendererInterface $dayNameRenderer;
    private WeekRowRendererInterface $weekRowRenderer;
    private DayRendererInterface $dayRenderer;
    private MonthRendererInterface $monthRenderer;
    private EventRendererInterface $eventRenderer;


    public function __construct(
        private \DateTimeImmutable $date,
        private CalendarType $type = CalendarType::Monthly,
        private DayName $startDay = DayName::Monday,
        /** @var \DateTimeImmutable[] $disabledDays */
        private array $disabledDays = []
    ) {
        $this->translator = new Translator();
        $this->eventRenderer = new \Tito10047\Calendar\Renderer\EventRenderer(
            $this->translator,
            $this->translationDomain
        );
        $this->dayNameRenderer = new \Tito10047\Calendar\Renderer\DayNameRenderer(
            $this->translator,
            $this->translationDomain
        );
        $this->dayRenderer = new \Tito10047\Calendar\Renderer\DayRenderer(
            $this->eventRenderer,
            $this->type, $this->translator, $this->translationDomain
        );
        $this->weekRowRenderer = new \Tito10047\Calendar\Renderer\WeekRowRenderer(
            $this->dayRenderer
        );
        $this->monthRenderer = new \Tito10047\Calendar\Renderer\MonthRenderer(
            $this->dayNameRenderer,
            $this->weekRowRenderer
        );
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setTranslator(TranslatorInterface $translator, string $translationDomain = "calendar"): self
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        return $this;
    }

    public function disableDaysRange(\DateTimeImmutable $from = null, \DateTimeImmutable $to = null): self
    {
        if (!$from) {
            $from = $this->date->modify('first day of this month')->modify('monday this week');
        }
        if (!$to) {
            $to = $this->date->modify('last day of this month')->modify('sunday this week');
        }
        $current = clone $from;
        $days = [];
        while ($current <= $to) {
            $days[] = $current;
            $current = $current->modify('+1 day');
        }
        $calendar = clone $this;
        $calendar->disabledDays = array_unique(array_merge($calendar->disabledDays, $days), SORT_REGULAR);
        return $calendar;
    }

    public function disableDays(\DateTimeImmutable ...$days): self
    {
        $calendar = clone $this;
        $calendar->disabledDays = array_unique(array_merge($calendar->disabledDays, $days), SORT_REGULAR);
        return $calendar;
    }

    public function disableDayName(DayName ...$dayNames): static
    {
        $from = $this->date->modify('first day of this month')->modify('monday this week');
        $to = $this->date->modify('last day of this month')->modify('sunday this week');

        $days = [];
        foreach ($dayNames as $dayName) {
            $current = clone $from;
            while ($current <= $to) {
                if (DayName::fromDate($current) === $dayName) {
                    $days[] = $current;
                }
                $current = $current->modify('+1 day');
            }
        }
        $calendar = clone $this;
        $calendar->disabledDays = array_unique(array_merge($calendar->disabledDays, $days), SORT_REGULAR);
        return $calendar;
    }

    public function disableWeek(int $weekNum): self
    {
        $from = $this->date->modify('first day of this month')->modify('monday this week');
        $to = $this->date->modify('last day of this month')->modify('sunday this week');

        $current = clone $from;
        $days = [];
        while ($current <= $to) {
            if ((int)$current->format('W') === $weekNum) {
                $days[] = $current;
            }
            $current = $current->modify('+1 day');
        }
        $calendar = clone $this;
        $calendar->disabledDays = array_unique(array_merge($calendar->disabledDays, $days), SORT_REGULAR);
        return $calendar;
    }


    public function nextMonth(): self
    {
        $calendar = clone $this;
        $calendar->date = $this->date->modify('+1 month');
        $this->disabledDays = [];
        return $calendar;
    }

    public function prevMonth(): self
    {
        $calendar = clone $this;
        $calendar->date = $this->date->modify('-1 month');
        return $calendar;
    }


    public function setDayRenderer(DayRendererInterface $dayRenderer): self
    {
        $this->dayRenderer = $dayRenderer;
        return $this;
    }

    public function setDayNameRenderer(DayNameRendererInterface $dayNameRenderer): self
    {
        $this->dayNameRenderer = $dayNameRenderer;
        return $this;
    }

    public function setWeekRowRenderer(WeekRowRendererInterface $weekRowRenderer): self
    {
        $this->weekRowRenderer = $weekRowRenderer;
        return $this;
    }

    public function setMonthRenderer(MonthRendererInterface $monthRenderer): self
    {
        $this->monthRenderer = $monthRenderer;
        return $this;
    }

    public function setEventRenderer(EventRendererInterface $eventRenderer): self
    {
        $this->eventRenderer = $eventRenderer;
        return $this;
    }

    public function setRenderer(
        DayRendererInterface&DayNameRendererInterface&WeekRowRendererInterface&MonthRendererInterface&EventRendererInterface $renderer
    ): self {
        $this->setDayRenderer($renderer);
        $this->setDayNameRenderer($renderer);
        $this->setWeekRowRenderer($renderer);
        $this->setMonthRenderer($renderer);
        $this->setEventRenderer($renderer);
        return $this;
    }

    /**
     * @return \Tito10047\Calendar\Day[][]
     */
    public function getDaysTable(): array
    {
        $days = $this->type->getDays($this->date, $this->startDay);
        $rows = [];
        while (count($days) > 0) {
            $row = [];
            $weekNum = (int)$days[0]->format('W');
            for ($i = 0; $i < 7 and count($days) > 0; $i++) {
                $day = array_shift($days);
                $row[] = new \Tito10047\Calendar\Day(
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

    public function render(): string
    {
        $daysTable = $this->getDaysTable();
        return $this->monthRenderer->renderMonth($this->date, DayName::all($this->startDay), $daysTable);
    }

    public function isDayDisabled(\DateTimeImmutable $day)
    {
        foreach($this->disabledDays as $disabledDay){
            if($disabledDay->format('Y-m-d') === $day->format('Y-m-d')){
                return true;
            }
        }
        return false;
    }


}