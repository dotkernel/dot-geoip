<?php

declare(strict_types=1);

namespace DotTest\GeoIP;

use Dot\GeoIP\Command\GeoIpCommand;
use Dot\GeoIP\ConfigProvider;
use Dot\GeoIP\Factory\GeoIpCommandFactory;
use Dot\GeoIP\Factory\LocationServiceFactory;
use Dot\GeoIP\Service\LocationService;
use Dot\GeoIP\Service\LocationServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type DependencyConfig from ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    /** @var array{dependencies: DependencyConfig} */
    protected array $config;

    protected function setUp(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertSame(
            GeoIpCommandFactory::class,
            $this->config['dependencies']['factories'][GeoIpCommand::class]
        );
        $this->assertSame(
            LocationServiceFactory::class,
            $this->config['dependencies']['factories'][LocationService::class]
        );
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertSame(
            LocationService::class,
            $this->config['dependencies']['aliases'][LocationServiceInterface::class]
        );
    }
}
