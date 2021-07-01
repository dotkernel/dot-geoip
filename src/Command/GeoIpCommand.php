<?php

declare(strict_types=1);

namespace Dot\GeoIP\Command;

use Dot\GeoIP\Service\LocationService;
use Dot\GeoIP\Service\LocationServiceInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Laminas\Filter\Decompress;
use MaxMind\Db\Reader\Metadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use function array_key_exists;
use function array_keys;
use function date;
use function implode;
use function sprintf;

/**
 * Class GeoIpCommand
 * @package Dot\GeoIP\Command
 */
class GeoIpCommand extends Command
{
    protected static $defaultName = 'geoip:synchronize';

    protected array $config = [];

    protected LocationServiceInterface $locationService;

    /**
     * GeoIpCommand constructor.
     * @param LocationServiceInterface $locationService
     * @param array $config
     */
    public function __construct(LocationServiceInterface $locationService, array $config)
    {
        parent::__construct(self::$defaultName);
        $this->config = $config;
        $this->locationService = $locationService;
    }

    /**
     * Configure command
     */
    public function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Download latest version of GeoLite2-* database files.')
            ->addOption(
                'database',
                'd',
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'Database name. Accepted values: %s',
                    implode(', ', array_keys(LocationService::DATABASES))
                ),
                LocationService::DATABASE_ALL
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws GuzzleException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileSystem = new Filesystem();
        if (!$fileSystem->exists($this->config['targetDir'])) {
            $fileSystem->mkdir($this->config['targetDir']);
        }

        $database = $input->getOption('database') ?? LocationService::DATABASE_ALL;
        $databases = $this->identifyDatabases($database);
        foreach ($databases as $database) {
            $sourcePath = $this->locationService->getDatabaseSource($database);
            $targetPath = $this->locationService->getDatabasePath($database);

            $oldVersion = 'n/a';
            $oldMetadata = $this->locationService->getDatabaseMetadata($database);
            if ($oldMetadata instanceof Metadata) {
                $oldVersion = date('Y-m-d H:i:s', $oldMetadata->buildEpoch);
            }

            $client = new Client();
            $client->get($this->config['databases'][$database]['source'], [RequestOptions::SINK => $sourcePath]);

            $extractor = new Decompress();
            $content = $extractor->getAdapter()->decompress($sourcePath);
            $fileSystem->remove($targetPath);
            $fileSystem->dumpFile($targetPath, $content);
            $fileSystem->remove($sourcePath);

            $newVersion = 'n/a';
            $newMetadata = $this->locationService->getDatabaseMetadata($database);
            if ($newMetadata instanceof Metadata) {
                $newVersion = date('Y-m-d H:i:s', $newMetadata->buildEpoch);
            }

            $output->writeln(sprintf("%s: %s -> %s", $database, $oldVersion, $newVersion));
        }

        return self::SUCCESS;
    }

    /**
     * @param string $identifier
     * @return array|string[]
     * @throws Exception
     */
    private function identifyDatabases(string $identifier): array
    {
        if ($identifier === LocationService::DATABASE_ALL) {
            return [
                LocationService::DATABASE_ASN,
                LocationService::DATABASE_CITY,
                LocationService::DATABASE_COUNTRY,
            ];
        }

        if (!array_key_exists($identifier, LocationService::DATABASES)) {
            $message = sprintf('Invalid database identifier: %s. Use one of the following identifiers: %s.',
                $identifier,
                implode(', ', array_keys(LocationService::DATABASES))
            );
            throw new Exception($message);
        }

        return [$identifier];
    }
}
