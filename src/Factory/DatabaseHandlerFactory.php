<?php

declare(strict_types=1);

namespace Dot\GeoIP\Factory;

use Dot\GeoIP\Handler\DatabaseHandler;
use Dot\GeoIP\Service\LocationServiceInterface;
use Psr\Container\ContainerInterface;

/**
 * Class DatabaseHandlerFactory
 * @package Dot\GeoIP\Factory
 */
class DatabaseHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return DatabaseHandler
     */
    public function __invoke(ContainerInterface $container): DatabaseHandler
    {
        $config = $container->get('config');

        return new DatabaseHandler($container->get(LocationServiceInterface::class), $config['dot-geoip'] ?? []);
    }
}
