<?php

namespace Tito10047\Calendar\Interface;

interface DayDataLoaderInterface
{
    public function load(\DateTimeImmutable $from, \DateTimeImmutable $to);

    public function getData(\DateTimeImmutable $date): array;
}