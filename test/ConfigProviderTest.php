<?php

declare(strict_types=1);

namespace DotTest\GeoIP;

use Dot\GeoIP\Command\GeoIpCommand;
use Dot\GeoIP\ConfigProvider;
use Dot\GeoIP\Service\LocationService;
use Dot\GeoIP\Service\LocationServiceInterface;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected array $config;

    protected function setup(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertArrayHasKey(GeoIpCommand::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(LocationService::class, $this->config['dependencies']['factories']);
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertArrayHasKey('aliases', $this->config['dependencies']);
        $this->assertArrayHasKey(LocationServiceInterface::class, $this->config['dependencies']['aliases']);
    }
}
