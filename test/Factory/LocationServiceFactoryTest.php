<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Factory;

use Dot\GeoIP\Factory\AbstractFactory;
use Dot\GeoIP\Factory\LocationServiceFactory;
use Dot\GeoIP\Service\LocationService;
use DotTest\GeoIP\CommonTrait;
use DotTest\GeoIP\Fixture\Container;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function sprintf;

class LocationServiceFactoryTest extends TestCase
{
    use CommonTrait;

    public function testCreateService(): void
    {
        $container = new Container(['config' => ['dot-geoip' => $this->getConfig()]]);

        $service = (new LocationServiceFactory())($container);

        $this->assertSame($this->getConfig(), $service->getConfigs());
    }

    public function testCreateServiceWithOneDatabase(): void
    {
        $config = $this->getConfig();
        unset(
            $config['databases'][LocationService::DATABASE_CITY],
            $config['databases'][LocationService::DATABASE_COUNTRY]
        );

        $service = (new LocationServiceFactory())(new Container(['config' => ['dot-geoip' => $config]]));

        $this->assertSame($config, $service->getConfigs());
    }

    /**
     * @return iterable<string, array{array<array-key, mixed>, string}>
     */
    public static function invalidConfigProvider(): iterable
    {
        $databases = ['asn' => ['source' => 'https://example.com/asn.mmdb.gz']];

        yield 'missing config' => [[], AbstractFactory::MESSAGE_MISSING_CONFIG];
        yield 'missing targetDir' => [
            ['databases' => $databases],
            AbstractFactory::MESSAGE_MISSING_CONFIG_TARGET_DIR,
        ];
        yield 'empty targetDir' => [
            ['targetDir' => '', 'databases' => $databases],
            AbstractFactory::MESSAGE_MISSING_CONFIG_TARGET_DIR,
        ];
        yield 'missing databases' => [
            ['targetDir' => '/tmp'],
            AbstractFactory::MESSAGE_MISSING_CONFIG_DATABASES,
        ];
        yield 'empty databases' => [
            ['targetDir' => '/tmp', 'databases' => []],
            AbstractFactory::MESSAGE_MISSING_CONFIG_DATABASES,
        ];
        yield 'unknown databases only' => [
            ['targetDir' => '/tmp', 'databases' => ['test' => ['source' => 'https://example.com']]],
            AbstractFactory::MESSAGE_MISSING_CONFIG_DATABASES,
        ];
        yield 'missing source' => [
            ['targetDir' => '/tmp', 'databases' => ['asn' => []]],
            sprintf(AbstractFactory::MESSAGE_MISSING_CONFIG_DATABASE, 'asn'),
        ];
        yield 'database entry is not an array' => [
            ['targetDir' => '/tmp', 'databases' => ['asn' => 'https://example.com/asn.mmdb.gz']],
            sprintf(AbstractFactory::MESSAGE_MISSING_CONFIG_DATABASE, 'asn'),
        ];
    }

    /**
     * @param array<array-key, mixed> $config
     */
    #[DataProvider('invalidConfigProvider')]
    public function testInvalidConfigThrows(array $config, string $message): void
    {
        $container = new Container(['config' => ['dot-geoip' => $config]]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        (new LocationServiceFactory())($container);
    }
}
