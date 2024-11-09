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
use Tito10047\Calendar\Enum\Day;
use Tito10047\Calendar\Interface\DayNameRendererInterface;
use Tito10047\Calendar\Interface\DayRendererInterface;
use Tito10047\Calendar\Interface\EventRendererInterface;
use Tito10047\Calendar\Interface\HasConfig;
use Tito10047\Calendar\Interface\WeekRowRendererInterface;
use Tito10047\Calendar\Interface\MonthRendererInterface;

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
        private Day $startDay = Day::Monday,
        private ?array $enabledDays = null,
    )
    {
        if ($enabledDays == null){
            $enabledDays = Day::cases();
        }
        $this->enabledDays = $enabledDays;
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
            $this->type,$this->translator, $this->translationDomain
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



    public function nextMonth():self
    {
        $calendar = clone $this;
        $calendar->date = $this->date->modify('+1 month');
        return $calendar;
    }
    public function prevMonth():self
    {
        $calendar = clone $this;
        $calendar->date = $this->date->modify('-1 month');
        return $calendar;
    }


    public function setDayRenderer(DayRendererInterface $dayRenderer):self
    {
        $this->dayRenderer = $dayRenderer;
        return $this;
    }
    public function setDayNameRenderer(DayNameRendererInterface $dayNameRenderer):self
    {
        $this->dayNameRenderer = $dayNameRenderer;
        return $this;
    }
    public function setWeekRowRenderer(WeekRowRendererInterface $weekRowRenderer):self
    {
        $this->weekRowRenderer = $weekRowRenderer;
        return $this;
    }
    public function setMonthRenderer(MonthRendererInterface $monthRenderer):self
    {
        $this->monthRenderer = $monthRenderer;
        return $this;
    }
    public function setEventRenderer(EventRendererInterface $eventRenderer):self
    {
        $this->eventRenderer = $eventRenderer;
        return $this;
    }
    public function setRenderer(DayRendererInterface&DayNameRendererInterface&WeekRowRendererInterface&MonthRendererInterface&EventRendererInterface $renderer):self
    {
        $this->setDayRenderer($renderer);
        $this->setDayNameRenderer($renderer);
        $this->setWeekRowRenderer($renderer);
        $this->setMonthRenderer($renderer);
        $this->setEventRenderer($renderer);
        return $this;
    }

    /**
     * @return \DateTimeImmutable[][]
     */
    public function getDaysTable():array
    {
        $days = $this->type->getDays($this->date, $this->startDay);
        $rows = [];
        while($day = count($days)>0){
            $row = [];
            for ($i = 0; $i < 7 and count($days)>0; $i++) {
                $day = array_shift($days);
                if (in_array(Day::fromDate($day), $this->enabledDays)) {
                    $row[] = $day;
                }
            }
            $rows[] = $row;
        }
        return $rows;
    }

    public function render():string
    {
        $daysTable = $this->getDaysTable();
        return $this->monthRenderer->renderMonth($this->date->format('n'), $this->enabledDays, $daysTable);
    }


}