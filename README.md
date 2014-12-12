# Phprest

[![Author](http://img.shields.io/badge/author-@adammbalogh-blue.svg?style=flat-square)](https://twitter.com/adammbalogh)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

# Description

Php Rest Framework.

It extends the [Proton](https://github.com/alexbilbie/Proton) Micro [StackPhp](http://stackphp.com/) compatible Framework.

# Components

* [Orno\Di](https://github.com/orno/di)
* [Orno\Route](https://github.com/orno/route)
* [League\Event](https://github.com/thephpleague/event)
* [Willdurand\Negotiation](https://github.com/willdurand/Negotiation)
* [Willdurand\Hateoas](https://github.com/willdurand/Hateoas)

# Skills

* Dependency injection
* Routing
* Serialization
* Deserialization
* Hateoas
* Api versioning
* Pagination

# ToC

* [Installation](https://github.com/phprest/phprest#installation)
* [Usage](https://github.com/phprest/phprest#usage)
 * [Set up](https://github.com/phprest/phprest#set-up)
 * [Routing](https://github.com/phprest/phprest#routing)
    * [Simple routing](https://github.com/phprest/phprest#simple-routing)
    * [Routing with arguments](https://github.com/phprest/phprest#routing-with-arguments)
    * [Routing through a controller](https://github.com/phprest/phprest#routing-through-a-controller)
    * [Routing through a service controller](https://github.com/phprest/phprest#routing-through-a-service-controller)
    * [Routing with annotations](https://github.com/phprest/phprest#routing-with-annotations)
 * [Api versioning](https://github.com/phprest/phprest#api-versioning)
 * [Serialization, Deserialization, Hateoas](https://github.com/phprest/phprest#serialization-deserialization-hateoas)
    * [Serialization example](https://github.com/phprest/phprest#serialization-example)
    * [Deserialization example](https://github.com/phprest/phprest#deserialization-example)
 * [Pagination](https://github.com/phprest/phprest#pagination)
 * [Responses](https://github.com/phprest/phprest#responses)
    * [1xx, 2xx, 3xx status codes](https://github.com/phprest/phprest#1xx-2xx-3xx-status-codes)
      * [Example](https://github.com/phprest/phprest#example) 
      * [Types](https://github.com/phprest/phprest#types)
    * [4xx, 5xx status codes](https://github.com/phprest/phprest#4xx-5xx-status-codes)
      * [Example](https://github.com/phprest/phprest#example-1)
      * [Types](https://github.com/phprest/phprest#types-1)
 * [Exception handler](https://github.com/phprest/phprest#exception-handler)
    * [On a single exception](https://github.com/phprest/phprest#on-a-single-exception)
    * [Fatal error handler](https://github.com/phprest/phprest#fatal-error-handler)
 * [Dependency Injection Container](https://github.com/phprest/phprest#dependency-injection-container)

# Installation

Install it through composer.

```json
{
    "require": {
        "adammbalogh/phprest": "@stable"
    }
}
```

**tip:** you should browse the [`adammbalogh/phprest`](https://packagist.org/packages/adammbalogh/phprest)
page to choose a stable version to use, avoid the `@stable` meta constraint.

# Usage

## Setup

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Phprest\Application;
use Symfony\Component\HttpFoundation\Request;
use Phprest\Response;
use Phprest\Exception;

# vendorName, apiVersion, debug
$app = new Application('vendor', '0.1', true);

# optional
$app->setApiVersionHandler(function ($apiVersion) {
    if ( ! in_array($apiVersion, ['0.1'])) {

        # tip: list your available versions in the exception
        
        throw new Phprest\Exception\NotAcceptable(PHP_INT_MAX - 3, ['Not supported Api Version']);
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
use Phprest\Response;

class HomeController
{
    public function index(Request $request)
    {
        return new Response\Ok('Hello World!');
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

### Routing with annotations

You have to register your controller.

```php
<?php

$app->registerController('\Foo\Bar\Controller\Home');
```

```php
<?php namespace Foo\Bar\Controller;
# Home.php

use Phprest\Util\Controller;
use Symfony\Component\HttpFoundation\Request;
use Phprest\Response;
use Phprest\Annotation as Phprest;

class Home extends Controller
{
    /**
     * @Phprest\Route(method="GET", path="/foobars/{id}")
     */
    public function get(Request $request, $id)
    {
        return new Response\Ok('Hello World!');
    }
}
```

## Api Versioning

|Accept/Content-Type header can be|Transfers to|
|---------------------------------|------------|
|application/vnd.**Vendor**-v**Version**+json|itself|
|application/vnd.**Vendor**+json; version=**Version**|itself|
|application/vnd.**Vendor**-v**Version**+xml|itself|
|application/vnd.**Vendor**+xml; version=**Version**|itself|
|application/json|application/vnd.**Vendor**-v**Version**+json|
|application/xml|application/vnd.**Vendor**-v**Version**+xml|
|\*/\*|application/vnd.**Vendor**-v**Version**+json|
 
* If Accept header is not parsable
 * then Phprest throws a Not Acceptable exception
 
* If you do a deserialization and Content-Type header is not parsable
 * then Phprest throws an Unsupported Media Type exception

## Serialization, Deserialization, Hateoas

* Phprest will automatically serialize* your response based on the Accept header.
* Phprest can deserialize your content based on the Content-Type header.

Except*:
* If your response is not a Response instance (e.g. it a simple string)
* If your response is empty

### Serialization example

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

### Deserialization example

You have to use the Phprest\Service\Hateoas\Util trait in your controller to do deserialization.

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

## Pagination

```php
<?php
...
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;
...
$paginatedCollection = new PaginatedRepresentation(
    new CollectionRepresentation([$user1, $user2, ...]),
    '/users', # route
    [], # route parameters, should be $request->query->all()
    1, # page, should be (int)$request->query->get('page')
    10, # limit, should be (int)$request->query->get('limit')
    5, # total pages
    'page', # page route parameter name, optional, defaults to 'page'
    'limit', # limit route parameter name, optional, defaults to 'limit'
    true, # absolute URIs
    47 # total number of rows
);
...
return new Response\Ok($paginatedCollection);
```

For more informations please visit the [Hateoas docs](https://github.com/willdurand/Hateoas#dealing-with-collections)

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

### 4xx, 5xx status codes

These are Exceptions.

#### Example

```php
<?php
# ...
$app->get('/', function (Request $request) {
    # ...
    
    throw new \Phprest\Exception\BadRequest();
    
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

## Exception handler

### On a single exception

```php
<?php
# ...
$app->get('/', function (Request $request) {
    throw new \Phprest\Exception\Exception('Code Red!', 9, 503);
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

Phprest can also handle all the non recoverable errors like E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR.

For a clear error message you should do something like this:

```php
<?php
ini_set('display_errors', 'Off');
```

## Dependency Injection Container

See [Proton's doc](https://github.com/alexbilbie/Proton#dependency-injection-container) and for more information please visit [Orno/Di](https://github.com/orno/di).

# Api testing

There are a couple of great tools out there for testing your Api.

* [Postman](http://www.getpostman.com/) and [Newman](https://github.com/a85/Newman)
 * Tip: Create collections in Postman and then run these in Newman
* [Frisby](https://github.com/vlucas/frisby)
 * Frisby is a REST API testing framework built on node.js and Jasmine that makes testing API endpoints easy, fast, and fun.
* [Runscope](https://www.runscope.com/)
 * For Api Monitoring and Testing
