# Phrest

[![Author](http://img.shields.io/badge/author-@adammbalogh-blue.svg?style=flat-square)](https://twitter.com/adammbalogh)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

# Description

Php Rest Micro Framework.

It extends the [Proton](https://github.com/alexbilbie/Proton) Micro [StackPhp](http://stackphp.com/) compatible Framework.

# Components

* [Orno\Route](https://github.com/orno/route)
* [Orno\Di](https://github.com/orno/di)
* [League\Event](https://github.com/thephpleague/event)
* [Willdurand/StackNegotiation](https://github.com/willdurand/StackNegotiation)
* [Willdurand/Hateoas](https://github.com/willdurand/Hateoas)

# Installation

Install it through composer.

```json
{
    "require": {
        "adammbalogh/phrest": "@stable"
    }
}
```

**tip:** you should browse the [`adammbalogh/phrest`](https://packagist.org/packages/adammbalogh/phrest)
page to choose a stable version to use, avoid the `@stable` meta constraint.

# Usage

## Set up

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Phrest\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Application();
$app['debug'] = true; # it is false by default

$app->get('/', function (Request $request, Response $response) {
    return $response->setContent('Hello World!');
});

$app->run();
```

## Routing

### Simple routing with arguments

```php
<?php
# ...
$app->get('/hello/{name:word}', function (Request $request, Response $response, array $args) {
    return $response->setContent('Hello ' . $args['name']);
});
# ...
```

### Routing through a controller

```php
<?php
# index.php

# ...
$app->get('/', '\Foo\Bar\HomeController::index'); # calls index method on HomeController class
# ...
```

```php
<?php namespace Foo\Bar;
# HomeController.php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function index(Request $request, Response $response, array $args)
    {
        return $response->setContent('Hello World!');
    }
}
```

### Routing through a service controller

```php
<?php
# ...
$app['HomeController'] = function () {
    return new \Foo\Bar\HomeController();
};

$app->get('/', 'HomeController::index');
# ...
```

## Hateoas, Serialization

## Exceptions

## Di


