<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Data;

use Dot\GeoIP\Data\CountryData;
use DotTest\GeoIP\AbstractTest;

class CountryDataTest extends AbstractTest
{
    public function testDefaultValues(): void
    {
        $countryData = new CountryData();
        $this->assertFalse($countryData->getIsEuMember());
        $this->assertNull($countryData->getIsoCode());
        $this->assertNull($countryData->getName());
    }

    public function testAccessors(): void
    {
        $countryData = new CountryData();
        $countryData->setIsEuMember($this->defaults['country']['isEuMember']);
        $this->assertInstanceOf(CountryData::class, $countryData);
        $this->assertSame($countryData->getIsEuMember(), $this->defaults['country']['isEuMember']);
        $countryData->setIsoCode($this->defaults['country']['isoCode']);
        $this->assertInstanceOf(CountryData::class, $countryData);
        $this->assertSame($countryData->getIsoCode(), $this->defaults['country']['isoCode']);
        $countryData->setName($this->defaults['country']['name']);
        $this->assertInstanceOf(CountryData::class, $countryData);
        $this->assertSame($countryData->getName(), $this->defaults['country']['name']);
    }

    public function testExchangeArray(): void
    {
        $countryData = (new CountryData())->exchangeArray($this->defaults['country']);
        $this->assertInstanceOf(CountryData::class, $countryData);
        $this->assertSame($countryData->getIsEuMember(), $this->defaults['country']['isEuMember']);
        $this->assertSame($countryData->getIsoCode(), $this->defaults['country']['isoCode']);
        $this->assertSame($countryData->getName(), $this->defaults['country']['name']);
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
    }
}
