# Usage

Inject `Dot\GeoIP\Service\LocationServiceInterface` into your class.
The ConfigProvider of the package registers it.

```php
<?php

declare(strict_types=1);

namespace App\Example\Service;

use Dot\GeoIP\Data\CityData;
use Dot\GeoIP\Data\ContinentData;
use Dot\GeoIP\Data\CountryData;
use Dot\GeoIP\Data\LocationData;
use Dot\GeoIP\Data\OrganizationData;
use Dot\GeoIP\Service\LocationServiceInterface;

class ExampleService
{
    public function __construct(
        private LocationServiceInterface $locationService,
    ) {
    }

    // Contains isEuMember, isoCode, name and error.
    public function getCountry(string $ipAddress): CountryData
    {
        return $this->locationService->getCountry($ipAddress);
    }

    // Contains name and error.
    public function getCity(string $ipAddress): CityData
    {
        return $this->locationService->getCity($ipAddress);
    }

    // Contains code, name and error.
    public function getContinent(string $ipAddress): ContinentData
    {
        return $this->locationService->getContinent($ipAddress);
    }

    // Contains asn, name and error.
    public function getOrganization(string $ipAddress): OrganizationData
    {
        return $this->locationService->getOrganization($ipAddress);
    }

    // Contains all the above, and the estimated coordinates and time zone.
    public function getLocation(string $ipAddress): LocationData
    {
        return $this->locationService->getLocation($ipAddress);
    }
}
```

## Errors

`getCountry()`, `getCity()`, `getContinent()` and `getOrganization()` do not throw.
When the lookup fails, they return an object with the `error` property set to the error message.
For example, the lookup fails when the database is missing or the address is not in the database.

`getLocation()` throws when the IP address is not valid, when the `city` database is missing, or when the address is not in the `city` database.
Catch `Throwable` around this call.

## Privacy

Before a lookup, the service sets the last part of the IP address to zero.
For example, `1.2.3.4` becomes `1.2.3.0`, and `::ffff:1.2.3.4` becomes `::ffff:1.2.3.0`.
For other IPv6 addresses, the service sets the last group to zero.
