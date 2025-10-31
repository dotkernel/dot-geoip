# Usage

Below is an example implementation of using DotGeoip to retrieve information about an IP address.

```php
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
       return $this->locationService->getCountry($ipAddress); // Returns an instance of Dot\GeoIP\Data\CountryData
       
       return $this->locationService->getCity($ipAddress); // Return instance of Dot\GeoIP\Data\CityData
       
       return $this->locationService->getContinent($ipAddress) // Returns an instance of Dot\GeoIP\Data\ContinentData
       
       return $this->locationService->getOrganization($ipAddress) // Returns an instance of Dot\GeoIP\Data\OrganizationData
       
       return $this->locationService->getLocation($ipAddress) // Returns an instance of Dot\GeoIP\Data\LocationData
    }
}
```
