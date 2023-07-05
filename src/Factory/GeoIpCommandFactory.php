<?php

declare(strict_types=1);

namespace Dot\GeoIP\Factory;

use Dot\GeoIP\Command\GeoIpCommand;
use Dot\GeoIP\Service\LocationServiceInterface;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class GeoIpCommandFactory extends AbstractFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container): GeoIpCommand
    {
        $locationService = $container->has(LocationServiceInterface::class)
            ? $container->get(LocationServiceInterface::class)
            : null;
        if (! $locationService instanceof LocationServiceInterface) {
            throw new Exception(self::MESSAGE_MISSING_LOCATION_SERVICE);
        }

        return new GeoIpCommand($locationService);
    }
}
