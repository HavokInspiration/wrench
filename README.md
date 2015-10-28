# Wrench : CakePHP 3 Maintenance mode plugin

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://img.shields.io/travis/HavokInspiration/wrench/master.svg?style=flat-square)](https://travis-ci.org/havokinspiration/wrench)

**This plugin is still under development and should be considered alpha software.**

Wrench is a CakePHP 3.X plugin that aims to provide an easy way to implement a **Maintenance Mode**
for your CakePHP website / applications.

## Requirements

- PHP 5.4.16
- CakePHP 3

## Recommanded packages

- If you want to create your own maintenance mode, you can use the [CakePHP 3 Bake plugin](https://github.com/cakephp/bake)

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

The plugin is built around a **DispatcherFilter** that will intercept the current request to
return a customized response to warn the user that the website / app is undergoing maintenance.

To use the Maintenance mode, you need to add the **MaintenanceModeFilter** to the
**DispatcherFactory** in your bootstrap file using the following statement:

```php
DispatcherFactory::add('Wrench.MaintenanceMode');
```

By default, only adding it with the previous line will make use of the **Redirect** mode. More informations on maintenance Modes below.

The Filter is only active when the Configure key ``Wrench.enable`` is equal to ``true``.
To enable the maintenance mode, use the following statement in your **bootstrap.php** file :

```php
Configure::write('Wrench.enable', true);
```

### Modes

The plugin is built around the concept of "modes".
Modes are special classes which will have the task of processing the request and return the proper response
in order to warn the user that the website / application is undergoing maintenance.

The plugin comes packaged with three maintenance modes : ``Redirect``, ``Output`` and ``Callback``.

You can configure it to use specific modes when adding the Filter to the DispatcherFactory using the ``options`` parameter of the ``DispatcherFactory::add()`` method.
The array of parameters is required to be of the following form:

```php
[
    'mode' => [
        'className' => 'Full\Namespace\To\Mode',
        'config' => [
            // Specific configuration parameters for the Mode
        ]
    ]
]
```

If you need it, you can directly pass an instance of a ``Mode`` to the ``mode`` array key of the filter's config:

```php
DispatcherFactory::add('Wrench.MaintenanceMode', [
    'mode' => new \Wrench\Mode\Redirect([
        'url' => 'http://example.com/maintenance'
    ])
]);
```

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
DispatcherFactory::add('Wrench.MaintenanceMode', [
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
DispatcherFactory::add('Wrench.MaintenanceMode', [
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

- **request** : A ``\Cake\Network\Request`` instance
- **response** : A ``\Cake\Network\Response`` instance

The callable is expected to return a ``\Cake\Network\Response`` if the request is to be
stopped.

```php
DispatcherFactory::add('Wrench.MaintenanceMode', [
    'mode' => [
        'className' => 'Wrench\Mode\Callback',
        'config' => [
            'callback' => function($request, $response) {
                $response->body('This is from a callback');
                $response->statusCode(503);
                return $response;
            }
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
Your skeleton will only contain one method ``process()`` returning a ``\Cake\Network\Response`` object. This is where
the logic of your maintenance mode goes. You can either make the method return a ``Response`` object which will shortcut
the request cycle and use the returned ``Response`` object to respond to the request. Any other returned value will make
the maintenance mode no-op and the request cycle will go on. This is useful if you need to display the maintenance status
only on specific conditions.

The Mode implements the ``InstanceConfigTrait`` which allows you to easily define default configuration parameters and 
gives you easy access to them.

You can check out the implemented mode to have some examples.

### Conditionally applying the maintenance mode

If you need to apply the maintenance mode only for a specific part of your application or in specific conditions, you can use the ``when`` and ``for`` ``DispatcherFilter`` options.
The ``for`` option lets you match a URL substring and the ``when`` option allows you to register a callable : if the callable returns ``true``, the filter will be applied.
For instance, if you want to only show the maintenance mode for the blog part of your application, you can register it like this:

DispatcherFactory::add('Wrench.MaintenanceMode', [
    'mode' => [
        'className' => 'Wrench\Mode\Output',
        'config' => [
            'path' => '/path/to/my/file',
            'code' => 404,
            'headers' => ['someHeader' => 'someValue']
        ]
    ],
    'for' => '/blog'
]);

If you want to filter specific IP addresses to apply the maintenance mode to anyone but you, you can use the ``when`` option:

DispatcherFactory::add('Wrench.MaintenanceMode', [
    'mode' => [
        'className' => 'Wrench\Mode\Output',
        'config' => [
            'path' => '/path/to/my/file',
            'code' => 404,
            'headers' => ['someHeader' => 'someValue']
        ]
    ],
    'when' => function ($request, $response) {
        $myIp = '1.23.456.789';
        $ip = $request->clientIp();
        return $ip !== $myIp;
    }
]);

You can of course use both ``for`` and ``when`` options at the same time.
More details and examples about the ``for`` and ``when`` options in the [CakePHP Cookbook](http://book.cakephp.org/3.0/en/development/dispatch-filters.html#conditionally-applying-filters).

## To do

- [x] Add a direct output mode
- [ ] Add a "View" layer mode
- [x] Document how to build a custom mode
- [x] Implement, test and write about passing a Mode instance
- [x] Test and write about the ``when`` and ``for`` options

## License

Copyright (c) 2015, Yves Piquel and licensed under [The MIT License](http://opensource.org/licenses/mit-license.php).
Please refer to the LICENSE.txt file.
