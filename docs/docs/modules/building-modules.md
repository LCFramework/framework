# Building Modules

Modules in this definition are essentially packages of code written in PHP to extend or customise
functionality.

::: warning
LCFramework is currently in `alpha` status. This means there may be bugs,
and the API may still change between minor versions.
:::

## Manifest

LCFramework requires modules to have a manifest file that provides common information to the
framework that is uses for administration, dependency management, and booting your module.

Since LCFramework is built on modern PHP, LCFramework utilises Composer and thus the 
`composer.json` file as your manifest file. This saves you from having to write both a manifest
file for Composer to manage to dependencies and a separate manifest file for your module.

### The basics

Your manifest file must contain at least the following, everything else is completely your choice:

- Name
- Description
- Version
- LCFramework module definition

You may use the below example as a starting point for your module:

```json
{
    "name": "your-name/module-name",
    "description": "Build something great!",
    "version": "1.0",
    "extra": {
        "lcframework": {
            "module": {
                "providers": [   
                    "YourName\\ModuleName\\ModuleNameServiceProvider"
                ]
            }
        }
    }
}
```

::: tip
LCFramework will automatically include the Composer autoloader by your module, and
boot any [Service Providers](#service-providers) defined in your manifest.
:::

## Service providers

Service providers are the entry-point into your module. You can have as many service providers
as you wish, and LCFramework will automatically boot them when the module is enabled.
To learn more about Service Providers, you can use the [Laravel documentation](https://laravel.com/docs/providers).

Service providers must be defined in your Composer file, using the full namespace and classname.
