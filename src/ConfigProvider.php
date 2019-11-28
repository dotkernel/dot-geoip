<?php

declare(strict_types=1);

namespace Dot\GeoIP;

use Dot\GeoIP\Factory\DatabaseHandlerFactory;
use Dot\GeoIP\Factory\LocationServiceFactory;
use Dot\GeoIP\Handler\DatabaseHandler;
use Dot\GeoIP\Service\LocationService;
use Dot\GeoIP\Service\LocationServiceInterface;

/**
 * Class ConfigProvider
 * @package Dot\GeoIP
 */
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies()
        ];
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                DatabaseHandler::class => DatabaseHandlerFactory::class,
                LocationService::class => LocationServiceFactory::class
            ],
            'aliases' => [
                LocationServiceInterface::class => LocationService::class
            ]
        ];
    }
}
