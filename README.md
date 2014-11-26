# Phrest

[![Author](http://img.shields.io/badge/author-@adammbalogh-blue.svg?style=flat-square)](https://twitter.com/adammbalogh)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

# Description

Php Rest Micro Framework.

It extends the [Proton](https://github.com/alexbilbie/Proton) Micro [StackPhp](http://stackphp.com/) compatible Framework.

# Components

* [Orno\Di](https://github.com/orno/di)
* [Orno\Route](https://github.com/orno/route)
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
use Phrest\Response;

$app = new Application();
$app['debug'] = true; # it is false by default

$app->get('/', function (Request $request) {
    return new Response\Ok('Hello World!');
});

$app->run();
```

## Content Negotiation

Phrest can Accept:

* application/json
* application/xml
* application/hal+json
* application/hal+xml

## Routing

### Simple routing

```php
<?php
# ...
$app->get('/hello', function (Request $request) { # You can leave the $request variable
    return new Response\Ok('Hello World!');
});
# ...
```

### Routing with arguments

```php
<?php
# ...
$app->get('/hello/{name:word}', function (Request $request, $name) {
    return new Response\Ok('Hello ' . $name);
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
use Phrest\HttpFoundation\Response;

class HomeController
{
    public function index(Request $request)
    {
        return new Response('Hello World!');
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

## Serialization, Hateoas (Content Negotiation)

Let's see a Temperature entity:

```php
<?php namespace Foo\Entity;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\XmlRoot("result")
 *
 * @Hateoas\Relation("self", href = "expr('/temperatures/' ~ object.id)")
 */
class Temperature
{
    /**
     * @var integer
     * @Serializer\Type("integer")
     */
    public $id;

    /**
     * @var integer
     * @Serializer\Type("integer")
     */
    public $value;

    /**
     * @var \DateTime
     * @Serializer\Exclude
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:sO'>")
     */
    public $created;

    /**
     * @param integer $id
     * @param integer $value
     * @param \DateTime $created
     */
    public function __construct($id = null, $value = null, \DateTime $created = null)
    {
        $this->id = $id;
        $this->value = $value;
        $this->created = $created;
    }
}
```

The router:

```php
<?php
# ...
$app->post('/temperatures', function () use ($app) {
    $temperature = new \Foo\Entity\Temperature(1, 32, new \DateTime());
    
    return new Response\Created('/temperatures/1', $temperature);
});
# ...
```

Json response, default (Accept: application/json):

```json
{
    "id": 1,
    "value": 32
}
```

Xml response (Accept: application/xml):

```xml
<result>
  <id>1</id>
  <value>32</value>
</result>
```

Hal+Json response (Accept: application/hal+json):

```json
{
    "id": 1,
    "value": 32,
    "_links": {
        "self": {
            "href": "\/temperatures\/1"
        }
    }
}
```

Hal+Xml response (Accept: application/hal+xml):

```xml
<result>
  <id>1</id>
  <value>32</value>
  <link rel="self" href="/temperatures/1"/>
</result>
```

## Default exception handler

### On a single exception

```php
<?php
# ...
$app->get('/', function (Request $request) {
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

See [Proton's doc](https://github.com/alexbilbie/Proton#dependency-injection-container) and for more information please visit [Orno/Di](https://github.com/orno/di).
