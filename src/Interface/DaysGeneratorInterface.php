<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 10. 11. 2024
 * Time: 8:10
 */

namespace Tito10047\Calendar\Interface;

use Tito10047\Calendar\Enum\DayName;

interface DaysGeneratorInterface
{
    public function getDays(\DateTimeImmutable $day, DayName $firstDay):array;
}
