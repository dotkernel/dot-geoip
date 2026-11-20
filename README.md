# dot-geoip

Dotkernel component to provide geographical details about an IP address

> dot-geoip is a wrapper on top of [maxmind/GeoIP2-php](https://github.com/maxmind/GeoIP2-php)

## Documentation

Documentation is available at: https://docs.dotkernel.org/dot-geoip/.

## Versions

| Version | Branch | PHP versions |
| --- | --- | --- |
| 4.x | `4.0` | 8.3, 8.4, 8.5, 8.6 |
| 3.x | `3.0` | 8.2, 8.3, 8.4, 8.5 |

To upgrade from 3.x, read the [upgrade guide](https://docs.dotkernel.org/dot-geoip/v4/upgrading/).

## Badges

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-geoip)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-geoip/4.0.0)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-geoip)](https://github.com/dotkernel/dot-geoip/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-geoip)](https://github.com/dotkernel/dot-geoip/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-geoip)](https://github.com/dotkernel/dot-geoip/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-geoip)](https://github.com/dotkernel/dot-geoip/blob/4.0/LICENSE)

[![Build Static](https://github.com/dotkernel/dot-geoip/actions/workflows/continuous-integration.yml/badge.svg?branch=4.0)](https://github.com/dotkernel/dot-geoip/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-geoip/graph/badge.svg?token=K473P7MDZ4)](https://codecov.io/gh/dotkernel/dot-geoip)
[![PHPStan](https://github.com/dotkernel/dot-geoip/actions/workflows/static-analysis.yml/badge.svg?branch=4.0)](https://github.com/dotkernel/dot-geoip/actions/workflows/static-analysis.yml)

## Install

Install this library by running the following command:

```shell
composer require dotkernel/dot-geoip
```

If your application does not use [dotkernel/dot-cli](https://github.com/dotkernel/dot-cli) yet, this command also installs it.
In this case, read its [README](https://github.com/dotkernel/dot-cli/blob/3.0/README.md) file to learn how to use it.

Copy the config file `vendor/dotkernel/dot-geoip/config/autoload/geoip.global.php` into the `config/autoload` directory of your application.

Register the ConfigProvider of the library.
Add the following line to the `config/config.php` file of your application:

```php
Dot\GeoIP\ConfigProvider::class,
```

Register the synchronizer command of the library.
Add the following line under the `commands` array key in the `config/autoload/cli.global.php` file of your application:

```php
'geoip:synchronize' => Dot\GeoIP\Command\GeoIpCommand::class,
```

## Manage the databases

You can download or update one database by running the following command:

```shell
php ./bin/cli.php geoip:synchronize -d {DATABASE}
```

Where _{DATABASE}_ takes one of the following values: `asn`, `city`, `country`.

You can download or update all the configured databases at once by running the following command:

```shell
php ./bin/cli.php geoip:synchronize
```

For each database, the output shows the database identifier, the previous build date and the current build date:

```text
asn: n/a -> 2026-11-01 02:52:43
city: n/a -> 2026-11-01 02:48:12
country: n/a -> 2026-11-01 02:47:39
```

Get help for this command by running `php ./bin/cli.php help geoip:synchronize`.

If a download fails, the old database file stays unchanged and the command returns exit code `1`.
If you run the command as a cron job, check its exit code.

## Usage

Below is an example that uses dot-geoip to get information about an IP address.

```php
<?php

declare(strict_types=1);

namespace App\Example\Service;

use Dot\GeoIP\Data\CountryData;
use Dot\GeoIP\Data\LocationData;
use Dot\GeoIP\Service\LocationServiceInterface;

class ExampleService
{
    public function __construct(
        private LocationServiceInterface $locationService,
    ) {
    }

    // Does not throw. On failure, the "error" property contains the message.
    // getContinent(), getCity() and getOrganization() work the same way.
    public function getCountry(string $ipAddress): CountryData
    {
        return $this->locationService->getCountry($ipAddress);
    }

    // Contains continent, country, city, organization, coordinates and time zone.
    // Throws when the lookup fails.
    public function getLocation(string $ipAddress): ?LocationData
    {
        try {
            return $this->locationService->getLocation($ipAddress);
        } catch (\Throwable) {
            return null;
        }
    }
}
```
