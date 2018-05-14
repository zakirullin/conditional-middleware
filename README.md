# zakirullin/conditional-middleware

[![Build Status](https://img.shields.io/travis/zakirullin/conditional-middleware.svg?style=flat-square)](https://travis-ci.org/zakirullin/conditional-middleware)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/zakirullin/conditional-middleware.svg?style=flat-square)](https://scrutinizer-ci.com/g/zakirullin/conditional-middleware/)
![PHP from Packagist](https://img.shields.io/packagist/php-v/zakirullin/conditional-middleware.svg?style=flat-square)
![GitHub commits](https://img.shields.io/github/commits-since/zakirullin/conditional-middleware/0.1.0.svg?style=flat-square)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

PSR-15 middleware that makes it possible to add conditional middlewares, based on `request`

## Requirements

* PHP >= 7.1
* A [PSR-7](https://packagist.org/providers/psr/http-message-implementation) http message implementation ([Diactoros](https://github.com/zendframework/zend-diactoros), [Guzzle](https://github.com/guzzle/psr7), [Slim](https://github.com/slimphp/Slim), etc...)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [zakirullin/conditional-middleware](https://packagist.org/packages/zakirullin/conditional-middleware).

```sh
composer require zakirullin/conditional-middleware 
```

## PHP

```php
$shouldProtect = function (\Psr\Http\Message\ServerRequestInterface $request) {
    $handler = $request->getAttribute('handler');
    return $handler != 'login';
};
$getIdentity = function (\Psr\Http\Message\ServerRequestInterface $request) {
    $session = $request->getAttribute('session');
    return [$session->get('userId')];
};

$dispatcher = new Dispatcher([
    ...
    new \Zakirullin\Middlewares\CSRF($shouldProtect, $getIdentity, 'secret'),
    ...
]);
```

## Options

```php 
__construct(
    callable $shouldProtect,
    callable $getIdentity,
    string $secret,
    string $attribute = self::ATTRIBUTE,
    int $ttl = self::TTL,
    string $algorithm = self::ALGORITHM
)
```

#### `name(string $name)`

The session name. If it's not defined, the default `PHPSESSID` will be used.

---

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
