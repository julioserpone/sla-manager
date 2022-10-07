<?php

namespace JulioSerpone\SlaManager\Traits;

use JulioSerpone\SlaManager\SLASchedule;

trait HasTimezone
{
    public string $timezone = 'UTC';

    public function setTimezone(string $timezone): SLASchedule
    {
        $this->timezone = $timezone;

        return $this;
    }
}
