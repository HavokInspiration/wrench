# Wrench : CakePHP 3 Maintenance mode plugin

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://travis-ci.org/HavokInspiration/wrench.svg?branch=master)](https://travis-ci.org/HavokInspiration/wrench)
[![codecov.io](https://codecov.io/github/HavokInspiration/wrench/coverage.svg?branch=master)](https://codecov.io/github/HavokInspiration/wrench?branch=master)

Wrench is a CakePHP 3.X plugin that aims to provide an easy way to implement a **Maintenance Mode**
for your CakePHP website / applications.

## Requirements

- PHP >= 5.5.9
- CakePHP >= 3.3.0

## About the plugin versions

| CakePHP < 3.3.0 | CakePHP >= 3.3.0 | CakePHP >= 3.6.0 |
| --------------- | ---------------- | ---------------- |
| Wrench 1.X | Wrench 2.X | Wrench 3.X |
| PHP >= 5.4.16 | PHP >= 5.5.9 | PHP >= 5.6.0 |
| Uses CakePHP DispatcherFilter mecanism | Uses CakePHP Middleware Stack and PSR-7 Request / Response implementation | Uses CakePHP Middleware Stack and PSR-7 Request / Response implementation + no deprecation warning from CakePHP 3.6.X |

## Recommanded package

If you want to create your own maintenance mode, you can use the [CakePHP 3 Bake plugin](https://github.com/cakephp/bake)

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require havokinspiration/wrench
```

## Loading the plugin

You can load the plugin using the shell command:

```
bin/cake plugin load Wrench
```

Or you can manually add the loading statement in the **boostrap.php** file of your application:

```php
Plugin::load('Wrench');
```

## Usage

The plugin is built around a **Middleware** that will intercept the current request to
return a customized response to warn the user that the website / app is undergoing maintenance.

To use the Maintenance mode, you need to add the **MaintenanceMiddleware** to the
**MiddlewareStack** in your Application file by adding the following elements :

```php
use Wrench\Middleware\MaintenanceMiddleware;

// ...

public function middleware($middleware)
{
    $middleware->add(new MaintenanceMiddleware());
    
    // Other middleware configuration
    
    return $middleware;
}
```

Since this Middleware is here to prevent the application from responding, it should be the first to be treated by the Dispatcher and should,
as such, be configured as the first one, either by adding it in the beginning of the method with the ``push()`` method or using the 
``prepend()`` method anywhere you want in your middlewares configuration.

By default, only adding it with the previous line will make use of the **Redirect** mode. More informations on maintenance Modes below.

The Middleware is only active when the Configure key ``Wrench.enable`` is equal to ``true``.
To enable the maintenance mode, use the following statement in your **bootstrap.php** file :

```php
Configure::write('Wrench.enable', true);
```

### Modes

The plugin is built around the concept of "modes".
Modes are special classes which will have the task of processing the request and return the proper response
in order to warn the user that the website / application is undergoing maintenance.

The plugin comes packaged with four maintenance modes : ``Redirect``, ``Output``, ``Callback`` and ``View``.

You can configure it to use specific modes when adding the Middleware to the Middleware stack by passing parameters to the Middleware constructor.
The will result in a call looking like this :

```php
$middleware->add(new MaintenanceMiddleware([
    'mode' => [
        'className' => 'Full\Namespace\To\Mode',
        'config' => [
            // Specific configuration parameters for the Mode
        ]
    ]
]);
```

If you need it, you can directly pass an instance of a ``Mode`` to the ``mode`` array key of the filter's config:

```php
$middleware->add(new MaintenanceMiddleware([
    'mode' => new \Wrench\Mode\Redirect([
        'url' => 'http://example.com/maintenance'
    ])
]);
```

#### IP Whitelisting

While you put your application under maintenance, you might want, as the project administrator or developer, to be able
to access the application. You can do so using the IP whitelisting feature.
When configuring the `MaintenanceMiddleware`, just pass an array of allowed IP addresses to the `whitelist` key in the 
Middleware configuration array. All those IP will be allowed to access the application, even if the maintenance mode is
on:

```php
$middleware->add(new MaintenanceMiddleware([
    'whitelist' => ['1.2.3.4', '5.6.7.8'],
]));
```

In the above example, clients connecting with the IP address `1.2.3.4` or `5.6.7.8` will be able to access the project,
even if the maintenance mode is on.

#### Redirect Mode

The Redirect Mode is the default one. It will perform a redirect to a specific URL.
The Redirect Mode accepts the following parameters :

- **url** : The URL where the redirect should point to. Default to the app base path pointing to a **maintenance.html**
page.
- **code** : The HTTP status code of the redirect response. The code should be in the 3XX range, otherwise, it might
 get overwritten. Default to 307.
- **headers** : Array of additional headers to pass along the redirect response. Default to empty.

You can customize all those parameters :

```php
$middleware->add(new MaintenanceMiddleware([
    'mode' => [
        'className' => 'Wrench\Mode\Redirect',
        'config' => [
            'url' => 'http://example.com/maintenance',
            'code' => 303,
            'headers' => ['someHeader' => 'someValue']
        ]
    ]
]);
```
#### Output Mode

The Output Mode allows you to display the content of a static file as a response for the maintenance status.
It accepts multiple parameters :
- **path** : the **absolute** path to the file that will be served. Default to {ROOT}/maintenance.html.
- **code** : The HTTP status code of the redirect response. Default to 503.
- **headers** : Array of additional headers to pass along the redirect response. Default to empty.

You can customize all those parameters :

```php
$middleware->add(new MaintenanceMiddleware([
    'mode' => [
        'className' => 'Wrench\Mode\Output',
        'config' => [
            'path' => '/path/to/my/file',
            'code' => 404,
            'headers' => ['someHeader' => 'someValue']
        ]
    ]
]);
```

#### Callback Mode

The Callback Mode gives you the ability to use a custom callable.
It accepts only one parameter ``callback`` which should be a callable.
The callable will take two arguments :

- **request** : A ``\Psr\Http\Message\ServerRequestInterface`` instance
- **response** : A ``\Psr\Http\Message\ResponseInterface`` instance

The callable is expected to return a ``\Psr\Http\Message\ResponseInterface`` if the request is to be
stopped.

```php
$middleware->add(new MaintenanceMiddleware([
    'mode' => [
        'className' => 'Wrench\Mode\Callback',
        'config' => [
            'callback' => function($request, $response) {
                $string = 'Some content from a callback';

                $stream = new Stream(fopen('php://memory', 'r+'));
                $stream->write($string);
                $response = $response->withBody($stream);
                $response = $response->withStatus(503);
                $response = $response->withHeader('someHeader', 'someValue');
                return $response;
            }
        ]
    ]
]);
```

#### View Mode

The View Mode gives you the ability to use a View to render the maintenance page.
This gives you the ability to leverage helpers and the layout / template system of the framework.
It accepts multiple parameters :
- **code** : The HTTP status code of the redirect response. Default to 503.
- **headers** : Array of additional headers to pass along the redirect response. Default to empty.
- **view** : Array of parameters to pass to the View class constructor. Only the following options are supported :
    - **className** : Fully qualified class name of the View class to use. Default to AppView
    - **templatePath** : Path to the template you wish to display (relative to your ``src/Template`` directory). You can use plugin dot notation.
    - **template** : Template name to use. Default to "maintenance".
    - **plugin** : Theme where to find the layout and template
    - **theme** : Same thing than plugin
    - **layout** : Layout name to use. Default to "default"
    - **layoutPath** : Path to the layout you wish to display (relative to your ``src/Template`` directory). You can use plugin dot notation. Default to "Layout"

```php
// Will load a template ``src/Template/Maintenance/maintenance.ctp``
// in a layout ``src/Template/Layout/Maintenance/maintenance.ctp``
$middleware->add(new MaintenanceMiddleware([
    'mode' => [
        'className' => 'Wrench\Mode\View',
        'config' => [
            'view' => [
                 'templatePath' => 'Maintenance',
                 'layout' => 'maintenance',
                 'layoutPath' => 'Maintenance'
            ]
        ]
    ]
]);
```

### Creating a custom mode

If you have special needs, you can create your own maintenance mode.
To get started quickly, you can use the ``bake`` console tool to generate a skeleton:

```
bin/cake bake maintenance_mode MyCustomMode
```

This will generate a ``MyCustomMode`` class file under the ``App\Maintenance\Mode`` namespace (as well as a test file).
Your skeleton will only contain one method ``process()`` returning a ``\Psr\Http\Message\ResponseInterface`` object. This is where
the logic of your maintenance mode goes. You can either make the method return a ``ResponseInterface`` object which will shortcut
the request cycle and use the returned ``ResponseInterface`` object to respond to the request. Any other returned value will make
the maintenance mode no-op and the request cycle will go on. This is useful if you need to display the maintenance status
only on specific conditions.

The Mode implements the ``InstanceConfigTrait`` which allows you to easily define default configuration parameters and
gives you easy access to them.

Keep in mind that the ``ResponseInterface`` you need to return is PSR-7 compliant. You can get more details about the implementation
and how to interact with it on [the PHP-FIG website](http://www.php-fig.org/psr/psr-7/)
as well as [on the CakePHP documentation](https://github.com/cakephp/docs/blob/3.3/en/controllers/middleware.rst#psr7-requests-and-responses)

You can check out the implemented modes to have some examples.

### Conditionally applying the maintenance mode

Conditionally applying a middleware is currently not possible with the current implementation of the Middleware stack in CakePHP 3.3.
A documentation on how to do this will be added when and if this feature is implemented in the core.

## Contributing

If you find a bug or would like to ask for a feature, please use the [GitHub issue tracker](https://github.com/HavokInspiration/wrench/issues).
If you would like to submit a fix or a feature, please fork the repository and [submit a pull request](https://github.com/HavokInspiration/wrench/pulls).

### Coding standards

Since this plugin is tangled with features from the CakePHP Core and to provide consistency, it follows the [CakePHP coding standards](http://book.cakephp.org/3.0/en/contributing/cakephp-coding-conventions.html).
When submitting a pull request, make sure your code follows these standards.
You can check it by installing the code sniffer :

```
composer require cakephp/cakephp-codesniffer:dev-master
```

And then running the sniff :

```
./vendor/bin/phpcs -p --extensions=php --standard=vendor/cakephp/cakephp-codesniffer/CakePHP ./src ./tests
```

## License

Copyright (c) 2015 - 2017, Yves Piquel and licensed under [The MIT License](http://opensource.org/licenses/mit-license.php).
Please refer to the LICENSE.txt file.
