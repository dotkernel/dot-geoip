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
use Symfony\Component\Console\Attribute\AsCommand;
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
use function str_replace;
use function trim;

#[AsCommand(
    name: 'geoip:synchronize',
    description: 'Download latest version of GeoLite2-* database files.',
)]
class GeoIpCommand extends Command
{
    protected LocationServiceInterface $locationService;
    /** @var string $defaultName */
    protected static $defaultName = 'geoip:synchronize';

    public function __construct(LocationServiceInterface $locationService)
    {
        parent::__construct(self::$defaultName);
        $this->locationService = $locationService;
    }

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
     * @throws GuzzleException
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileSystem = new Filesystem();
        if (! $fileSystem->exists($this->locationService->getConfig('targetDir'))) {
            $fileSystem->mkdir($this->locationService->getConfig('targetDir'));
        }

        $database  = $input->getOption('database') ?? LocationService::DATABASE_ALL;
        $databases = $this->identifyDatabases($database);
        foreach ($databases as $database) {
            $sourcePath = $this->locationService->getDatabaseSource($database);
            $targetPath = $this->locationService->getDatabasePath($database);

            $oldVersion  = 'n/a';
            $oldMetadata = $this->locationService->getDatabaseMetadata($database);
            if ($oldMetadata instanceof Metadata) {
                $oldVersion = date('Y-m-d H:i:s', $oldMetadata->buildEpoch);
            }

            $url = trim($this->locationService->getConfig('databases')[$database]['source']);
            $url = str_replace('{year}', date('Y'), $url);
            $url = str_replace('{month}', date('m'), $url);
            (new Client())->get($url, [RequestOptions::SINK => $sourcePath]);

            $content = (new Decompress())->getAdapter()->decompress($sourcePath);
            $fileSystem->remove($targetPath);
            $fileSystem->dumpFile($targetPath, $content);
            $fileSystem->remove($sourcePath);

            $newVersion  = 'n/a';
            $newMetadata = $this->locationService->getDatabaseMetadata($database);
            if ($newMetadata instanceof Metadata) {
                $newVersion = date('Y-m-d H:i:s', $newMetadata->buildEpoch);
            }

            $output->writeln(sprintf("%s: %s -> %s", $database, $oldVersion, $newVersion));
        }

        return self::SUCCESS;
    }

    /**
     * @throws Exception
     */
    public function identifyDatabases(string $identifier): array
    {
        if ($identifier === LocationService::DATABASE_ALL) {
            return [
                LocationService::DATABASE_ASN,
                LocationService::DATABASE_CITY,
                LocationService::DATABASE_COUNTRY,
            ];
        }

        if (! array_key_exists($identifier, LocationService::DATABASES)) {
            $message = sprintf(
                'Invalid database identifier: %s. Use one of the following identifiers: %s.',
                $identifier,
                implode(', ', array_keys(LocationService::DATABASES))
            );
            throw new Exception($message);
        }

        return [$identifier];
    }
}
