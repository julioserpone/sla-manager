<?php

namespace JulioSerpone\SlaManager;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use JulioSerpone\SlaManager\Traits\CanComposeSLASchedules;
use JulioSerpone\SlaManager\Traits\HasTimezone;

class SLASchedule
{
    use HasTimezone;
    use CanComposeSLASchedules;

    public string $valid_from = '1970-01-01 00:00:01';

    /** @var Carbon[] */
    public array $excluded_dates = [];

    /** @var CarbonPeriod[] */
    public array $excluded_time_periods = [];

    public function effectiveFrom(string $date): self
    {
        $this->valid_from = $date;

        return $this;
    }
}
