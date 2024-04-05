# Manage GeoLite2 database

You can download/update a specific GeoLite2 database, by running the following command:

```bash
php bin/cli.php geoip:synchronize -d {DATABASE}
```

Where _{DATABASE}_ takes one of the following values: `asn`, `city`, `country`.

You can download/update all GeoLite2 databases at once, by running the following command:

```bash
php bin/cli.php geoip:synchronize
```

The output should be similar to the below, displaying per row: `database identifier`: `previous build datetime` -> `current build datetime`.

```text
asn: n/a -> 2021-07-01 02:09:34
city: n/a -> 2021-07-01 02:09:20
country: n/a -> 2021-07-01 02:05:12
```

Get help for this command by running `php bin/cli.php help geoip:synchronize`.

**Tip**: If you set up the synchronizer command as a cronjob, you can add the `-q|--quiet` option, and it will output data only if an error has occurred.
