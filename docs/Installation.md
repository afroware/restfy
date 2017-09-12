---
layout: page
title: About
permalink: /about/
---


To install this package you will need:

- Laravel 5.4+ or Lumen 5.4+
- PHP 5.5.9+

You must then modify your `composer.json` file and run `composer update` to include the latest version of the package in your project.

```json
"require": {
    "afroware/restfy": "1.0.0"
}
```

Or you can run the `composer require` command from your terminal.

```


composer require afroware/restfy:1.0.0
```

> At this time the package is still in a developmental stage and as such does not have a **stable** release.
> You may need to set your `minimum-stability` to `dev`.

Once the package is installed the next step is dependant on which framework you're using.

### Laravel

Open `config/app.php` and register the required service provider **above** your application providers.

```php
'providers' => [
    Afroware\Restfy\Provider\LaravelServiceProvider::class
]
```

If you'd like to make configuration changes in the configuration file you can pubish it with the following Aritsan command:

```
php artisan vendor:publish --provider="Afroware\Restfy\Provider\LaravelServiceProvider"
```

### Lumen

Open `bootstrap/app.php` and register the required service provider.

```
$app->register(Afroware\Restfy\Provider\LumenServiceProvider::class);
```

### Facades

There are two facades shipped with the package. You can add either of them should you wish.

#### `Afroware\Restfy\Facade\API`

This is a facade for the dispatcher, however, it also provides helper methods for other methods throughout the package.

#### `Afroware\Restfy\Facade\Route`

This is a facade for the API router and can be used to fetch the current route, request, check the current route name, etc.

[Configuration →](Configuration.md)
