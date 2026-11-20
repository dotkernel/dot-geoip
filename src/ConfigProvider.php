<?php

declare(strict_types=1);

namespace Dot\GeoIP;

use Dot\GeoIP\Command\GeoIpCommand;
use Dot\GeoIP\Factory\GeoIpCommandFactory;
use Dot\GeoIP\Factory\LocationServiceFactory;
use Dot\GeoIP\Service\LocationService;
use Dot\GeoIP\Service\LocationServiceInterface;

/**
 * @phpstan-type DependencyConfig array{
 *     factories: array<class-string, class-string>,
 *     aliases: array<class-string, class-string>
 * }
 */
class ConfigProvider
{
    /**
     * @return array{dependencies: DependencyConfig}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return DependencyConfig
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                GeoIpCommand::class    => GeoIpCommandFactory::class,
                LocationService::class => LocationServiceFactory::class,
            ],
            'aliases'   => [
                LocationServiceInterface::class => LocationService::class,
            ],
        ];
    }
}
