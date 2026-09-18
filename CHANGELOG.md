# Changelog

All notable changes to `sla-timer` will be documented in this file

## v1.1.0 - 2026-09-18

## Bug fixes

- Fixed SLA durations dropping the final day's time when the subject ended before the start time-of-day (closes #4 and #6)
- Fixed overnight (midnight-crossing) schedules silently returning zero — `from('22:00:00')->to('02:00:00')` now counts correctly across midnight, and equal from/to times (`09:00:00` -> `09:00:00`) now mean 24/7 coverage

## Performance

- Fixed quadratic runtime for durations over a month with pauses/holidays: a year-long SLA with a holiday on every day previously exhausted 128MB of memory and now calculates in ~30ms; 10-year calculations run in ~90ms
- The hot path no longer constructs `CarbonPeriod` objects per period — `AgendaInterface::toPeriods()` now returns start/end Carbon pairs instead of `CarbonPeriod[]` (**breaking change** for direct consumers)

## Dependencies

- PHP floor raised to `^8.3` (8.1/8.2 are EOL)
- `nesbot/carbon` `^2` → `^3`, `illuminate/collections` → `^11|^12|^13`
- Dev tooling: Pest `^4`, PHPStan `^2`, Pint `^1.30`, spatie/invade `^2`, spatie/pest-plugin-test-time `^2.2`
- npm: vitepress 1.6.4, tailwindcss 4.3.3, vue 3.5.41

## Housekeeping

- Added `phpstan.neon.dist` and fixed all level-8 findings (50 errors, incl. a latent `Weekly::addTimePeriods` flattening bug and dead code)
- CI modernised to first-party actions; test matrix now covers PHP 8.3–8.5
- 28 new tests covering schedules, boundaries, overnight windows, pauses/holidays and breaches (98.4% coverage)

## v1.0.2

- CarbonPeriod class to take into account the endDate if it has a time in H:i:s less than the starDate time

Example:
Scenery -> Assigning the following schedule...
SLASchedule::create()->from('08:00:00')->to('17:00:00')->everyDay()

With the following dates:
start_date = 2022-10-18 16:00:00 (Y-m-d H:i:s)
now = 2022-10-19 08:00:01 (Y-m-d H:i:s)

Without this fix, CarbonPeriod may omit the end_date because the time on that day is less than the time on the start date. The value returned in this scenario would be 1 hour.

Now, CarbonPeriod will fully assume the end date but considering that the time to be evaluated must be within the range defined in the calendar

## v1.0.1

- Determine the percentage of compliance with the SLAs

## v1.0.0

- Initial dev release
- Imported `spatie/period` to help with time overlap problems
- Published documentation to GH Pages