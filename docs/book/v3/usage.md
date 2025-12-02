# Usage

Below is an example implementation of using DotGeoip to retrieve information about an IP address.

```php
class ExampleService
{
    public function __construct(
        private \Dot\GeoIP\Service\LocationServiceInterface $locationService
    ) {
    }

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
