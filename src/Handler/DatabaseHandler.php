<?php

declare(strict_types=1);

namespace Dot\GeoIP\Handler;

use Dot\Console\Command\AbstractCommand;
use Dot\GeoIP\Service\LocationService;
use Dot\GeoIP\Service\LocationServiceInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use MaxMind\Db\Reader\Metadata;
use PharData;
use Symfony\Component\Filesystem\Filesystem;
use Laminas\Console\Adapter\AdapterInterface;
use Laminas\Text\Table\Row;
use Laminas\Text\Table\Table;
use Dot\Console\RouteCollector as Route;

use function array_key_exists;
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
    /** @var array $config */
    protected $config;

    /** @var LocationServiceInterface $locationService */
    protected $locationService;

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
     */
    public function __invoke(Route $route, AdapterInterface $console)
    {
        if (empty($this->config)) {
            exit('Unable to proceed because config data is missing.');
        }

        $table = new Table(['columnWidths' => [23, 21, 21, 100]]);
        $table->setAutoSeparate(Table::AUTO_SEPARATE_HEADER);
        $table->setPadding(1);
        $table->appendRow(['Database', 'Previous build', 'Current build', 'Info']);

        $databases = $this->identifyDatabases($route->getMatchedParam('database'));
        foreach ($databases as $database) {
            $row = new Row();
            $row->createColumn(LocationService::DATABASES[$database]);

            $oldMetadata = $this->locationService->getDatabaseMetadata($database);
            if ($oldMetadata instanceof Metadata) {
                $row->createColumn(date('Y-m-d H:i:s', $oldMetadata->buildEpoch));
            } else {
                $row->createColumn('n/a');
            }

            try {
                $localTempDir = $this->config['tempDir'];
                $localTempFile = $localTempDir . '/' . basename($this->config['databases'][$database]['source']);

                $client = new Client();
                $client->get($this->config['databases'][$database]['source'], [RequestOptions::SINK => $localTempFile]);

                $phar = new PharData($localTempFile);
                $phar->extractTo($localTempDir, null, true);
                $localTempDir .= '/' . $phar->getBasename();

                $fileSystem = new Filesystem();
                $fileSystem->mirror($localTempDir, $this->config['databases'][$database]['target'], null, [
                    'override' => true
                ]);
                $fileSystem->remove($localTempDir);
                $fileSystem->remove($localTempFile);

                $newMetadata = $this->locationService->getDatabaseMetadata($database);
                if ($newMetadata instanceof Metadata) {
                    $row->createColumn(date('Y-m-d H:i:s', $newMetadata->buildEpoch));
                } else {
                    $row->createColumn('n/a');
                }
            } catch (Exception $exception) {
                $row
                    ->createColumn(date('Y-m-d H:i:s', $oldMetadata->buildEpoch))
                    ->createColumn($exception->getMessage());
            }

            $table->appendRow($row);
        }

        $console->writeLine(sprintf('Running %s for the following database(s): %s',
            $route->getName(),
            implode(', ', $databases)
        ));
        $console->write($table->render());
    }

    /**
     * @return array
     */
    public static function getValidIdentifiers(): array
    {
        return [
            LocationService::DATABASE_ALL,
            LocationService::DATABASE_ASN,
            LocationService::DATABASE_CITY,
            LocationService::DATABASE_COUNTRY,
        ];
    }

    /**
     * @param string $identifier
     * @return array
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
            exit(sprintf('Invalid database identifier: %s. Use one of the following identifiers: %s.',
                $identifier,
                implode(', ', self::getValidIdentifiers())
            ));
        }

        return [$identifier];
    }
}
