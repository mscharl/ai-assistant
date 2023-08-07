<?php declare(strict_types=1);

namespace App\DTO;

class DaySummaryWithGreeting
{
    public function __construct(public readonly Greeting $greeting, public readonly DaySummary $summary)
    {
    }
}
