<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Factory;

use Dot\GeoIP\Factory\AbstractFactory;
use Dot\GeoIP\Factory\GeoIpCommandFactory;
use Dot\GeoIP\Service\LocationService;
use Dot\GeoIP\Service\LocationServiceInterface;
use DotTest\GeoIP\CommonTrait;
use DotTest\GeoIP\Fixture\Container;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class GeoIpCommandFactoryTest extends TestCase
{
    use CommonTrait;

    public function testFactoryWillNotCreateCommandWithoutLocationService(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(AbstractFactory::MESSAGE_MISSING_LOCATION_SERVICE);
        (new GeoIpCommandFactory())(new Container([]));
    }

    public function testFactoryWillCreateCommand(): void
    {
        $container = new Container([
            LocationServiceInterface::class => new LocationService($this->getConfig()),
        ]);

        $command = (new GeoIpCommandFactory())($container);

        $this->assertSame('geoip:synchronize', $command->getName());
    }
}
