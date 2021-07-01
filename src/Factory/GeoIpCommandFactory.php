<?php

declare(strict_types=1);

namespace Dot\GeoIP\Factory;

use Dot\GeoIP\Command\GeoIpCommand;
use Dot\GeoIP\Service\LocationService;
use Psr\Container\ContainerInterface;

/**
 * Class GeoIpCommandFactory
 * @package Dot\GeoIP\Factory
 */
class GeoIpCommandFactory
{
    /**
     * @param ContainerInterface $container
     * @return GeoIpCommand
     */
    public function __invoke(ContainerInterface $container): GeoIpCommand
    {
        $config = $container->get('config')['dot-geoip'] ?? [];
        return new GeoIpCommand($container->get(LocationService::class), $config);
    }
}
