# dot-geoip
DotKernel component based on Maxmind's [geoip2/geoip2](https://github.com/maxmind/GeoIP2-php) package, using their [free GeoLite2 databases](https://dev.maxmind.com/geoip/geoip2/geolite2/) to provide geographical details about an IP address.

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-geoip)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-geoip)](https://github.com/dotkernel/dot-geoip/blob/3.0/LICENSE.md)


## Install

You can install this library by running the following command:
```bash
$ composer require dotkernel/dot-geoip
```

Using this library in your application is done in two steps:

### Step 1: Download/Update your local distribution of GeoList2 databases
Database files can be synchronized by using the [dotkernel/dot-console](https://github.com/dotkernel/dot-console) command provided with this library.
If your application does not use DotConsole yet, you can add it by consulting it's [README](https://github.com/dotkernel/dot-console/blob/master/README.md) file.

Next, add `Dot\GeoIP\ConfigProvider::class,` to your application's `config/config.php` file.

Once you have DotConsole in your application, continue setting up DotGeoip: 
* copy the command from `config/autoload/console.global.php` to your application's `config/autoload/console.global.php`
* copy `config/autoload/geoip.global.php` to your application's `config/autoload/geoip.global.php`
* copy `data/geoip` to your application's `data` directory - this is where the database file(s) will be downloaded.

After this, running `php bin/console.php geoip:synchronize --database=<database>` from a console will download the latest copy of MaxMind's GeoLite2 database file for the specified database(s).
Replace `<database>` with one of the following string: `asn`, `city`, `country` to download a specific database file OR with `all` to download all three database files at once.
You can get help for this command by running `php bin/console.php geoip:synchronize --help`.


### Step 2: Include `Dot\GeoIP\Service\LocationService` in your code
Below is an example implementation of using DotGeoip to retrieve information about an IP address.

```php
<?php

declare(strict_types=1);

namespace Api\Example\Service;

use Dot\GeoIP\Service\LocationService;
use Dot\GeoIP\Data\LocationData;

/**
 * Class MyService
 * @package Api\Example\Service
 */
class MyService
{
    /** @var LocationService $locationService */
    protected $locationService;

    /**
     * MyService constructor.
     * @param LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * @param string $ipAddress
     */
    public function myMethod(string $ipAddress)
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
        } catch (\Exception $exception) {
            // handle errors
        }
    }
}
```
