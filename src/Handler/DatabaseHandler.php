<?php

declare(strict_types=1);

namespace Dot\GeoIP\Handler;

use Dot\Console\Command\AbstractCommand;
use Dot\GeoIP\Service\LocationService;
use Dot\GeoIP\Service\LocationServiceInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Laminas\Filter\Decompress;
use MaxMind\Db\Reader\Metadata;
use Symfony\Component\Filesystem\Filesystem;
use Laminas\Console\Adapter\AdapterInterface;
use Dot\Console\RouteCollector as Route;

use function basename;
use function date;
use function implode;
use function sprintf;

/**
 * Class DatabaseHandler
 * @package Dot\GeoIP\Handler
 */
class DatabaseHandler extends AbstractCommand
{
    protected array $config;

    protected LocationServiceInterface $locationService;

    /**
     * DatabaseHandler constructor.
     * @param LocationServiceInterface $locationService
     * @param array $config
     */
    public function __construct(LocationServiceInterface $locationService, array $config)
    {
        $this->config = $config;
        $this->locationService = $locationService;
    }

    /**
     * @param Route $route
     * @param AdapterInterface $console
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function __invoke(Route $route, AdapterInterface $console)
    {
        if (empty($this->config)) {
            throw new Exception('Unable to proceed because config data is missing.');
        }

        $quiet = $route->getMatchedParam('quiet') || $route->getMatchedParam('q');

        $fileSystem = new Filesystem();
        if (!$fileSystem->exists($this->config['targetDir'])) {
            $fileSystem->mkdir($this->config['targetDir']);
        }

        $results = [];
        $databases = $this->identifyDatabases($route->getMatchedParam('database'));
        foreach ($databases as $database) {
            $sourcePath = $this->getSourcePath($database);
            $targetPath = $this->getTargetPath($database);

            $results[$database] = [
                'database' => basename($targetPath),
                'build' => [
                    'previous' => null,
                    'current' => null
                ]
            ];

            $oldMetadata = $this->locationService->getDatabaseMetadata($database);
            if ($oldMetadata instanceof Metadata) {
                $results[$database]['build']['previous'] = date('Y-m-d H:i:s', $oldMetadata->buildEpoch);
            }

            $client = new Client();
            $client->get($this->config['databases'][$database]['source'], [RequestOptions::SINK => $sourcePath]);

            $extractor = new Decompress();
            $content = $extractor->getAdapter()->decompress($sourcePath);
            $fileSystem->remove($targetPath, $content);
            $fileSystem->dumpFile($targetPath, $content);
            $fileSystem->remove($sourcePath);

            $newMetadata = $this->locationService->getDatabaseMetadata($database);
            if ($newMetadata instanceof Metadata) {
                $results[$database]['build']['current'] = date('Y-m-d H:i:s', $newMetadata->buildEpoch);
            }
        }

        if ($quiet) {
            return;
        }

        foreach ($results as $result) {
            $console->writeLine($this->getMessage($result));
        }
    }

    /**
     * @param array $result
     * @return string
     */
    private function getMessage(array $result): string
    {
        if (is_null($result['build']['current'])) {
            return sprintf('Failed to install database %s.', $result['database']);
        }

        if (is_null($result['build']['previous']) && !is_null($result['build']['current'])) {
            return sprintf('Database %s has been installed, current version is: %s',
                $result['database'],
                $result['build']['current']
            );
        }

        if ($result['build']['current'] !== $result['build']['previous']) {
            return sprintf(
                'Database %s has been updated from version %s to %s',
                $result['database'],
                $result['build']['previous'],
                $result['build']['current']
            );
        }

        return sprintf(
            'Database %s is already at the latest version: %s',
            $result['database'],
            $result['build']['current']
        );
    }

    /**
     * @param string $database
     * @return string
     */
    private function getSourcePath(string $database): string
    {
        return sys_get_temp_dir() . '/' . basename($this->config['databases'][$database]['source']);
    }

    /**
     * @param string $database
     * @return string
     */
    private function getTargetPath(string $database): string
    {
        return sprintf('%s/%s.mmdb', $this->config['targetDir'], $database);
    }

    /**
     * @param string|null $identifier
     * @return null[]|string[]
     * @throws Exception
     */
    private function identifyDatabases(?string $identifier): array
    {
        if (empty($identifier) || $identifier === LocationService::DATABASE_ALL) {
            return LocationService::DATABASES;
        }

        if (!$this->locationService->isValidDatabaseIdentifier($identifier)) {
            throw new Exception(
                sprintf('Invalid database identifier specified: %s',
                    $identifier,
                    implode(', ', LocationService::DATABASES)
                )
            );
        }

        return [$identifier];
    }
}
