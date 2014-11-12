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
* [Willdurand/Negotiation](https://github.com/willdurand/Negotiation)
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

For more information please visit [Orno/Route](https://github.com/orno/route).

## Hateoas, Serialization

Let's see a Humidity entity:

```php
<?php namespace Rest\Entity;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\XmlRoot("humidity")
 *
 * @Hateoas\Relation("self", href = "expr('/sensors/humidity/' ~ object.getId())")
 */
class Humidity
{
    /**
     * @var integer
     * @Serializer\Type("integer")
     */
    private $id;

    /**
     * @var integer
     * @Serializer\Type("integer")
     */
    private $value;

    /**
     * @param integer $id
     * @param integer $value
     */
    public function __construct($id, $value)
    {
        $this->id = $id;
        $this->value = $value;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }
}
```

The router:

```php
<?php
# ...
$app->get('/', function () use ($app) {
    $response = new \Orno\Http\Response('', 200);
    $humidity = new \Rest\Entity\Humidity(1, 78);
    
    return $app->serviceSerializer($humidity, Request::createFromGlobals(), $response);
});
# ...
```

Json response (default):

```json
{
    "value": 78,
    "_links": {
        "self": {
            "href": "\/sensors\/humidity\/1"
        }
    }
}
```

Xml response (Accept: application/xml):

```xml
<humidity>
    <value>78</value>
    <link rel="self" href="/sensors/humidity/1"/>
</humidity>
```

## Default exception handler

### On a single exception

```php
<?php
# ...
$app->get('/', function (Request $request, Response $response) {
    throw new \Phrest\Exception\Exception('Code Red!', 9, 503);
});
# ...
```

The response is content negotiationed (xml/json), the status code is 503.

```json
{
    "code": 9,
    "message": "Code Red!"
}
```

```xml
<result>
    <code>9</code>
    <message>
        <![CDATA[Code Red!]]>
    </message>
</result>
```

### Fatal error handler

Phrest can also handle all the non recoverable errors like E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR.

For a clear error message you should do something like this:

```php
<?php
ini_set('display_errors', 'Off');
error_reporting(-1);
```

## Dependency Injection Container

See [Proton's doc](https://github.com/alexbilbie/Proton#dependency-injection-container) and for more information please visit [Orno/Route](https://github.com/orno/route).

## Api documentation
