<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Data;

use Dot\GeoIP\Data\CountryData;
use DotTest\GeoIP\CommonTrait;
use PHPUnit\Framework\TestCase;

class CountryDataTest extends TestCase
{
    use CommonTrait;

    public function testDefaultValues(): void
    {
        $countryData = new CountryData();
        $this->assertFalse($countryData->getIsEuMember());
        $this->assertNull($countryData->getIsoCode());
        $this->assertNull($countryData->getName());
        $this->assertNull($countryData->getError());
    }

    public function testAccessors(): void
    {
        $countryData = new CountryData();
        $countryData->setIsEuMember($this->defaults['country']['isEuMember']);
        $this->assertContainsOnlyInstancesOf(CountryData::class, [$countryData]);
        $this->assertSame($countryData->getIsEuMember(), $this->defaults['country']['isEuMember']);
        $countryData->setIsoCode($this->defaults['country']['isoCode']);
        $this->assertContainsOnlyInstancesOf(CountryData::class, [$countryData]);
        $this->assertSame($countryData->getIsoCode(), $this->defaults['country']['isoCode']);
        $countryData->setName($this->defaults['country']['name']);
        $this->assertContainsOnlyInstancesOf(CountryData::class, [$countryData]);
        $this->assertSame($countryData->getName(), $this->defaults['country']['name']);
        $countryData->setError($this->defaults['country']['error']);
        $this->assertContainsOnlyInstancesOf(CountryData::class, [$countryData]);
        $this->assertSame($countryData->getError(), $this->defaults['country']['error']);
    }

    public function testExchangeArray(): void
    {
        $countryData = (new CountryData())->exchangeArray($this->defaults['country']);
        $this->assertContainsOnlyInstancesOf(CountryData::class, [$countryData]);
        $this->assertSame($countryData->getIsEuMember(), $this->defaults['country']['isEuMember']);
        $this->assertSame($countryData->getIsoCode(), $this->defaults['country']['isoCode']);
        $this->assertSame($countryData->getName(), $this->defaults['country']['name']);
        $this->assertSame($countryData->getError(), $this->defaults['country']['error']);
    }

    public function testGetArrayCopy(): void
    {
        $countryData = (new CountryData())->exchangeArray($this->defaults['country'])->getArrayCopy();
        $this->assertIsArray($countryData);
        $this->assertArrayHasKey('isEuMember', $countryData);
        $this->assertSame($countryData['isEuMember'], $this->defaults['country']['isEuMember']);
        $this->assertArrayHasKey('isoCode', $countryData);
        $this->assertSame($countryData['isoCode'], $this->defaults['country']['isoCode']);
        $this->assertArrayHasKey('name', $countryData);
        $this->assertSame($countryData['name'], $this->defaults['country']['name']);
        $this->assertArrayHasKey('error', $countryData);
        $this->assertSame('error', $this->defaults['country']['error']);
    }
}
