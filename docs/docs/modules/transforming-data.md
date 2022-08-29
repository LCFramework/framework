# Transforming data

LCFramework provides an API allowing developers to hook into "events" to transform
any data structure. For example, mutating content (such as forms) before displaying it 
on the front-end. The LCFramework core exposes an API allowing modules and themes to
transform core functionality without overwriting the core code.

::: warning
LCFramework is currently in `alpha` status. This means there may be bugs,
and the API may still change between minor versions.
:::

## Transform data

The `Transformer` facade allows you to easily send data out to listeners to mutate. After all
the mutators have been called, you'll be left with final result of the mutated data.

In the below example, we give an array to the `Transformer` facade. Along with this array,
we specify a key that describes this data. This key is similar to an event name, and it should
represent what the data is. The key should also be as unique as possible to prevent conflicts.

::: tip
One common usage of transforming data, is for [mutating forms](#). This allows for incredibly
flexible forms that can have its inputs be conditionally changed by modules and themes.
:::

```php
use LCFramework\Framework\Transformer\Facade\Transformer;

// The data we want to mutate
$user = [
    'full_name' => 'John Doe'
];

// A listener is registered by a module, or a theme
// In this example, we'll take the full name of a user
// and insert their first and last names
Transformer::register('user.model', function(array $value) {
    $parts = explode(' ', $value['full_name']),

    return [
        ...$value,
        'first_name' => $parts[0],        
        'last_name' => $parts[1]        
    ];
});

// Send the data to the listeners
$user = Transformer::transform('user.model', $user);

// [ 'full_name' => 'John Doe', 'first_name' => 'John', 'last_name' => 'Doe' ]
dd($user);
```

Listeners can conditionally mutate the data:

```php
use LCFramework\Framework\Transformer\Facade\Transformer;

Transformer::register('user.model', function(array $value) {
    // If `full_name` isn't set, then just return the data.
    if(!isset($value['full_name'])) {
        return $value;
    }
    
    $parts = explode(' ', $value['full_name']),

    return [
        ...$value,
        'first_name' => $parts[0],        
        'last_name' => $parts[1]        
    ];
});
```

We can even make use of dependency-injection and include services straight from the container:

```php
use Illuminate\Log\LogManager;
use LCFramework\Framework\Transformer\Facade\Transformer;

Transformer::register('user.model', function(LogManager $logger, array $value) {
    // If `full_name` isn't set, then just return the data.
    if(!isset($value['full_name'])) {
        return $value;
    }
    
    $parts = explode(' ', $value['full_name']),
    
    $logger->debug(sprintf('Mutating user: "%s"', $value['full_name']));

    return [
        ...$value,
        'first_name' => $parts[0],        
        'last_name' => $parts[1]        
    ];
});
```
