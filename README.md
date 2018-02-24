# Donphan

Utility traits for type-safe &amp; immutable entity in PHP.

## Install 

https://packagist.org/packages/fsubal/donphan

```
composer require fsubal/donphan
```

## Why Donphan ?

In PHP application without certain frameworks, `array`s are often used as domain models.

```php
<?php

$user = [
  'user_id' => validateInt($_POST['user_id']),
  'name' => validateString($_POST['name']),
];

...

function doSomethingForUser(array $user)
{
  $name = $user['name'];
  ...
}
```

When refactoring these mess (or migrating to completely new framework) seems too hard, Donphan may help you.

## What Donphan does

It provides two utility traits.

- `\Donphan\Validatable`: provides `::validate` method, and lifecycle hooks.
- `\Donphan\Immutable`: provides `::from` factory method.

```php
<?php

final class User
{
  use \Donphan\Immutable;
 
  const REQUIRED = [
    'user_id' => 'numeric',
    'name' => 'string'
  ];
}

// and then
$user = User::from([
  'user_id' => $_POST['user_id'],
  'name' => $_POST['name']
]);

function doSomethingForUser(User $user)
{
  $name = $user->name;
  ...
}
```

Not perfect, but now you have type safety with ease.

## Type checking

Donphan supports these types.

- `mixed`
- `int`
- `float`
- `numeric`
- `string`
- `boolean`
- `array`
- and any defined `class` name.

`numeric` looks like an original type ? Yes, but it is just validated by `is_numeric` ( http://php.net/manual/ja/function.is-numeric.php ).

It is almost like `int|string`, useful for some ids in `$_GET` or `$_POST` or something.

These types are not supported.

- `null`
- `resource`
- `callable`
- `Closure`
- `Exception`
- `Error`

## Lifecycle methods

If you want to have default value ? Or if you allow additional validation to your `Immutable` object ?

Then, the lifecycle methods might be needed.

`\Donphan\Validatable` provides two lifecycle methods.

- `beforeTypeCheck`: Executed just before type checking. It gets original params, and you must return an `array`.
- `afterTypeCheck`: Executed just before type checking. It gets original params, and you must NOT return anything.

`beforeTypeCheck` is useful for mutating the original array, and `afterTypeCheck` is useful for performing additional validations.

Example below

```php
final class User
{
  use \Donphan\Immutable;
 
  const REQUIRED = [
    'user_id' => 'numeric',
    'name' => 'string'
  ];
  
  const OPTIONAL = [
    'url' => 'string'
  ];
  
  public static function beforeTypeCheck(array $params)
  {
    if (!isset($params['url'])) {
      $params['url'] = 'https://example.com';
    }
    return $params;
  }
  
  public static function afterTypeCheck(array $params)
  {
    if (strlen($params['name']) == 0) {
      throw new \InvalidArgumentException('params[name] must not be empty!');
    }
  }
}
```

Note that the `url` added in `beforeTypeCheck` is also type checked (if it is written in `REQUIRED` or `OPTIONAL`).

## Requirements

PHP 5.6+ ( Needs to be writable `const` with array in classes )

## LICENSE

This is licensed under MIT License.
