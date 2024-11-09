<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 9. 11. 2024
 * Time: 21:20
 */

namespace Tito10047\Calendar;

readonly class Day
{

    public function __construct(
        public \DateTimeImmutable $date,
        public bool $ghost,
        public bool $today,
        public bool $enabled,
    )
    {
    }
}