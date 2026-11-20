# Manage the databases

You can download or update one database by running the following command:

```shell
php ./bin/cli.php geoip:synchronize -d {DATABASE}
```

Where _{DATABASE}_ takes one of the following values: `asn`, `city`, `country`.
The database must be in the configuration.

You can download or update all the configured databases at once by running the following command:

```shell
php ./bin/cli.php geoip:synchronize
```

For each database, the output shows the database identifier, the previous build date and the current build date:

```text
asn: n/a -> 2026-11-01 02:52:43
city: n/a -> 2026-11-01 02:48:12
country: n/a -> 2026-11-01 02:47:39
```

Get help for this command by running:

```shell
php bin/cli.php geoip:synchronize --help
```

## Errors

The command replaces a database only after it downloads a file, extracts it and opens it as a valid database.
If one of these steps fails, the old database file stays unchanged.
The command shows the error for that database, continues with the next database, and returns exit code `1`.

If you run the command as a cron job, check its exit code.
The `-q` or `--quiet` option hides all output, also the errors.
You can verify this with the version of `symfony/console` that your application uses.

## Memory limit

By default, the synchronizer command uses up to 128 MB of memory.
To use more memory, give the `memory-limit` option:

```shell
php bin/cli.php geoip:synchronize --memory-limit 256M
```

The option needs a value. You can use the following formats: `-1` (no limit), `131072K`, `256M`, `1G`.
The unit can also be lower case, for example `1g`.
