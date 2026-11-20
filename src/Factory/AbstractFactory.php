<?php

declare(strict_types=1);

namespace Dot\GeoIP\Factory;

use Dot\GeoIP\Service\LocationService;
use InvalidArgumentException;

use function array_key_exists;
use function is_array;
use function is_string;
use function sprintf;

abstract class AbstractFactory
{
    public const MESSAGE_MISSING_LOCATION_SERVICE  = 'Unable to find LocationService.';
    public const MESSAGE_MISSING_CONFIG            = 'Unable to find dot-geoip config.';
    public const MESSAGE_MISSING_CONFIG_TARGET_DIR = 'Missing/invalid dot-geoip config: targetDir';
    public const MESSAGE_MISSING_CONFIG_DATABASES  = 'Missing/invalid dot-geoip config: databases';
    public const MESSAGE_MISSING_CONFIG_DATABASE   = 'Missing/invalid dot-geoip config database: %s';

    /**
     * @param array<array-key, mixed> $config
     * @throws InvalidArgumentException
     */
    protected function validateConfig(array $config): void
    {
        if (empty($config)) {
            throw new InvalidArgumentException(static::MESSAGE_MISSING_CONFIG);
        }

        if (
            ! array_key_exists('targetDir', $config)
            || ! is_string($config['targetDir'])
            || empty($config['targetDir'])
        ) {
            throw new InvalidArgumentException(static::MESSAGE_MISSING_CONFIG_TARGET_DIR);
        }

        if (
            ! array_key_exists('databases', $config)
            || ! is_array($config['databases'])
            || empty($config['databases'])
        ) {
            throw new InvalidArgumentException(static::MESSAGE_MISSING_CONFIG_DATABASES);
        }

        $found     = false;
        $databases = [
            LocationService::DATABASE_ASN,
            LocationService::DATABASE_CITY,
            LocationService::DATABASE_COUNTRY,
        ];
        foreach ($databases as $database) {
            if (! array_key_exists($database, $config['databases'])) {
                continue;
            }

            $found    = true;
            $dbConfig = $config['databases'][$database];
            if (
                ! is_array($dbConfig)
                || ! array_key_exists('source', $dbConfig)
                || ! is_string($dbConfig['source'])
                || empty($dbConfig['source'])
            ) {
                throw new InvalidArgumentException(
                    sprintf(static::MESSAGE_MISSING_CONFIG_DATABASE, $database)
                );
            }
        }

        if (false === $found) {
            throw new InvalidArgumentException(static::MESSAGE_MISSING_CONFIG_DATABASES);
        }
    }
}
