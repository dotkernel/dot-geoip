<?php

declare(strict_types=1);

namespace Dot\GeoIP\Factory;

use Dot\GeoIP\Service\LocationService;
use Psr\Container\ContainerInterface;

/**
 * Class LocationServiceFactory
 * @package Dot\GeoIP\Factory
 */
class LocationServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @return LocationService
     */
    public function __invoke(ContainerInterface $container): LocationService
    {
        $config = $container->get('config');

        return new LocationService($config['dot-geoip'] ?? []);
    }
}
