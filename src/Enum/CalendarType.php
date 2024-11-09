<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 9. 11. 2024
 * Time: 15:50
 */

namespace Tito10047\Calendar\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tito10047\Calendar\Config;

enum CalendarType
{
    case Monthly;
    case Weekly;
    case WorkWeek;

    public function getDayName(
        \DateTimeImmutable $date,
        ?TranslatorInterface $translator = null,
        ?string $translationDomain = null
    ): string
    {
        $dayName = $date->format('D');
        $monthName = $date->format('M');
        if ($translator) {
            $dayName = $translator->trans($dayName, [], $translationDomain);
            $monthName = $translator->trans($monthName, [], $translationDomain);
        }
        $dayNum = $date->format('j');
        return match ($this) {
            self::Monthly => $dayNum,
            self::Weekly,self::WorkWeek => "{$dayName} {$dayNum} {$monthName}",
        };
    }

    public function getDays(\DateTimeImmutable $day, Day $firstDay):array
    {
        $firstDayDate = $day->modify(match ($this) {
            self::Monthly => 'first day of this month',
            self::Weekly => 'monday this week',
            self::WorkWeek => 'monday this week',
        });
        $lastDayDate = $day->modify(match ($this) {
            self::Monthly => 'last day of this month',
            self::Weekly => 'sunday this week',
            self::WorkWeek => 'friday this week',
        });
        $firstDayDate = $firstDayDate->modify("monday this week");
        $lastDayDate = $lastDayDate->modify("sunday this week");
        if ($this!==CalendarType::Monthly){
            if ($firstDay==Day::Sunday) {
                $firstDayDate = $firstDayDate->modify("-7 day");
                $lastDayDate = $lastDayDate->modify("-7 day");
            }
            $dayNumber = $firstDay->getDayNumber()-1;
            $firstDayDate = $firstDayDate->modify("+{$dayNumber} days");
            $lastDayDate = $lastDayDate->modify("+{$dayNumber} days");
        }
        $days = [];
        $currentDay = $firstDayDate;
        while ($currentDay<=$lastDayDate){
            $days[] = $currentDay;
            $currentDay = $currentDay->modify('+1 day');
        }
        return $days;
    }
}