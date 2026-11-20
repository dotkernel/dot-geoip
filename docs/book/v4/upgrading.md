# Upgrading

## Upgrading from 3.x to 4.0

Version 4.0 drops PHP 8.2, fixes the synchronizer command, and makes the package configuration stricter.

Run the following command to upgrade:

```shell
composer require dotkernel/dot-geoip:^4.0
```

## Changes that can break your code

| Change | Symptom | Fix |
| --- | --- | --- |
| PHP 8.2 is not supported | Composer does not install 4.x on PHP 8.2 | Use PHP 8.3 or later, or stay on 3.x |
| The command returns exit code `1` when a download, an extraction or a validation fails | A cron job reports an error where it reported success before. In 3.x the command replaced the database with an empty file | Read the error. The old database file stays unchanged |
| The default database `all` synchronizes only the databases in the configuration | Fewer lines in the output. In 3.x a partial configuration caused a `TypeError` | No action |
| `-d` with a database that is not in the configuration throws | `Database "city" is not configured.` | Add the database under `dot-geoip.databases`, or remove the option |
| `--memory-limit` needs a value | `The "--memory-limit" option requires a value.` | Give a value, for example `256M`, or remove the option |
| `GeoIpCommand::configure()` and `GeoIpCommand::execute()` are `protected` | `Call to protected method Dot\GeoIP\Command\GeoIpCommand::execute()` | Run the command through the console application, or use `Symfony\Component\Console\Tester\CommandTester` in tests |
| The factories and the service throw `InvalidArgumentException` or `RuntimeException`, not `Exception` | No symptom when you catch `Exception` or `Throwable` | No action. Both classes extend `Exception` |
| An IPv4-mapped IPv6 address such as `::ffff:1.2.3.4` becomes `::ffff:1.2.3.0` before the lookup | Different lookup results for these addresses. In 3.x it became `::ffff:0` | No action |

## Register the command

The 3.x README registered the command with `GeoIpCommand::getDefaultName()`.
This method does not exist in `symfony/console` 8, so the call causes a fatal error.
Use the command name:

```php
'geoip:synchronize' => Dot\GeoIP\Command\GeoIpCommand::class,
```

## Other changes

- `LocationData::exchangeArray()` accepts the nested data as arrays, so it accepts the output of `getArrayCopy()`.
- `CountryData::exchangeArray()` sets `isEuMember` to `false` when the key is missing. In 3.x it set `null`.
- The package now declares `symfony/console`, `laminas/laminas-stdlib` and `maxmind-db/reader`. Before, these packages came only from other dependencies.
