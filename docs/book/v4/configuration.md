# Configuration

## Register the package

Copy the config file `vendor/dotkernel/dot-geoip/config/autoload/geoip.global.php` into the `config/autoload` directory of your application.

Register the ConfigProvider of the package.
Add the following line to the `config/config.php` file of your application:

```php
Dot\GeoIP\ConfigProvider::class,
```

Register the synchronizer command.
Add the following line under the `commands` array key in the `config/autoload/cli.global.php` file of your application:

```php
'geoip:synchronize' => Dot\GeoIP\Command\GeoIpCommand::class,
```

## Configuration keys

The configuration is under the `dot-geoip` key.

```php
return [
    'dot-geoip' => [
        'targetDir' => getcwd() . '/data/geoip',
        'databases' => [
            Dot\GeoIP\Service\LocationService::DATABASE_ASN     => [
                'source' => 'https://download.db-ip.com/free/dbip-asn-lite-{year}-{month}.mmdb.gz',
            ],
            Dot\GeoIP\Service\LocationService::DATABASE_CITY    => [
                'source' => 'https://download.db-ip.com/free/dbip-city-lite-{year}-{month}.mmdb.gz',
            ],
            Dot\GeoIP\Service\LocationService::DATABASE_COUNTRY => [
                'source' => 'https://download.db-ip.com/free/dbip-country-lite-{year}-{month}.mmdb.gz',
            ],
        ],
    ],
];
```

| Key | Description |
| --- | --- |
| `targetDir` | The directory for the database files. The command creates it if it does not exist. Each database is saved as `<identifier>.mmdb`, for example `asn.mmdb` |
| `databases` | The databases to download. The keys are `asn`, `city` and `country` |
| `databases.<identifier>.source` | The URL of a gzip-compressed database file |

The command replaces `{year}` with the current year, for example `2026`.
It replaces `{month}` with the current month as two digits, for example `03`.

You can configure only some databases.
The command then synchronizes only those databases.
The lookup methods that need a missing database return an object with the `error` property set.
`getLocation()` needs the `city` database.
