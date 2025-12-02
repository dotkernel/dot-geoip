# Configuration

Copy config file `vendor/dotkernel/dot-geoip/config/autoload/geoip.global.php` into your application's `config/autoload` directory.

Register the library's ConfigProvider by adding the following line to your application's `config/config.php` file:

```php
Dot\GeoIP\ConfigProvider::class,
```

Register the library's synchronizer command by adding the following line to your application's `config/autoload/cli.global.php` file under the `commands` array key:

```php
\Dot\GeoIP\Command\GeoIpCommand::$defaultName => \Dot\GeoIP\Command\GeoIpCommand::class,
```
