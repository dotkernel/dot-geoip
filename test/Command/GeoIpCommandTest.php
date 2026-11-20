<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Command;

use Dot\GeoIP\Command\GeoIpCommand;
use Dot\GeoIP\Service\LocationService;
use DotTest\GeoIP\CommonTrait;
use InvalidArgumentException;
use MaxMind\Db\Reader\Metadata;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;

use function file_get_contents;
use function file_put_contents;
use function gzencode;
use function ini_get;
use function ini_set;
use function is_dir;
use function is_file;
use function md5_file;
use function mkdir;
use function rmdir;
use function sprintf;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

/**
 * @phpstan-import-type GeoIpConfig from LocationService
 */
class GeoIpCommandTest extends TestCase
{
    use CommonTrait {
        setUp as commonSetUp;
    }

    private static string $sourceDir;
    private string $memoryLimit;

    public static function setUpBeforeClass(): void
    {
        $sample = file_get_contents(sprintf('%s/../sample.mmdb', __DIR__));
        if ($sample === false) {
            throw new RuntimeException('Cannot read the sample database.');
        }

        self::$sourceDir = sprintf('%s/%s', sys_get_temp_dir(), uniqid('dot-geoip-test-'));
        mkdir(self::$sourceDir);
        file_put_contents(self::$sourceDir . '/valid.mmdb.gz', gzencode($sample));
        file_put_contents(self::$sourceDir . '/invalid.mmdb.gz', gzencode('This is not a database.'));
    }

    public static function tearDownAfterClass(): void
    {
        foreach (['valid.mmdb.gz', 'invalid.mmdb.gz'] as $file) {
            if (is_file(self::$sourceDir . '/' . $file)) {
                unlink(self::$sourceDir . '/' . $file);
            }
        }
        if (is_dir(self::$sourceDir)) {
            rmdir(self::$sourceDir);
        }
    }

    protected function setUp(): void
    {
        $this->commonSetUp();

        $this->memoryLimit = (string) ini_get('memory_limit');
    }

    protected function tearDown(): void
    {
        ini_set('memory_limit', $this->memoryLimit);
    }

    public function testCommandHasNameAndOptions(): void
    {
        $command = new GeoIpCommand(new LocationService($this->getConfig()));

        $this->assertSame('geoip:synchronize', $command->getName());
        $this->assertSame('geoip:synchronize', GeoIpCommand::$defaultName);
        $this->assertTrue($command->getDefinition()->hasOption('database'));
        $this->assertTrue($command->getDefinition()->hasOption('memory-limit'));
    }

    public function testExecuteReplacesDatabaseFromValidSource(): void
    {
        $locationService = new LocationService($this->configWithAsnSource('valid.mmdb.gz'));
        $realFilePath    = $locationService->getRealFilePath(LocationService::DATABASE_ASN);
        unlink($realFilePath);

        $tester = new CommandTester(new GeoIpCommand($locationService));
        $status = $tester->execute(['--database' => LocationService::DATABASE_ASN]);

        $this->assertSame(Command::SUCCESS, $status);
        $this->assertFileExists($realFilePath);
        $this->assertFileDoesNotExist($realFilePath . '.new');
        $this->assertInstanceOf(
            Metadata::class,
            $locationService->getDatabaseMetadata(LocationService::DATABASE_ASN)
        );
        $this->assertStringContainsString('asn: n/a -> 2023-02-01', $tester->getDisplay());
    }

    public function testFailedDownloadKeepsExistingDatabase(): void
    {
        $source = 'file:///nonexistent/dot-geoip/asn.mmdb.gz';
        $config = $this->getConfig();

        $config['databases'][LocationService::DATABASE_ASN]['source'] = $source;

        $locationService = new LocationService($config);
        $realFilePath    = $locationService->getRealFilePath(LocationService::DATABASE_ASN);
        $checksum        = md5_file($realFilePath);

        $tester = new CommandTester(new GeoIpCommand($locationService));
        $status = $tester->execute(['--database' => LocationService::DATABASE_ASN]);

        $this->assertSame(Command::FAILURE, $status);
        $this->assertSame($checksum, md5_file($realFilePath));
        $this->assertFileDoesNotExist($realFilePath . '.new');
        $this->assertStringContainsString('asn: The download from', $tester->getDisplay());
    }

    public function testInvalidDatabaseKeepsExistingDatabase(): void
    {
        $locationService = new LocationService($this->configWithAsnSource('invalid.mmdb.gz'));
        $realFilePath    = $locationService->getRealFilePath(LocationService::DATABASE_ASN);
        $checksum        = md5_file($realFilePath);

        $tester = new CommandTester(new GeoIpCommand($locationService));
        $status = $tester->execute(['--database' => LocationService::DATABASE_ASN]);

        $this->assertSame(Command::FAILURE, $status);
        $this->assertSame($checksum, md5_file($realFilePath));
        $this->assertFileDoesNotExist($realFilePath . '.new');
    }

    public function testAllSynchronizesOnlyConfiguredDatabases(): void
    {
        $config = $this->configWithAsnSource('valid.mmdb.gz');
        unset(
            $config['databases'][LocationService::DATABASE_CITY],
            $config['databases'][LocationService::DATABASE_COUNTRY]
        );

        $tester = new CommandTester(new GeoIpCommand(new LocationService($config)));
        $status = $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $status);
        $this->assertStringContainsString('asn:', $tester->getDisplay());
        $this->assertStringNotContainsString('city:', $tester->getDisplay());
        $this->assertStringNotContainsString('country:', $tester->getDisplay());
    }

    public function testDatabaseThatIsNotConfiguredThrows(): void
    {
        $config = $this->getConfig();
        unset($config['databases'][LocationService::DATABASE_CITY]);

        $tester = new CommandTester(new GeoIpCommand(new LocationService($config)));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Database "city" is not configured.');
        $tester->execute(['--database' => LocationService::DATABASE_CITY]);
    }

    public function testMemoryLimitWithoutValueThrows(): void
    {
        $tester = new CommandTester(new GeoIpCommand(new LocationService($this->getConfig())));

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('The "--memory-limit" option requires a value.');
        $tester->execute(['--memory-limit' => null]);
    }

    public function testInvalidMemoryLimitThrows(): void
    {
        $tester = new CommandTester(new GeoIpCommand(new LocationService($this->getConfig())));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid memory limit: abc');
        $tester->execute(['--memory-limit' => 'abc']);
    }

    /**
     * Each value is at least as large as the memory that PHPUnit uses, so that ini_set() accepts it.
     *
     * @return iterable<string, array{string}>
     */
    public static function validMemoryLimitProvider(): iterable
    {
        yield 'unlimited' => ['-1'];
        yield 'lower-case unit' => ['1g'];
        yield 'kilobytes' => ['2097152K'];
        yield 'megabytes' => ['2048M'];
    }

    #[DataProvider('validMemoryLimitProvider')]
    public function testValidMemoryLimitIsAccepted(string $memoryLimit): void
    {
        $locationService = new LocationService($this->configWithAsnSource('valid.mmdb.gz'));

        $tester = new CommandTester(new GeoIpCommand($locationService));
        $status = $tester->execute([
            '--database'     => LocationService::DATABASE_ASN,
            '--memory-limit' => $memoryLimit,
        ]);

        $this->assertSame(Command::SUCCESS, $status);
        $this->assertSame($memoryLimit, ini_get('memory_limit'));
    }

    public function testWillIdentifyValidDatabases(): void
    {
        $command = new GeoIpCommand(new LocationService($this->getConfig()));

        $this->assertSame([LocationService::DATABASE_ASN], $command->identifyDatabases(LocationService::DATABASE_ASN));
        $this->assertSame(
            [LocationService::DATABASE_ASN, LocationService::DATABASE_CITY, LocationService::DATABASE_COUNTRY],
            $command->identifyDatabases(LocationService::DATABASE_ALL)
        );
    }

    public function testWillIdentifyInvalidDatabases(): void
    {
        $command = new GeoIpCommand(new LocationService($this->getConfig()));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid database identifier: test. Use one of the following identifiers: asn, city, country.'
        );
        $command->identifyDatabases('test');
    }

    /**
     * @return GeoIpConfig
     */
    private function configWithAsnSource(string $file): array
    {
        if (! is_dir(self::$sourceDir)) {
            throw new RuntimeException('The source directory does not exist.');
        }

        $config = $this->getConfig();
        $config['databases'][LocationService::DATABASE_ASN]['source'] = sprintf(
            'file://%s/%s',
            self::$sourceDir,
            $file
        );

        return $config;
    }
}
