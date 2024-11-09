<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 9. 11. 2024
 * Time: 16:44
 */

namespace Tito10047\Calendar\Interface;

use Tito10047\Calendar\Enum\Day;

interface MonthRendererInterface
{
    /**
     * @param DateTimeImmutable $month
     * @param Day[] $headers
     * @param \DateTimeImmutable[][] ...$dayRows
     * @return string
     */
    public function renderMonth(\DateTimeImmutable $month, array $headers, array $dayRows): string;
}