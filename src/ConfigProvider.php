<?php

declare(strict_types=1);

namespace Dot\GeoIP;

use Dot\GeoIP\Command\GeoIpCommand;
use Dot\GeoIP\Factory\GeoIpCommandFactory;
use Dot\GeoIP\Factory\LocationServiceFactory;
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
                GeoIpCommand::class => GeoIpCommandFactory::class,
                LocationService::class => LocationServiceFactory::class,
            ],
            'aliases' => [
                LocationServiceInterface::class => LocationService::class,
            ]
        ];
    }
}
