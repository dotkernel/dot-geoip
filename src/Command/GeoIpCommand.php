<?php

declare(strict_types=1);

namespace Dot\GeoIP\Command;

use Dot\GeoIP\Client;
use Dot\GeoIP\Extractor;
use Dot\GeoIP\FileSystem;
use Dot\GeoIP\Service\LocationService;
use Dot\GeoIP\Service\LocationServiceInterface;
use GeoIp2\Database\Reader;
use InvalidArgumentException;
use MaxMind\Db\Reader\Metadata;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function array_intersect;
use function array_key_exists;
use function array_keys;
use function array_values;
use function date;
use function get_debug_type;
use function implode;
use function in_array;
use function ini_set;
use function is_array;
use function is_int;
use function is_string;
use function preg_match;
use function rename;
use function sprintf;

#[AsCommand(
    name: 'geoip:synchronize',
    description: 'Download latest version of GeoLite2-* database files.',
)]
class GeoIpCommand extends Command
{
    /** @var string $defaultName */
    public static $defaultName = 'geoip:synchronize';
    private FileSystem $fileSystem;

    public function __construct(
        protected LocationServiceInterface $locationService,
    ) {
        parent::__construct(self::$defaultName);

        $this->fileSystem = new FileSystem();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'database',
                'd',
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'Database name. Accepted values: %s',
                    implode(', ', array_keys(LocationService::DATABASES))
                ),
                LocationService::DATABASE_ALL
            )
            ->addOption(
                'memory-limit',
                'm',
                InputOption::VALUE_REQUIRED,
                'Memory limit for decompression. Examples: 256M, 1G, -1',
                '128M'
            );
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $memoryLimit = $this->validateMemoryLimit($input->getOption('memory-limit'));
        if (ini_set('memory_limit', $memoryLimit) === false) {
            throw new RuntimeException(sprintf('Cannot set the memory limit to %s.', $memoryLimit));
        }

        $this->fileSystem->mkdir($this->locationService->getConfig('targetDir'));

        $database = $input->getOption('database');
        if (! is_string($database)) {
            $database = LocationService::DATABASE_ALL;
        }

        $status = self::SUCCESS;
        foreach ($this->identifyDatabases($database) as $database) {
            $realFilePath = $this->locationService->getRealFilePath($database);
            $newFilePath  = $realFilePath . '.new';
            $tempFilePath = $this->locationService->getTempFilePath($database);
            $oldVersion   = $this->formatBuildDate($this->locationService->getDatabaseMetadata($database));

            try {
                (new Client())->get($this->locationService->getDatabaseSourceUrl($database), $tempFilePath);
                (new Extractor())->extract($tempFilePath, $newFilePath);
                $this->validateDatabase($newFilePath);
                if (! rename($newFilePath, $realFilePath)) {
                    throw new RuntimeException(sprintf('Cannot replace "%s".', $realFilePath));
                }
            } catch (Throwable $exception) {
                $this->getErrorOutput($output)->writeln(
                    sprintf('<error>%s: %s</error>', $database, $exception->getMessage())
                );
                $status = self::FAILURE;
                continue;
            } finally {
                $this->fileSystem->remove($tempFilePath);
                $this->fileSystem->remove($newFilePath);
            }

            $newVersion = $this->formatBuildDate($this->locationService->getDatabaseMetadata($database));
            $output->writeln(sprintf("%s: %s -> %s", $database, $oldVersion, $newVersion));
        }

        return $status;
    }

    /**
     * Return the identifiers of the databases to synchronize.
     * The identifier "all" returns only the databases in the config.
     *
     * @return list<string>
     * @throws InvalidArgumentException
     */
    public function identifyDatabases(string $identifier): array
    {
        $databases  = $this->locationService->getConfig('databases');
        $configured = is_array($databases) ? array_keys($databases) : [];

        if ($identifier === LocationService::DATABASE_ALL) {
            return array_values(array_intersect(array_keys(LocationService::DATABASES), $configured));
        }

        if (! array_key_exists($identifier, LocationService::DATABASES)) {
            $message = sprintf(
                'Invalid database identifier: %s. Use one of the following identifiers: %s.',
                $identifier,
                implode(', ', array_keys(LocationService::DATABASES))
            );
            throw new InvalidArgumentException($message);
        }

        if (! in_array($identifier, $configured, true)) {
            throw new InvalidArgumentException(sprintf('Database "%s" is not configured.', $identifier));
        }

        return [$identifier];
    }

    /**
     * @throws InvalidArgumentException
     */
    private function validateMemoryLimit(mixed $memoryLimit): string
    {
        if (is_string($memoryLimit) && preg_match('/^(-1|\d+[KMG]?)$/i', $memoryLimit) === 1) {
            return $memoryLimit;
        }

        throw new InvalidArgumentException(
            sprintf('Invalid memory limit: %s', is_string($memoryLimit) ? $memoryLimit : get_debug_type($memoryLimit))
        );
    }

    /**
     * Open the file as a database, so that an invalid file never replaces the current database.
     *
     * @throws Throwable
     */
    private function validateDatabase(string $path): void
    {
        $reader = new Reader($path);
        $reader->metadata();
        $reader->close();
    }

    private function formatBuildDate(?Metadata $metadata): string
    {
        if ($metadata === null || ! is_int($metadata->buildEpoch)) {
            return 'n/a';
        }

        return date('Y-m-d H:i:s', $metadata->buildEpoch);
    }

    private function getErrorOutput(OutputInterface $output): OutputInterface
    {
        return $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
    }
}
