<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Data;

use Dot\GeoIP\Data\CityData;
use DotTest\GeoIP\CommonTrait;
use PHPUnit\Framework\TestCase;

class CityDataTest extends TestCase
{
    use CommonTrait;

    public function testDefaultValues(): void
    {
        $cityData = new CityData();
        $this->assertNull($cityData->getName());
        $this->assertNull($cityData->getError());
    }

    public function testAccessors()
    {
        $cityData = new CityData();
        $cityData->setName($this->defaults['city']['name']);
        $this->assertContainsOnlyInstancesOf(CityData::class, [$cityData]);
        $this->assertSame($cityData->getName(), $this->defaults['city']['name']);
        $cityData->setError($this->defaults['city']['error']);
        $this->assertContainsOnlyInstancesOf(CityData::class, [$cityData]);
        $this->assertSame($cityData->getError(), $this->defaults['city']['error']);
    }

    public function testExchangeArray()
    {
        $cityData = (new CityData())->exchangeArray($this->defaults['city']);
        $this->assertContainsOnlyInstancesOf(CityData::class, [$cityData]);
        $this->assertSame($cityData->getName(), $this->defaults['city']['name']);
        $this->assertSame($cityData->getError(), $this->defaults['city']['error']);
    }

    public function testGetArrayCopy()
    {
        $cityData = (new CityData())->exchangeArray($this->defaults['city'])->getArrayCopy();
        $this->assertIsArray($cityData);
        $this->assertArrayHasKey('name', $cityData);
        $this->assertSame('name', $cityData['name']);
        $this->assertArrayHasKey('error', $cityData);
        $this->assertSame('error', $cityData['error']);
    }
}
