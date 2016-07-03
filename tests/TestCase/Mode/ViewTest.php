<?php
/**
 * Copyright (c) Yves Piquel (http://www.havokinspiration.fr)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Yves Piquel (http://www.havokinspiration.fr)
 * @link          http://github.com/HavokInspiration/wrench
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wrench\Test\TestCase\Mode;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;
use Wrench\Middleware\MaintenanceMiddleware;
use Zend\Diactoros\Response;

class ViewTest extends TestCase
{

    /**
     * @inheritDoc
     */
    public function setup()
    {
        Plugin::load(['TestPlugin']);
    }

    /**
     * @inheritDoc
     */
    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload('TestPlugin');
        Configure::write('Wrench.enable', false);
    }

    /**
     * Test the View filter mode without params
     * @return void
     */
    public function testViewModeNoParams()
    {
        Configure::write('Wrench.enable', true);
        $request = ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'localhost',
            'REQUEST_URI' => '/'
        ]);
        $response = new Response();
        $next = function ($req, $res) {
            return $res;
        };
        $middleware = new MaintenanceMiddleware([
            'mode' => [
                'className' => 'Wrench\Mode\View'
            ]
        ]);
        $res = $middleware($request, $response, $next);

        $expected = "Layout Header\nThis is an element<div>test</div>This app is undergoing maintenanceLayout Footer";
        $this->assertEquals($expected, (string) $res->getBody());
    }

    /**
     * Test the View with custom params
     * @return void
     */
    public function testViewModeCustomParams()
    {
        Configure::write('Wrench.enable', true);

        $request = ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'localhost',
            'REQUEST_URI' => '/'
        ]);
        $response = new Response();
        $next = function ($req, $res) {
            return $res;
        };
        $middleware = new MaintenanceMiddleware([
            'mode' => [
                'className' => 'Wrench\Mode\View',
                'config' => [
                    'code' => 404,
                    'view' => [
                        'templatePath' => 'Maintenance',
                        'layout' => 'maintenance',
                        'layoutPath' => 'Maintenance'
                    ]
                ]
            ]
        ]);
        $res = $middleware($request, $response, $next);

        $expected = "Maintenance Header\nI'm in a sub-directoryMaintenance Footer";
        $this->assertEquals($expected, (string) $res->getBody());
    }

    /**
     * Test the View with custom params and plugins
     * @return void
     */
    public function testViewModeCustomParamsPlugin()
    {
        Configure::write('Wrench.enable', true);

        $request = ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'localhost',
            'REQUEST_URI' => '/'
        ]);
        $response = new Response();
        $next = function ($req, $res) {
            return $res;
        };
        $middleware = new MaintenanceMiddleware([
            'mode' => [
                'className' => 'Wrench\Mode\View',
                'config' => [
                    'code' => 404,
                    'view' => [
                        'template' => 'maintenance',
                        'templatePath' => 'Maintenance',
                        'layout' => 'maintenance',
                        'plugin' => 'TestPlugin',
                        'layoutPath' => 'Maintenance',
                    ]
                ]
            ]
        ]);
        $res = $middleware($request, $response, $next);

        $expected = "Plugin Maintenance Header\nI'm in a plugin sub-directoryPlugin Maintenance Footer";
        $this->assertEquals($expected, (string) $res->getBody());

        $middleware = new MaintenanceMiddleware([
            'mode' => [
                'className' => 'Wrench\Mode\View',
                'config' => [
                    'code' => 404,
                    'view' => [
                        'template' => 'maintenance',
                        'templatePath' => 'Maintenance',
                        'layout' => 'maintenance',
                        'theme' => 'TestPlugin',
                        'layoutPath' => 'Maintenance',
                    ]
                ]
            ]
        ]);
        $res = $middleware($request, $response, $next);

        $expected = "Plugin Maintenance Header\nI'm in a plugin sub-directoryPlugin Maintenance Footer";
        $this->assertEquals($expected, (string) $res->getBody());

        $middleware = new MaintenanceMiddleware([
            'mode' => [
                'className' => 'Wrench\Mode\View',
                'config' => [
                    'code' => 404,
                    'view' => [
                        'template' => 'TestPlugin.maintenance',
                        'templatePath' => 'Maintenance',
                        'layout' => 'TestPlugin.maintenance',
                        'layoutPath' => 'Maintenance',
                    ]
                ]
            ]
        ]);
        $res = $middleware($request, $response, $next);

        $expected = "Plugin Maintenance Header\nI'm in a plugin sub-directoryPlugin Maintenance Footer";
        $this->assertEquals($expected, (string) $res->getBody());
    }
}
