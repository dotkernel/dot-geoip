# dot-geoip
DotKernel component based on Maxmind's [geoip2/geoip2](https://github.com/maxmind/GeoIP2-php) package, using their [free GeoLite2 databases](https://dev.maxmind.com/geoip/geoip2/geolite2/) to provide geographical details about an IP address.

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-geoip)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-geoip)](https://github.com/dotkernel/dot-geoip/blob/3.0/LICENSE.md)


## Install

You can install this library by running the following command:
```bash
$ composer require dotkernel/dot-geoip
```

## Usage

### Step 1: Install/Update your local distribution of GeoList2 databases
Database files can be synchronized by using the [dotkernel/dot-console](https://github.com/dotkernel/dot-console) command provided with this library.
If your application does not use DotConsole yet, you can add it by following it's [README](https://github.com/dotkernel/dot-console/blob/master/README.md) file.

Add `Dot\GeoIP\ConfigProvider::class,` to your application's `config/config.php` file.

Once you have DotConsole installed in your application, continue setting up DotGeoip by locating in this package: 
* `config/autoload/console.global.php` and copy the console command called `geoip:synchronize` to your application's `config/autoload/console.global.php`
* `config/autoload/geoip.global.php` and copy it to your application's `config/autoload/` directory.

#### Updating all databases at once
Running the command `php bin/console.php geoip:synchronize` will install/update all (ASN, City and Country) databases.

#### Updating specific databases
Running the command `php bin/console.php geoip:synchronize --database=<identifier>` after replacing `<identifier>` will install/update only the database corresponding to the specified identifier.
The database identifiers are the following: `asn`, `city` and `country`.

#### Keep your databases updated by setting up a cron
Database updates are released on the 1st of every month, so we recommend setting up a monthly cronjob (the 2nd of the month should be fine).
For the update process, you can use either of the above described commands.
Additionally, you can specify an extra parameter `-q` or `--quiet` if in order to omit output messages.


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
