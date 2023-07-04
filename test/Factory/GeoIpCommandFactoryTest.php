<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Factory;

use Dot\GeoIP\Command\GeoIpCommand;
use Dot\GeoIP\Factory\AbstractFactory;
use Dot\GeoIP\Factory\GeoIpCommandFactory;
use Dot\GeoIP\Service\LocationServiceInterface;
use DotTest\GeoIP\CommonTrait;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class GeoIpCommandFactoryTest extends TestCase
{
    use CommonTrait;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|Exception
     */
    public function testFactoryWillNotCreateCommandWithoutLocationService(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with(LocationServiceInterface::class)
            ->willReturn(false);

        $this->expectExceptionMessage(AbstractFactory::MESSAGE_MISSING_LOCATION_SERVICE);
        (new GeoIpCommandFactory())($container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|Exception
     */
    public function testFactoryWillCreateCommand(): void
    {
        $container       = $this->createMock(ContainerInterface::class);
        $locationService = $this->createMock(LocationServiceInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with(LocationServiceInterface::class)
            ->willReturn(true);

        $container->method('get')->willReturnMap([
            [LocationServiceInterface::class, $locationService],
        ]);

        $command = (new GeoIpCommandFactory())($container);
        $this->assertInstanceOf(GeoIpCommand::class, $command);
    }
}
