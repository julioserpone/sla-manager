<?php

namespace JulioSerpone\SlaManager\Agenda;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use JulioSerpone\SlaManager\Interfaces\AgendaInterface;

class Weekly implements AgendaInterface
{
    /** @var array<int, array{0: string, 1: string}> */
    public array $time_periods = [];

    /** @var string[] */
    public array $days = [];

    public function addTimePeriod(string $start_time, string $end_time): Weekly
    {
        $this->time_periods[] = [$start_time, $end_time];

        return $this;
    }

    /**
     * @param  array<int, array{0: string, 1: string}>  $periods
     */
    public function addTimePeriods(...$periods): Weekly
    {
        collect([$periods])->flatten(1)->each(function ($period) {
            $this->addTimePeriod($period[0], $period[1]);
        });

        return $this;
    }

    public function clearTimePeriods(): Weekly
    {
        $this->time_periods = [];

        return $this;
    }

    /**
     * @param  array<int, string>  $days
     */
    public function setDays(array $days): Weekly
    {
        $this->days = [];

        foreach ($days as $day) {
            $this->days[] = Carbon::parse($day)->dayName;
        }

        return $this;
    }

    /**
     * This is a pretty hacky workaround, but in order for our spatie/period package to calculate the correct overlap,
     * we need to generate a full number of periods surrounding/covering our subject period, because Carbon is not
     * capable of generating a full infinite series of 'Fridays 9am to 5pm', so we have to do the heavy lifting for it
     *
     * @return array<int, array{0: CarbonInterface, 1: CarbonInterface}>
     */
    public function toPeriods(CarbonPeriod $subject_period): array
    {
        $start_date = $subject_period->start;
        $end_date = $subject_period->end;

        if ($start_date === null || $end_date === null) {
            return [];
        }

        /**
         * The following allows the CarbonPeriod class to take into account the endDate if it has a time in H:i:s less than the starDate time:
         *
         * Example:
         * Scenery -> Assigning the following schedule...
         * SLASchedule::create()->from('08:00:00')->to('17:00:00')->everyDay()
         *
         * With the following dates:
         * start_date = 2022-10-18 16:00:00 (Y-m-d H:i:s)
         * now = 2022-10-19 08:00:01 (Y-m-d H:i:s)
         *
         * By not implementing this statement, CarbonPeriod may omit the end_date because the time on that day is less than the time on the start date. The value returned in this scenario would be 1 hour.
         * With the following fix, CarbonPeriod will fully assume the end date but considering that the time to be evaluated must be within the range defined in the calendar
         *
         * The correct result is 1 hour and 1 second
         */
        $end_date = Carbon::parse($start_date->format('H:i:s'))->greaterThan($end_date->format('H:i:s')) ? $end_date->format('Y-m-d').'23:59:59' : $end_date;

        $new_period = CarbonPeriod::create(Carbon::parse($start_date)->startOfDay(), Carbon::parse($end_date)->startOfDay())
            ->setDateInterval(CarbonInterval::day());

        return collect(iterator_to_array($new_period))
            ->filter(function (CarbonInterface $day) {
                return collect($this->days)->contains($day->dayName);
            })
            ->flatMap(function (CarbonInterface $day) {
                return collect($this->time_periods)
                    ->flatMap(function (array $t) use ($day) {
                        $start = $day->clone()->setTimeFromTimeString($t[0]);
                        $end = $day->clone()->setTimeFromTimeString($t[1]);

                        if ($end->lessThanOrEqualTo($start)) {
                            // Overnight period: keep it as a single period spanning midnight, the
                            // daily overlap logic in SLA::calculate clips it per day
                            return [[$start, $end->clone()->addDay()]];
                        }

                        return [[$start, $end]];
                    });
            })->toArray();
    }
}
