<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 9. 11. 2024
 * Time: 16:45
 */

namespace Tito10047\Calendar\Renderer;


class WeekRowRenderer implements \Tito10047\Calendar\Interface\WeekRowRendererInterface
{


    public function __construct(
        private \Tito10047\Calendar\Interface\DayRendererInterface $dayRenderer,
    )
    {
    }


    public function renderWeekRow(int $month, \DateTimeImmutable ...$days): string
    {
        $html = "<tr>";
        foreach($days as $day){
            $html .= "<td>";
            $html .= $this->dayRenderer->renderDay($day,[]);
            $html .= "</td>";
        }
        $html .= "</tr>";
        return $html;
    }
}