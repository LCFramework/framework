# Persistent Data

Laravel's config files are a great way to store static values that rarely, if ever change.
However, this isn't a flexible solution when storing mutable data that can be modified
at any time via the administration panel.

Instead of saving all the persistent values into the cache, that risks being cleared whenever
the cache is cleared (making the data not so persistent after all). LCFramework takes a different
approach by providing a driver based API to storing these settings. Currently, there are two
drivers officially supported:

- File `(default)`
- Database

The file and database drivers serve two different scenarios. The file-based driver is enabled by
default, and is generally the fastest solution at reading and writing persistent data. However,
the file-based driver reads all the persistent settings into memory, on every request. This
isn't usually an issue when small and simple data structures are used, but if you intend on
storing large data structures, the database driver provides an alternative solution that can
be more memory-efficient at the cost of speed.

## Saving data

```php
use LCFramework\Framework\Setting\Facade\Settings;

// We provide a key (that's used to identify the data)
// and we provide a value, the value can be of any type
Settings::put('example.array', ['example-data-value']);
Settings::put('example.string', 'example-data-value');
Settings::put('example.integer', 1234);

// Alternatively, we can use the global helpers
settings()->put('key', 'value');

// or
settings_put('key', 'value');
```

## Reading data

```php
use LCFramework\Framework\Setting\Facade\Settings;

Settings::get('example.array'); // ['example-data-value']
Settings::get('example.string') // 'example-data-value';
Settings::get('example.integer') // 1234;

// Alternatively, we can use the global helpers
settings()->get('key'); // 'value'

// or
settings('key');

// or
settings_get('key')

// We can even use dot-notation to get a group of settings by their namespace
$settings = settings('example');

/**
 * $settings = [
 *     'array' =>  ['example-data-value'],
 *     'string' => 'example-data-value',
 *     'integer' => 1234
 * ];
 */
dd($settings);
```

## Deleting data

```php
use LCFramework\Framework\Setting\Facade\Settings;

Settings::forget('example.array'); // ['example-data-value']
Settings::forget('example.string') // 'example-data-value';
Settings::forget('example.integer') // 1234;

// Alternatively, we can use the global helpers
settings()->forget('key'); // 'value'

// or
settings_forget('key')

// We can even use dot-notation to forget a group of settings by their namespace
$settings = settings('example');
```
