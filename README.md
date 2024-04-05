# dot-geoip

DotKernel component based on Maxmind's [geoip2/geoip2](https://github.com/maxmind/GeoIP2-php) package, using [db-ip.com's free GeoLite2 databases](https://db-ip.com/db/) to provide geographical details about an IP address.

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-geoip)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-geoip/3.6.0)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-geoip)](https://github.com/dotkernel/dot-geoip/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-geoip)](https://github.com/dotkernel/dot-geoip/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-geoip)](https://github.com/dotkernel/dot-geoip/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-geoip)](https://github.com/dotkernel/dot-geoip/blob/3.0/LICENSE)

[![Build Static](https://github.com/dotkernel/dot-geoip/actions/workflows/static-analysis.yml/badge.svg?branch=3.0)](https://github.com/dotkernel/dot-geoip/actions/workflows/static-analysis.yml)

[![SymfonyInsight](https://insight.symfony.com/projects/f1468fbc-7c76-48d3-9ca7-0f4d135c0ff3/big.svg)](https://insight.symfony.com/projects/f1468fbc-7c76-48d3-9ca7-0f4d135c0ff3)

## Install

You can install this library by running the following command:

    composer require dotkernel/dot-geoip

If your application didn't already use it, the above command also installed [dotkernel/dot-cli](https://github.com/dotkernel/dot-cli).
In this case, see it's [README](https://github.com/dotkernel/dot-cli/blob/3.0/README.md) file on how to use it.

Copy config file `vendor/dotkernel/dot-geoip/config/autoload/geoip.global.php` into your application's `config/autoload` directory.

Register the library's ConfigProvider by adding the following line to your application's `config/config.php` file:

    Dot\GeoIP\ConfigProvider::class,

Register the library's synchronizer command by adding the following line to your application's `config/autoload/cli.global.php` file under the `commands` array key:

    Dot\GeoIP\Command\GeoIpCommand::getDefaultName() => Dot\GeoIP\Command\GeoIpCommand::class,

## Manage GeoLite2 database

You can download/update a specific GeoLite2 database, by running the following command:

    php bin/cli.php geoip:synchronize -d {DATABASE}

Where _{DATABASE}_ takes one of the following values: `asn`, `city`, `country`.

You can download/update all GeoLite2 databases at once, by running the following command:

    php bin/cli.php geoip:synchronize

The output should be similar to the below, displaying per row: `database identifier`: `previous build datetime` -> `current build datetime`.

    asn: n/a -> 2021-07-01 02:09:34
    city: n/a -> 2021-07-01 02:09:20
    country: n/a -> 2021-07-01 02:05:12

Get help for this command by running `php bin/cli.php help geoip:synchronize`.

**Tip**: If you setup the synchronizer command as a cronjob, you can add the `-q|--quiet` option, and it will output data only if an error has occurred.

## Usage

Below is an example implementation of using DotGeoip to retrieve information about an IP address.

    <?php
    
    declare(strict_types=1);
    
    namespace Api\Example\Service;
    
    use Dot\GeoIP\Service\LocationServiceInterface;
    use Throwable;
    
    /**
     * Class ExampleService
     * @package Api\Example\Service
     */
    class ExampleService
    {
        protected LocationServiceInterface $locationService;
    
        /**
         * ExampleService constructor.
         * @param LocationServiceInterface $locationService
         */
        public function __construct(LocationServiceInterface $locationService)
        {
            $this->locationService = $locationService;
        }
    
        /**
         * @param string $ipAddress
         * @return object
         */
        public function myMethod(string $ipAddress): object
        {
            try {
                // You can use any of the below methods:
                
                // Get CountryData which includes isEuMember, isoCode and name
                return $this->locationService->getCountry($ipAddress);
                
                // Get ContinentData which includes code and name
                return $this->locationService->getContinent($ipAddress);
                
                // Get OrganizationData which includes asn and name
                return $this->locationService->getOrganization($ipAddress);
    
                // Get LocationData which includes all of the above + estimated coordinates + timezone
                return $this->locationService->getLocation($ipAddress);
            } catch (Throwable $exception) {
                // handle errors
            }
        }
    }
