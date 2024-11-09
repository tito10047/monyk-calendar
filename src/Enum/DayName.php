<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 9. 11. 2024
 * Time: 15:53
 */

namespace Tito10047\Calendar\Enum;

enum DayName:int
{
    case Monday=1;
    case Tuesday=2;
    case Wednesday=3;
    case Thursday=4;
    case Friday=5;
    case Saturday=6;
    case Sunday=7;

    public static function fromDate(\DateTimeImmutable $date):self
    {
        return match ((int)$date->format('N')) {
            1 => self::Monday,
            2 => self::Tuesday,
            3 => self::Wednesday,
            4 => self::Thursday,
            5 => self::Friday,
            6 => self::Saturday,
            7 => self::Sunday,
            default => throw new \LogicException('Unexpected match value'),
        };
    }

    public function getShortName():string
    {
        return match ($this) {
            self::Monday => 'Mon',
            self::Tuesday => 'Tue',
            self::Wednesday => 'Wed',
            self::Thursday => 'Thu',
            self::Friday => 'Fri',
            self::Saturday => 'Sat',
            self::Sunday => 'Sun',
        };
    }

    public static function all(DayName $startDay = self::Monday):array
    {
        $days = DayName::cases();
        $start = array_search($startDay,$days);
        return array_merge(array_slice($days,$start),array_slice($days,0,$start));
    }

    public function getDayNumber():int
    {
        return $this->value;
    }
}