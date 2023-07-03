<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Data;

use Dot\GeoIP\Data\OrganizationData;
use DotTest\GeoIP\CommonTrait;
use PHPUnit\Framework\TestCase;

class OrganizationDataTest extends TestCase
{
    use CommonTrait;

    public function testDefaultValues(): void
    {
        $organizationData = new OrganizationData();
        $this->assertNull($organizationData->getAsn());
        $this->assertNull($organizationData->getName());
    }

    public function testAccessors(): void
    {
        $organizationData = new OrganizationData();
        $organizationData->setAsn($this->defaults['organization']['asn']);
        $this->assertInstanceOf(OrganizationData::class, $organizationData);
        $this->assertSame($organizationData->getAsn(), $this->defaults['organization']['asn']);
        $organizationData->setName($this->defaults['organization']['name']);
        $this->assertInstanceOf(OrganizationData::class, $organizationData);
        $this->assertSame($organizationData->getName(), $this->defaults['organization']['name']);
    }

    public function testExchangeArray(): void
    {
        $organizationData = (new OrganizationData())->exchangeArray($this->defaults['organization']);
        $this->assertInstanceOf(OrganizationData::class, $organizationData);
        $this->assertSame($organizationData->getAsn(), $this->defaults['organization']['asn']);
        $this->assertSame($organizationData->getName(), $this->defaults['organization']['name']);
    }

    public function testGetArrayCopy(): void
    {
        $organizationData = (new OrganizationData())->exchangeArray($this->defaults['organization'])->getArrayCopy();
        $this->assertIsArray($organizationData);
        $this->assertArrayHasKey('asn', $organizationData);
        $this->assertSame($organizationData['asn'], $this->defaults['organization']['asn']);
        $this->assertArrayHasKey('name', $organizationData);
        $this->assertSame($organizationData['name'], $this->defaults['organization']['name']);
    }
}
