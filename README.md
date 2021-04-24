# ArrayQuery

A simple way to extract values from an array.

**Real world:** sometimes you need to work with an array with a lot of unnecessary information within a context, and it can be a problem. The `ArrayQuery` package can help simplify the return in a simple way, even with a multidimensional array.

## Installation

### With Composer

```
$ composer require rentalhost/vanilla-array-query
```

```json
{
    "require": {
        "rentalhost/vanilla-array-query": "^0.1"
    }
}
```

```php
require 'vendor/autoload.php';

use Rentalhost\Vanilla\ArrayQuery\ArrayQuery;

ArrayQuery::query(
    [ 'name' => 'John Doe', 'age' => 30 ],
    [ 'age' ]
) === [ 'age' => 30 ];
```

## Usage

### Simple extraction

As in the example above, you can extract information easily just by specifying the keys you want to extract from the source array.

Let's assume that we have an array containing information about a page, but we want to extract only its route and title, while all other information is irrelevant at the moment.

The output will capture from the first argument (the source array) only the keys indicated by the second argument (the query array), in the order in which it is requested.

```php
$page = [ 
    'route'       => '/home', 
    'title'       => 'Home', 
    'description' => 'Initial page',
];

ArrayQuery::query($page, [ 'route', 'title' ]) === [
    'route' => '/home', 
    'title' => 'Home', 
];
```

### Bidimensional extraction

It is also possible to extract information from bidimensional array in a simple way.

In this case, the query key will represent the key to be extracted from the source, and its value in array will represent the keys to be extracted from the second dimension.

So, let's assume that we have a bidimensional array containing information about a page, and we want to extract only some information related to it, keeping the original structure of the source array.

```php
$page = [ 
    'header' => [ 
        'title'       => 'Home', 
        'description' => 'Initial page',
    ],
];

ArrayQuery::query($page, [ 'header' => [ 'title' ] ]) === [ 
    'header' => [ 
        'title' => 'Home', 
    ],
];
```

## Multidimensional extraction

And going even further, we can extract information from an array even in a multidimensional source.

In this case, we pass a query containing an array of arrays, and in its keys we indicate what information we want to extract.

So, supposing that we have an array containing various information from several pages, but we are only interested in obtaining its routes, and nothing more.

```php
$pages = [
    [
        'route' => '/home',
        'title' => 'Home'
    ],
    [
        'route' => '/admin',
        'title' => 'Administrative'    
    ],
];

ArrayQuery::query($pages, [ [ 'route' ] ]) === [
    [
        'route' => '/home',
    ],
    [
        'route' => '/admin',
    ],
];
```

## Customized extraction

In some cases, the source array is too complex in a multidimensional way, and sometimes you need to simplify the output for something easier to work with.

For example, let's say you have a page, and the only thing that interests you is the title. But the title is inside the key header with a lot of information that you don't need in context.

In this case, you can enter a custom key that has a function as a value. This function will receive all the information existing at that level, and its return will be the output to that new key.

In the example below, note that the source array does not have a title key, but we will create it on-the-fly from the existing value inside the title key that is inside the header key.

```php
$page = [ 
    'header' => [ 
        'title' => 'Home', 
        'description' => 'Initial page' 
    ] 
];

ArrayQuery::query($page, [ 'title' => static function (array $page) {
    return $page['header']['title'];
} ]) === [ 
    'title' => 'Home', 
];
```

This functionality will also allow you to transform an existing key, filtering its elements, for example.

Let's assume, then, that we have a page with a simplified route, but we want to make it absolute. The key will be kept as a route, but its value will change.

It is important to remember that the callable will receive the entire array of that context (the `$page`, in this case), and not the value of the key you are reassigning.

```php
$page = [ 'route' => '/home' ];

ArrayQuery::query($page, [ 'route' => static function (array $page) {
    return url($page['route']);
} ]) === [ 
    'route' => 'https://.../home', 
];
```
