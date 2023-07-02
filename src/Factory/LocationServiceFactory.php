<?php

declare(strict_types=1);

namespace Dot\GeoIP\Factory;

use Dot\GeoIP\Service\LocationService;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class LocationServiceFactory extends AbstractFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container): LocationService
    {
        $config = $container->get('config')['dot-geoip'] ?? [];
        $this->validateConfig($config);

        return new LocationService($config);
    }
}
