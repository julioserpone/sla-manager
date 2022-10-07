<img src="https://github.com/julioserpone/sla-manager/raw/HEAD/.github/assets/logo.svg?" width="50%" alt="Logo for SLA Timer">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/julioserpone/sla-manager.svg?style=flat&labelColor=2c353c)](https://packagist.org/packages/julioserpone/sla-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/julioserpone/sla-manager.svg?style=flat&labelColor=2c353c)](https://packagist.org/packages/julioserpone/sla-manager)
![GitHub Actions](https://github.com/julioserpone/sla-manager/actions/workflows/main.yml/badge.svg)

A PHP package to calculate and track Service Level Agreement completion times.

Inspired from the [sla-timer](https://github.com/sifex/sla-timer) pack.

### Features

- 🕚 Easy schedule building
- ‼️ Defined breaches
- 🏝 Holiday & Paused Durations

---


<a href="https://twitter.com/julioserpone/status/1578441324558135298">
<img src="https://github.com/julioserpone/sla-manager/raw/HEAD/.github/assets/hiring.svg?" alt="Hi, I'm Julio & I'm currently looking for a Laravel job. Please reach out to me via twitter, or click this link." height="49">
</a>


---
## Installation

You can install the `sla-manager` via composer:

```bash
composer require julioserpone/sla-manager
```

## Getting Started

The best place to get started with SLA timer is to head over to the [✨ SLA Timer Getting Started Documentation](https://julioserpone.github.io/sla-manager/guide/getting_started). 

## Example Usage

To create a new SLA Timer, we can start by defining our SLA Schedule:

```php
require 'vendor/autoload.php';

use JulioSerpone\SlaManager\SLA;
use JulioSerpone\SlaManager\SLABreach;
use JulioSerpone\SlaManager\SLASchedule;

/**
 * Create a new SLA between 9am and 5:30pm weekdays
 */
$sla = SLA::fromSchedule(
    SLASchedule::create()->from('09:00:00')->to('17:30:00')
        ->onWeekdays()
);
```

We can define out breaches by calling the `addBreaches` method on our SLA

```php
/**
 * Define two breaches, one at 24 hours, and the next at 100 hours
 */
$sla->addBreaches([
    new SLABreach('First Response', '24h'),
    new SLABreach('Resolution', '100h'),
]);
```

Now that our **SLA Schedule** and **SLA Breaches** are defined, all we have to do is give our _subject_ "creation time" – or our SLA start time – to either the `status` method, or the `duration` method.

```php
// Given the time now is 14:00:00 29-07-2022
$status = $sla->status('05:35:40 25-07-2022'); // SLAStatus
$status->breaches; // [SLABreach] [0: { First Response } ]

$duration = $sla->duration('05:35:40 25-07-2022'); // CarbonInterval
$duration->forHumans(); // 1 day 15 hours
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [Alex (owner)](https://github.com/sifex)
-   [Julio Serpone](https://github.com/julioserpone)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
