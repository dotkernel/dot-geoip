<?php

declare(strict_types=1);

namespace DotTest\GeoIP\Command;

use Dot\GeoIP\Command\GeoIpCommand;
use Dot\GeoIP\Service\LocationService;
use Dot\GeoIP\Service\LocationServiceInterface;
use DotTest\GeoIP\AbstractTest;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeoIpCommandTest extends AbstractTest
{
    public function testCreateCommand(): void
    {
        $locationService = $this->createMock(LocationServiceInterface::class);

        $command = new GeoIpCommand($locationService);
        $this->assertInstanceOf(GeoIpCommand::class, $command);
    }

    /**
     * @throws GuzzleException
     * @throws InvalidDatabaseException
     */
    public function testExecuteCommandWillCreateDatabaseFile(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $input->expects($this->once())
            ->method('getOption')
            ->with('database')
            ->willReturn(
                LocationService::DATABASE_ASN
            );

        $locationService = new LocationService($this->getConfig());
        $command = new GeoIpCommand($locationService);
        $command->execute($input, $output);
        $this->assertFileExists($locationService->getDatabasePath(LocationService::DATABASE_ASN));
    }

    public function testWillIdentifyValidDatabases(): void
    {
        $locationService = $this->createMock(LocationServiceInterface::class);

        $command = new GeoIpCommand($locationService);
        $databases = $command->identifyDatabases(LocationService::DATABASE_ASN);
        $this->assertIsArray($databases);
        $this->assertContains(LocationService::DATABASE_ASN, $databases);
        $databases = $command->identifyDatabases(LocationService::DATABASE_ALL);
        $this->assertIsArray($databases);
        $this->assertCount(count(LocationService::DATABASES), $databases);
        $this->assertContains(LocationService::DATABASE_ASN, $databases);
        $this->assertContains(LocationService::DATABASE_CITY, $databases);
        $this->assertContains(LocationService::DATABASE_COUNTRY, $databases);
    }

    public function testWillIdentifyInvalidDatabases(): void
    {
        $locationService = $this->createMock(LocationServiceInterface::class);

        $command = new GeoIpCommand($locationService);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Invalid database identifier: test. Use one of the following identifiers: asn, city, country.'
        );
        $command->identifyDatabases('test');
    }
}
