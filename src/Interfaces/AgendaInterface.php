<?php

namespace JulioSerpone\SlaManager\Interfaces;

use Carbon\CarbonPeriod;

interface AgendaInterface
{
    /**
     * @param  CarbonPeriod  $subject_period
     * @return CarbonPeriod[]
     */
    public function toPeriods(CarbonPeriod $subject_period): array;
}
