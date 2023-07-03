<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Factory;

use Dot\GeoIP\Factory\LocationServiceFactory;
use Dot\GeoIP\Service\LocationService;
use DotTest\GeoIP\CommonTrait;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class LocationServiceFactoryTest extends TestCase
{
    use CommonTrait;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|Exception
     */
    public function testCreateService(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn([
                'dot-geoip' => $this->getConfig(),
            ]);

        $service = (new LocationServiceFactory)($container);
        $this->assertInstanceOf(LocationService::class, $service);
    }
}
