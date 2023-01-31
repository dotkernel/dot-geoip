<?php

declare(strict_types=1);

namespace Dot\GeoIP\Factory;

use Dot\GeoIP\Service\LocationService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class LocationServiceFactory
 * @package Dot\GeoIP\Factory
 */
class LocationServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @return LocationService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): LocationService
    {
        return new LocationService(
            $container->get('config')['dot-geoip'] ?? []
        );
    }
}
