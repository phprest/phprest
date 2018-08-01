# Phprest

[![Author](http://img.shields.io/badge/author-@adammbalogh-blue.svg?style=flat-square)](https://twitter.com/adammbalogh)
[![Build Status](https://img.shields.io/travis/phprest/phprest/master.svg?style=flat-square)](https://travis-ci.org/phprest/phprest)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/phprest/phprest.svg?style=flat-square)](https://scrutinizer-ci.com/g/phprest/phprest/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/phprest/phprest.svg?style=flat-square)](https://scrutinizer-ci.com/g/phprest/phprest)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/phprest/phprest.svg?style=flat-square)](https://packagist.org/packages/phprest/phprest)
[![Total Downloads](https://img.shields.io/packagist/dt/phprest/phprest.svg?style=flat-square)](https://packagist.org/packages/phprest/phprest)

# Game over

This project is not maintained anymore!

# Description

REST-like PHP micro-framework.

It's based on the [Proton](https://github.com/alexbilbie/Proton) ([StackPhp](http://stackphp.com/) compatible) micro-framework.

Phprest gives you only the very basics to build your own architecture within your own framework and assemble any folder structure you like. It is just a thin layer around your application with the help of some great libraries.

# Components

* [League\Container](https://github.com/thephpleague/container)
* [League\Route](https://github.com/thephpleague/route)
* [League\Event](https://github.com/thephpleague/event)
* [League\BooBoo](https://github.com/thephpleague/booboo)
* [Willdurand\Negotiation](https://github.com/willdurand/Negotiation)
* [Willdurand\Hateoas](https://github.com/willdurand/Hateoas)
* [Monolog\Monolog](https://github.com/Seldaek/monolog)
* [Stack\Builder](https://github.com/stackphp/builder)

# Skills

* Dependency injection
* Routing
* Error handling
* Serialization
* Deserialization
* HATEOAS
* API versioning
* Pagination
* Logging

# ToC

* [Installation](https://github.com/phprest/phprest#installation)
* [Usage](https://github.com/phprest/phprest#usage)
   * [Services](https://github.com/phprest/phprest#services)
   * [Setup](https://github.com/phprest/phprest#setup)
      * [Configuration](https://github.com/phprest/phprest#configuration)
      * [Logging](https://github.com/phprest/phprest#logging)
      * [Usage with Stack](https://github.com/phprest/phprest#usage-with-stack)
   * [API versioning](https://github.com/phprest/phprest#api-versioning)
   * [Routing](https://github.com/phprest/phprest#routing)
      * [Simple routing](https://github.com/phprest/phprest#simple-routing)
      * [Routing with arguments](https://github.com/phprest/phprest#routing-with-arguments)
      * [Routing through a controller](https://github.com/phprest/phprest#routing-through-a-controller)
      * [Routing through a service controller](https://github.com/phprest/phprest#routing-through-a-service-controller)
      * [Routing with annotations](https://github.com/phprest/phprest#routing-with-annotations)
   * [Controller](https://github.com/phprest/phprest#controller)
   * [Serialization, Deserialization, HATEOAS](https://github.com/phprest/phprest#serialization-deserialization-hateoas)
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
   * [Dependency Injection Container](https://github.com/phprest/phprest#dependency-injection-container)
   * [CLI](https://github.com/phprest/phprest#cli)
* [Error handler](https://github.com/phprest/phprest#error-handler)
   * [On a single exception](https://github.com/phprest/phprest#on-a-single-exception)
* [Authentication](https://github.com/phprest/phprest#authentication)
   * [Basic Authentication](https://github.com/phprest/phprest#basic-authentication)
   * [JWT Authentication](https://github.com/phprest/phprest#jwt-authentication)
* [API testing](https://github.com/phprest/phprest#api-testing)
* [API documentation](https://github.com/phprest/phprest#api-documentation)

# Installation

Install it through composer.

```json
{
    "require": {
        "phprest/phprest": "@stable"
    }
}
```

**tip:** you should browse the [`phprest/phprest`](https://packagist.org/packages/phprest/phprest)
page to choose a stable version to use, avoid the `@stable` meta constraint.

# Usage

## Services

There are a couple of services which can help you to solve some general problems:
* [Validator](https://github.com/phprest/phprest-service-validator)
* [Request Filter](https://github.com/phprest/phprest-service-request-filter)
* [Orm](https://github.com/phprest/phprest-service-orm)

*These are separate repositories.*

## Setup

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Phprest\Config;
use Phprest\Response;
use Phprest\Application;
use Symfony\Component\HttpFoundation\Request;

# vendor name, current API version, debug
$config = new Config('vendor.name', '0.1', true);

$app = new Application($config);

$app->get('/{version:\d\.\d}/', function (Request $request) {
    return new Response\Ok('Hello World!');
});

$app->run();
```

### Configuration

You should check the [Config](src/Config.php) class.

### Logging

```php
<?php
use Phprest\Service\Logger\Config as LoggerConfig;
use Phprest\Service\Logger\Service as LoggerService;
use Monolog\Handler\StreamHandler;

$config = new Config('vendor.name', '0.1');

$loggerHandlers[] = new StreamHandler('path_to_the_log_file', \Monolog\Logger::DEBUG);

$config->setLoggerConfig(new LoggerConfig('phprest', $loggerHandlers));
$config->setLoggerService(new LoggerService());
```

### Usage with Stack

You can register middlewares trough the ```registerMiddleware```function.

```php
$app->registerMiddleware('Jsor\Stack\JWT', [
    [
        'firewall' => [
	    ['path' => '/',         'anonymous' => false],
	    ['path' => '/tokens',   'anonymous' => true]
	],
	'key_provider' => function() {
	    return 'secret-key';
	},
	'realm' => 'The Glowing Territories'
    ]
]);
```

## API Versioning

Phprest works with API versions by default. This means that the [ApiVersion Middleware](src/Middleware/ApiVersion.php) manipulates the incoming request. The version (based on the current Accept header) is added to the path.

What does it mean?


|Accept header|Route|Result route*|
|-------------|-----|-------------|
|application/vnd.phprest-v1+json|/temperatures|/1.0/temperatures|
|application/vnd.phprest-v3.5+json|/temperatures|/3.5/temperatures|
|\*/\*|/temperatures|/*the version which you set in your [Config](src/Config.php#L70)*/temperatures|

\* *It is not a redirect or a forward method, it is just an inner application routing through a middleware.*

---
|Accept/Content-Type header can be|Transfers to|
|---------------------------------|------------|
|application/vnd.**Vendor**-v**Version**+json|itself|
|application/vnd.**Vendor**+json; version=**Version**|itself|
|application/vnd.**Vendor**-v**Version**+xml|itself|
|application/vnd.**Vendor**+xml; version=**Version**|itself|
|application/json|application/vnd.**Vendor**-v**Version**+json|
|application/xml|application/vnd.**Vendor**-v**Version**+xml|
|\*/\*|application/vnd.**Vendor**-v**Version**+json|
 
API **Version** only can be one of the following ranges:
* 0 - 9
* 0.0 - 9.9

---
* If Accept header is not parsable
 * then Phprest throws a Not Acceptable exception
 
* If you do a deserialization and Content-Type header is not parsable
 * then Phprest throws an Unsupported Media Type exception

## Routing

For more information please visit [League/Route](https://github.com/thephpleague/route).

### Simple routing

```php
<?php
$app->get('/{version:\d\.\d}/hello', function (Request $request, $version) {
	# You can leave the $request and the $version variable
    return new Response\Ok('Hello World!');
});
```

* The [ApiVersion Middleware](src/Middleware/ApiVersion.php) manipulates the inner routing every time, so you have to care about the first part of your route as a version number.
* This route is available in all API versions (see the ```\d\.\d``` regular expression)
* You can set a fix API version number too e.g. ```'/3.6/hello'```

### Routing with arguments

```php
<?php
$app->get('/2.4/hello/{name:word}', function (Request $request, $name) {
    return new Response\Ok('Hello ' . $name);
});
```

* This route is available only in API version 2.4

### Routing through a controller

```php
<?php
# index.php

# calls index method on HomeController class
$app->get('/{version:\d\.\d}/', '\Foo\Bar\HomeController::index');
```

```php
<?php namespace Foo\Bar;
# HomeController.php

use Symfony\Component\HttpFoundation\Request;
use Phprest\Response;

class HomeController
{
    public function index(Request $request, $version)
    {
        return new Response\Ok('Hello World!');
    }
}
```

### Routing through a service controller

```php
<?php
$app['HomeController'] = function () {
    return new \Foo\Bar\HomeController();
};

$app->get('/{version:\d\.\d}/', 'HomeController::index');
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
     * @Phprest\Route(method="GET", path="/foobars/{id}", since=1.2, until=2.8)
     */
    public function get(Request $request, $version, $id)
    {
        return new Response\Ok('Hello World!');
    }
}
```

* ```since``` tag is optional
* ```until``` tag is optional

## Controller

To create a Phprest Controller simply extends your class from ```\Phprest\Util\Controller```.

```php
<?php namespace App\Module\Controller;

class Index extends \Phprest\Util\Controller
{
   public function index(Request $request)
   {
      # ...
   }
}
```

## Serialization, Deserialization, Hateoas

* Phprest will automatically serialize* your response based on the Accept header.
* Phprest can deserialize your content based on the Content-Type header.

Except*:
* If your response is not a Response instance (e.g. it a simple string)
* If your response is empty

### Serialization example

Let's see a Temperature entity:

*You do not have to use annotations! You can use configuration files! Browse in* [Jms\Serializer](http://jmsyst.com/libs/serializer) *and* [Willdurand\Hateoas](https://github.com/willdurand/Hateoas)

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
$app->post('/{version:\d\.\d}/temperatures', function () use ($app, $version) {
    $temperature = new \Foo\Entity\Temperature(1, 32, new \DateTime());
    
    return new Response\Created('/temperatures/1', $temperature);
});
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

Properties will be translated from camel-case to a lower-cased underscored name, e.g. camelCase -> camel_case by default. If you want to use a custom serialized name you have to use the **@SerializedName** option on your attribute.

### Deserialization example

You have to use the [HATEOAS Util](src/Service/Hateoas/Util.php) trait in your controller to do deserialization.

```php
# ...
use JMS\Serializer\Exception\RuntimeException;
# ...
    public function post(Request $request)
    {
        try {
            /** @var \Foo\Entity\Temperature $temperature */
            $temperature = $this->deserialize('\Foo\Entity\Temperature', $request);
        } catch (RuntimeException $e) {
            throw new Exception\UnprocessableEntity(0, [new Service\Validator\Entity\Error('', $e->getMessage())]);
        }
    }
# ...
```

## Pagination

```php
<?php
# ...
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;
# ...
$paginatedCollection = new PaginatedRepresentation(
    new CollectionRepresentation([$user1, $user2, ...]),
    '/users', # route
    [],       # route parameters, should be $request->query->all()
    1,        # page, should be (int)$request->query->get('page')
    10,       # limit, should be (int)$request->query->get('limit')
    5,        # total pages
    'page',   # page route parameter name, optional, defaults to 'page'
    'limit',  # limit route parameter name, optional, defaults to 'limit'
    true,     # absolute URIs
    47        # total number of rows
);
# ...
return new Response\Ok($paginatedCollection);
```

For more informations please visit the [HATEOAS docs](https://github.com/willdurand/Hateoas#dealing-with-collections)

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

## Dependency Injection Container

See [Proton's doc](https://github.com/alexbilbie/Proton#dependency-injection-container) and for more information please visit [League/Container](https://github.com/thephpleague/container).

## CLI

You can use a helper script if you want after a composer install (```vendor/bin/phprest```).

You have to provide the (bootstrapped) app instance for the script. You have two options for this:
* Put your app instance to a specific file: ```app/app.php```
 * You have to return with the bootstrapped app instance in the proper file
* Put the path of the app instance in the ```paths.php``` file
 * You have to return with an array from the ```paths.php``` file with the app file path under the ```app``` array key

## Error handler

Phprest handles error with [League\BooBoo](https://github.com/thephpleague/booboo). The default formatter is [Json and Xml Formatter](src/ErrorHandler/Formatter/JsonXml.php).

### On a single exception

```php
<?php
# ...
$app->get('/{version:\d\.\d}/', function (Request $request, $version) {
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

# Authentication

### Basic Authentication

You'll need this package:
* [Dflydev\Dflydev-stack-basic-authentication](https://github.com/dflydev/dflydev-stack-basic-authentication)

```php
$app->registerMiddleware('Dflydev\Stack\BasicAuthentication', [
    [
        'firewall' => [
	    ['path' => '/', 'anonymous' => false],
            ['path' => '/temperatures', 'method' => 'GET', 'anonymous' => true]
	],
	'authenticator' => function ($username, $password) {
            if ('admin' === $username && 'admin' === $password) {
                # Basic YWRtaW46YWRtaW4=
                return 'success';
            }
        },
	'realm' => 'The Glowing Territories'
    ]
]);
```

### JWT Authentication

You'll need this package:
* [Jsor\Stack-jwt](https://github.com/jsor/stack-jwt)

```php
$app->registerMiddleware('Jsor\Stack\JWT', [
    [
        'firewall' => [
	    ['path' => '/',         'anonymous' => false],
	    ['path' => '/tokens',   'anonymous' => true]
	],
	'key_provider' => function() {
	    return 'secret-key';
	},
	'realm' => 'The Glowing Territories'
    ]
]);
```

# API testing

There are a couple of great tools out there for testing your API.

* [Postman](http://www.getpostman.com/) and [Newman](https://github.com/a85/Newman)
 * Tip: Create collections in Postman and then run these in Newman
* [Frisby](https://github.com/vlucas/frisby)
 * Frisby is a REST API testing framework built on node.js and Jasmine that makes testing API endpoints easy, fast, and fun.
* [Runscope](https://www.runscope.com/)
 * For Api Monitoring and Testing

# API documentation

Just a few recommendations:

* [API Blueprint](https://apiblueprint.org/)
   * API Blueprint is a documentation-oriented API description language. A couple of semantic assumptions over the plain Markdown. 
* [Swagger](http://swagger.io/)
   * With a Swagger-enabled API, you get interactive documentation, client SDK generation and discoverability.
