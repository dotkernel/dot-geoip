<?php

declare(strict_types=1);

namespace Dot\GeoIP\Factory;

use Dot\GeoIP\Command\GeoIpCommand;
use Dot\GeoIP\Service\LocationServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class GeoIpCommandFactory extends AbstractFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RuntimeException
     */
    public function __invoke(ContainerInterface $container): GeoIpCommand
    {
        if (! $container->has(LocationServiceInterface::class)) {
            throw new RuntimeException(self::MESSAGE_MISSING_LOCATION_SERVICE);
        }

        return new GeoIpCommand(
            $container->get(LocationServiceInterface::class)
        );
    }
}
