<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Data;

use Dot\GeoIP\Data\ContinentData;
use DotTest\GeoIP\CommonTrait;
use PHPUnit\Framework\TestCase;

class ContinentDataTest extends TestCase
{
    use CommonTrait;

    public function testDefaultValues(): void
    {
        $continentData = new ContinentData();
        $this->assertNull($continentData->getCode());
        $this->assertNull($continentData->getName());
    }

    public function testAccessors(): void
    {
        $continentData = new ContinentData();
        $continentData->setCode($this->defaults['continent']['code']);
        $this->assertInstanceOf(ContinentData::class, $continentData);
        $this->assertSame($continentData->getCode(), $this->defaults['continent']['code']);
        $continentData->setName($this->defaults['continent']['name']);
        $this->assertInstanceOf(ContinentData::class, $continentData);
        $this->assertSame($continentData->getName(), $this->defaults['continent']['name']);
    }

    public function testExchangeArray(): void
    {
        $continentData = (new ContinentData())->exchangeArray($this->defaults['continent']);
        $this->assertInstanceOf(ContinentData::class, $continentData);
        $this->assertSame($continentData->getCode(), $this->defaults['continent']['code']);
        $this->assertSame($continentData->getName(), $this->defaults['continent']['name']);
    }

    public function testGetArrayCopy(): void
    {
        $continentData = (new ContinentData())->exchangeArray($this->defaults['continent'])->getArrayCopy();
        $this->assertIsArray($continentData);
        $this->assertArrayHasKey('code', $continentData);
        $this->assertSame($continentData['code'], $this->defaults['continent']['code']);
        $this->assertArrayHasKey('name', $continentData);
        $this->assertSame($continentData['name'], $this->defaults['continent']['name']);
    }
}
