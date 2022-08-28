# Configuration

There are 2 ways to configure your application:

- [Administration Panel](#)
- [Config files](#config-files)

::: warning
LCFramework is currently in `alpha` status. This means there may be bugs,
and the API may still change between minor versions.
:::

## Accessing config values

To access the data stored inside the configuration files, see [Laravel Configuration](https://laravel.com/docs/9.x/configuration).

## Environment configuration

Every project has a `.env` file in the project root. This file contains sensitive information regarding the
application.

::: warning
The `.env` file contains all your sensitive information, so it should never be publicly shared!
:::

If you've made changes to the `.env` file after caching your configs, your changes will not take effect.
Run the following command to ensure the application has the latest changes:

```shell
php artisan config:cache
```

## Config files

Configuration files are stored in the `config` directory. For example, you can find the LCFramework configuration
at `config/lcframework.php`.

All configuration files must return an array.
