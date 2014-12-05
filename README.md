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

# Skills

* Dependency Injection
* Routing
* Serialization
* Deserialization
* Hateoas
* Api Versioning
* Serviceable

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
use Phrest\Exception;

# vendorName, apiVersion, debug
$app = new Application('vendor', 1, true);

# optional
$app->setApiVersionHandler(function ($apiVersion) {
    if ( ! in_array($apiVersion, [1, 2, 3])) {

        # tip: list your available versions in the exception
        
        throw new Phrest\Exception\NotAcceptable(PHP_INT_MAX - 3, ['Not supported Api Version']);
    }
});

$app->get('/', function (Request $request) {
    return new Response\Ok('Hello World!');
});

$app->run();
```

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

## Api Versioning

* Accept/Content-Type header can be:
 * application/vnd.vendor-v*Version*+json
 * application/vnd.vendor+json; version=*Version*
 * application/vnd.vendor-v*Version*+xml
 * application/vnd.vendor+xml; version=*Version*

* If Accept header is \*/*
 * then phrest automatically translate this to application/vnd.vendor-v*Version*+json
 
* If Accept header is not parsable
 * then phrest throws a Not Acceptable exception
 
* If you do a deserialization and Content-Type header is not parsable
 * then phrest throws an Unsupported Media Type exception

## Serialization, Deserialization, Hateoas

* Phrest will automatically serialize* your response based on the Accept header.
* Phrest can deserialize your content based on the Content-Type header.

Except*:
* If your response is not a Response instance (e.g. it a simple string)
* If your response is empty

### Serialization Example

Let's see a Temperature entity:

```php
<?php namespace Foo\Entity;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\XmlRoot("result")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route("/temperatures", parameters = {"id" = "expr(object.id)"}, absolute = false)
 * )
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
     * @Serializer\Type("DateTime")
     * @Serializer\Since("2")
     * @Serializer\Exclude
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

Json response (Accept: application/vnd.vendor+json; version=1):

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

Xml response (Accept: application/vnd.vendor+xml; version=1):

```xml
<result>
  <id>1</id>
  <value>32</value>
  <link rel="self" href="/temperatures/1"/>
</result>
```

### Deserialization Example

You have to use the Phrest\Service\Hateoas\Util trait in your controller to do deserialization.

```php
...
use JMS\Serializer\Exception\RuntimeException;
...
    public function post(Request $request)
    {
        try {
            /** @var \Foo\Entity\Temperature $temperature */
            $temperature = $this->deserialize('\Foo\Entity\Temperature', $request);
        } catch (RuntimeException $e) {
            throw new Exception\UnprocessableEntity(0, [new Service\Validator\Entity\Error('', $e->getMessage())]);
        }
    }
...
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
    "message": "Code Red!",
    "details": []
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
```

## Responses

There are several responses you can use by default, one of them is the Ok response.

### 1xx, 2xx, 3xx status codes

These are simple Response objects.

#### Example

```php
<?php
# ...
$app->get('/', function (Request $request) {
    return new Response\Ok('Hello World!');
});
# ...
```

#### Types

|Responses|
|-------------|
|Accepted|
|Created|
|NoContent|
|NotModified|
|Ok|

### 4xx, 5xx

These are Exceptions.

#### Example

```php
<?php
# ...
$app->get('/', function (Request $request) {
    # ...
    
    throw new \Phrest\Exception\BadRequest();
    
    # ...
});
# ...
```

#### Types

|Exceptions|
|----------|
|BadRequest|
|Conflict|
|Forbidden|
|Gone|
|InternalServerError|
|MethodNotAllowed|
|NotAcceptable|
|NotFound|
|TooManyRequests|
|PreconditionFailed|
|TooManyRequests|
|Unauthorized|
|UnprocessableEntity|
|UnsupportedMediaType|

## Dependency Injection Container

See [Proton's doc](https://github.com/alexbilbie/Proton#dependency-injection-container) and for more information please visit [Orno/Di](https://github.com/orno/di).
