<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Data;

use Dot\GeoIP\Data\CityData;
use Dot\GeoIP\Data\ContinentData;
use Dot\GeoIP\Data\CountryData;
use Dot\GeoIP\Data\LocationData;
use Dot\GeoIP\Data\OrganizationData;
use DotTest\GeoIP\CommonTrait;
use PHPUnit\Framework\TestCase;

class LocationDataTest extends TestCase
{
    use CommonTrait;

    public function testDefaultValues(): void
    {
        $locationData = new LocationData();
        $this->assertNull($locationData->getContinent());
        $this->assertNull($locationData->getCountry());
        $this->assertNull($locationData->getCity());
        $this->assertNull($locationData->getOrganization());
        $this->assertNull($locationData->getLatitude());
        $this->assertNull($locationData->getLongitude());
        $this->assertNull($locationData->getTimeZone());
    }

    public function testAccessors(): void
    {
        $locationData = new LocationData();
        $locationData->setContinent(new ContinentData());
        $this->assertContainsOnlyInstancesOf(LocationData::class, [$locationData]);
        $this->assertInstanceOf(ContinentData::class, $locationData->getContinent());
        $locationData->setCountry(new CountryData());
        $this->assertContainsOnlyInstancesOf(LocationData::class, [$locationData]);
        $this->assertInstanceOf(CountryData::class, $locationData->getCountry());
        $locationData->setCity(new CityData());
        $this->assertContainsOnlyInstancesOf(LocationData::class, [$locationData]);
        $this->assertInstanceOf(CityData::class, $locationData->getCity());
        $locationData->setOrganization(new OrganizationData());
        $this->assertContainsOnlyInstancesOf(LocationData::class, [$locationData]);
        $this->assertInstanceOf(OrganizationData::class, $locationData->getOrganization());
        $locationData->setLatitude($this->defaults['latitude']);
        $this->assertContainsOnlyInstancesOf(LocationData::class, [$locationData]);
        $this->assertSame($locationData->getLatitude(), $this->defaults['latitude']);
        $locationData->setLongitude($this->defaults['longitude']);
        $this->assertContainsOnlyInstancesOf(LocationData::class, [$locationData]);
        $this->assertSame($locationData->getLongitude(), $this->defaults['longitude']);
        $locationData->setTimeZone($this->defaults['timeZone']);
        $this->assertContainsOnlyInstancesOf(LocationData::class, [$locationData]);
        $this->assertSame($locationData->getTimeZone(), $this->defaults['timeZone']);
    }

    public function testExchangeArray(): void
    {
        $locationData = (new LocationData())->exchangeArray([
            'continent'    => new ContinentData(),
            'country'      => new CountryData(),
            'city'         => new CityData(),
            'organization' => new OrganizationData(),
            'latitude'     => $this->defaults['latitude'],
            'longitude'    => $this->defaults['longitude'],
            'timeZone'     => $this->defaults['timeZone'],
        ]);
        $this->assertContainsOnlyInstancesOf(LocationData::class, [$locationData]);
        $this->assertInstanceOf(ContinentData::class, $locationData->getContinent());
        $this->assertInstanceOf(CountryData::class, $locationData->getCountry());
        $this->assertInstanceOf(CityData::class, $locationData->getCity());
        $this->assertInstanceOf(OrganizationData::class, $locationData->getOrganization());
        $this->assertSame($this->defaults['latitude'], $locationData->getLatitude());
        $this->assertSame($this->defaults['longitude'], $locationData->getLongitude());
        $this->assertSame($this->defaults['timeZone'], $locationData->getTimeZone());
    }

    public function testGetArrayCopy(): void
    {
        $locationData = (new LocationData())->exchangeArray([
            'continent'    => new ContinentData(),
            'country'      => new CountryData(),
            'city'         => new CityData(),
            'organization' => new OrganizationData(),
            'latitude'     => $this->defaults['latitude'],
            'longitude'    => $this->defaults['longitude'],
            'timeZone'     => $this->defaults['timeZone'],
        ])->getArrayCopy();
        $this->assertArrayHasKey('continent', $locationData);
        $this->assertIsArray($locationData['continent']);
        $this->assertArrayHasKey('code', $locationData['continent']);
        $this->assertArrayHasKey('name', $locationData['continent']);
        $this->assertArrayHasKey('error', $locationData['continent']);

        $this->assertArrayHasKey('country', $locationData);
        $this->assertArrayHasKey('isEuMember', $locationData['country']);
        $this->assertArrayHasKey('isoCode', $locationData['country']);
        $this->assertArrayHasKey('name', $locationData['country']);
        $this->assertArrayHasKey('error', $locationData['country']);

        $this->assertArrayHasKey('city', $locationData);
        $this->assertIsArray($locationData['city']);
        $this->assertArrayHasKey('name', $locationData['city']);
        $this->assertArrayHasKey('error', $locationData['city']);

        $this->assertArrayHasKey('organization', $locationData);
        $this->assertIsArray($locationData['organization']);
        $this->assertArrayHasKey('asn', $locationData['organization']);
        $this->assertArrayHasKey('name', $locationData['organization']);
        $this->assertArrayHasKey('error', $locationData['organization']);

        $this->assertArrayHasKey('latitude', $locationData);
        $this->assertSame($locationData['latitude'], $this->defaults['latitude']);
        $this->assertSame($locationData['longitude'], $this->defaults['longitude']);
        $this->assertSame($locationData['timeZone'], $this->defaults['timeZone']);
    }
}
