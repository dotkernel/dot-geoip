# Changelog

## 4.0.0 - TBD

### Changed

* Supported PHP versions: 8.3, 8.4, 8.5 and 8.6
* Upgraded `phpunit/phpunit` to `^12.5`
* Raised the PHPStan level from 5 to 8
* Declared `symfony/console`, `laminas/laminas-stdlib` and `maxmind-db/reader` as direct requirements
* `GeoIpCommand::configure()` and `GeoIpCommand::execute()` are `protected`
* `--memory-limit` needs a value, and accepts `-1`, `K` and lower-case units
* The factories and `LocationService` throw `InvalidArgumentException` or `RuntimeException` instead of `Exception`
* `getLocation()` reads the `city` database once

### Added

* Documentation for v4
* Upgrade guide from 3.x

### Deprecated

* Nothing

### Removed

* Support for PHP 8.2

### Fixed

* A failed download no longer replaces the database with an empty file. The command returns exit code `1`
* The command validates the new database before it replaces the old one
* `all` synchronizes only the configured databases
* `-d` with a database that is not configured gives a clear error
* `Client` and `Extractor` check the result of each file and download operation
* `LocationData::exchangeArray()` accepts the output of `getArrayCopy()`
* `CountryData::exchangeArray()` sets `isEuMember` to `false` when the key is missing
* `obfuscateIpAddress()` keeps the IPv4 part of an IPv4-mapped IPv6 address
* The factory gives a clear error when a database entry in the config is not an array
* The README registered the command with `getDefaultName()`, which does not exist in `symfony/console` 8

## 3.11.0 - 2025-12-02

### Changed

* Download the databases with `ext-curl` and extract them with `ext-zlib`
* Renamed `LocationServiceInterface::getDatabasePath()` to `getRealFilePath()`
* Renamed `LocationServiceInterface::getDatabaseSource()` to `getTempFilePath()`

### Added

* `LocationServiceInterface::getDatabaseSourceUrl()`
* The `--memory-limit` option (#50)

### Deprecated

* Nothing

### Removed

* `guzzlehttp/guzzle`
* `laminas/laminas-filter`
* `symfony/filesystem`

### Fixed

* Nothing

## 3.10.1 - 2025-11-18

### Changed

* `GeoIpCommand::$defaultName` is `public` (#48)

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.10.0 - 2025-10-31

### Changed

* Nothing

### Added

* Support for PHP 8.5 (#46)

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.9.0 - 2025-02-24

### Changed

* Replaced Psalm with PHPStan (#44)
* Allowed `psr/container` `^2.0`

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.8.1 - 2025-01-28

### Changed

* Upgraded Psalm to 6.x (#42)

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.8.0 - 2025-01-27

### Changed

* Upgraded `laminas/laminas-coding-standard` to `^3.0`

### Added

* Support for PHP 8.4 (#40)

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.7.2 - 2024-07-04

### Changed

* The README and the documentation mark the package as a wrapper of `maxmind/GeoIP2-php`

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.7.1 - 2024-04-11

### Changed

* Nothing

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.7.0 - 2024-04-11

### Changed

* The lookup methods return data objects
* Updated `mikey179/vfsstream`

### Added

* `CityData` and `LocationServiceInterface::getCity()`
* Documentation for v3

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Nothing

## 3.6.0 - 2024-02-08

### Changed

* Upgraded all dependencies
* Dot\GeoIP\Command\GeoIpCommand.php

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Removed support for PHP 8.1

### Fixed

* Removed 'ZF\Console\Route' from 'DatabaseHandler' with 'Dot\Console\RouteCollector as Route';

## 3.0.1 - 2021-02-15

### Changed

Nothing

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Nothing

### Fixed

* Removed 'ZF\Console\Route' from 'DatabaseHandler' with 'Dot\Console\RouteCollector as Route';

## 3.0.0 - 2021-02-15

### Changed

* Composer 2 compatibility.
* Update composer.json to require version 2 of laminas/laminas-dependency-plugin to be Composer 2 compatible.
* bump laminas/laminas-dependency-plugin ^2.1
* require-dev compatibility with Composer 2

### Added

* Nothing

### Deprecated

* laminas/laminas-dependency-plugin version 1

### Removed

* Nothing

### Fixed

* require-dev compatibility with Composer 2

## 2.0.0 - 2020-01-30

### Changed

* Laminas and Mezzio migration.

### Added

* Nothing

### Deprecated

* Nothing

### Removed

* Support

### Fixed

* Nothing
