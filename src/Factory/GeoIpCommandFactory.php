<?php

declare(strict_types=1);

namespace Dot\GeoIP\Factory;

use Dot\GeoIP\Command\GeoIpCommand;
use Dot\GeoIP\Service\LocationService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class GeoIpCommandFactory
 * @package Dot\GeoIP\Factory
 */
class GeoIpCommandFactory
{
    /**
     * @param ContainerInterface $container
     * @return GeoIpCommand
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): GeoIpCommand
    {
        return new GeoIpCommand(
            $container->get(LocationService::class),
            $container->get('config')['dot-geoip'] ?? []
        );
    }
}
