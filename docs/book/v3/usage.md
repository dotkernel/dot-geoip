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
```
