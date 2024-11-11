<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 9. 11. 2024
 * Time: 21:20
 */

namespace Tito10047\Calendar;

final class Day
{

    public function __construct(
        public readonly \DateTimeImmutable $date,
        public readonly bool $ghost,
        public readonly bool $today,
        public readonly bool $enabled,
    )
    {
    }
}