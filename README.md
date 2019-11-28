# dot-geoip
DotKernel component based on Maxmind's [geoip2/geoip2](https://github.com/maxmind/GeoIP2-php) package, using their [free GeoLite2 databases](https://dev.maxmind.com/geoip/geoip2/geolite2/) to provide geographical details about an IP address.


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
use Exception;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;

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
     * @return LocationData
     * @throws Exception
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function myMethod(string $ipAddress): LocationData
    {
        return $this->locationService->getDetails($ipAddress);
    }
}
```


Because `GeoIp2\Database\Reader` throws exceptions if a database file is not found or if it is corrupted, you need to wrap the below call in a try-catch statement.
When called with an `$ipAddress = '8.8.8.8'`, `myMethod($ipAddress)` returns an object with the following structure:

```php
Dot\GeoIP\Data\LocationData::__set_state(array(
    'continent' =>
      Dot\GeoIP\Data\ContinentData::__set_state(array(
        'code' => 'NA',
        'name' => 'North America'
    )),
    'country' =>
      Dot\GeoIP\Data\CountryData::__set_state(array(
        'isEuMember' => false,
        'isoCode' => 'US',
        'name' => 'United States'
    )),
    'latitude' => 37.751,
    'longitude' => -97.822,
    'organization' =>
      Dot\GeoIP\Data\OrganizationData::__set_state(array(
        'asn' => 15169,
        'name' => 'Google LLC'
    )),
    'timeZone' => 'America/Chicago'
))
```

The above call can also be chained as `myMethod($ipAddress)->getArrayCopy()`, to retrieve the details as an array:

```php
array (
    'continent' =>
      array (
        'code' => 'NA',
        'name' => 'North America'
    ),
    'country' =>
      array (
        'isEuMember' => false,
        'isoCode' => 'US',
        'name' => 'United States'
    ),
    'latitude' => 37.751,
    'longitude' => -97.822,
    'organization' =>
      array (
        'asn' => 15169,
        'name' => 'Google LLC'
    ),
    'timeZone' => 'America/Chicago'
)
```
