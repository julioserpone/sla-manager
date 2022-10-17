<?php

namespace JulioSerpone\SlaManager;

use Carbon\CarbonInterval;

class SLABreach
{
    public string $name = 'Generic Breach';

    public CarbonInterval $breached_after;

    public bool $breached = false;

    public float|int $fulfillment = 0;

    public function __construct(string $name, string $string_duration)
    {
        $this->name = $name;
        $this->breached_after = CarbonInterval::fromString($string_duration);
    }

    public function check(CarbonInterval $current_interval): void
    {
        $this->breached = $current_interval->cascade()->totalSeconds > $this->breached_after->cascade()->totalSeconds;
        $this->setProgress($current_interval);
    }

    public function setProgress(CarbonInterval $current_interval): void
    {
        $start = $current_interval->cascade()->totalSeconds;
        $end = $this->breached_after->cascade()->totalSeconds;

        if ($start > $end)
        {
            $this->fulfillment = 0;
        }
        else
        {
            $this->fulfillment = 100 - (($start * 100) / $end);
        }
    }
}
