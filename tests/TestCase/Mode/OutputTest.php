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
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;
use Wrench\Middleware\MaintenanceMiddleware;
use Zend\Diactoros\Response;

class OutputTest extends TestCase
{

    /**
     * @inheritDoc
     */
    public function tearDown()
    {
        parent::tearDown();
        Configure::write('Wrench.enable', false);
    }

    /**
     * Test the Output filter mode without params
     * @return void
     */
    public function testOutputModeNoParams()
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
                'className' => 'Wrench\Mode\Output'
            ]
        ]);
        $res = $middleware($request, $response, $next);

        $this->assertEquals(503, $res->getStatusCode());

        $content = file_get_contents(ROOT . DS . 'maintenance.html');
        $this->assertEquals($res->getBody(), $content);
    }

    /**
     * Test the Output filter mode with additional headers
     * @return void
     */
    public function testMaintenanceModeFilterOutputHeaders()
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
                'className' => 'Wrench\Mode\Output',
                'config' => [
                    'code' => 404,
                    'headers' => ['someHeader' => 'someValue']
                ]
            ]
        ]);
        $middlewareResponse = $middleware($request, $response, $next);

        $this->assertEquals(404, $middlewareResponse->getStatusCode());

        $content = file_get_contents(ROOT . DS . 'maintenance.html');
        $this->assertEquals($middlewareResponse->getBody(), $content);

        $this->assertEquals('someValue', $middlewareResponse->getHeaderLine('someHeader'));
    }

    /**
     * Test the Output filter mode with a wrong file path : it should throw an
     * exception
     * @return void
     *
     * @expectedException \LogicException
     */
    public function testOutputModeCustomParams()
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
                'className' => 'Wrench\Mode\Output',
                'config' => [
                    'path' => ROOT . DS . 'wonky.html'
                ]
            ]
        ]);
        $middleware($request, $response, $next);
    }

    /**
     * Test the Output filter mode without params when using the "whitelist" option. Meaning the maintenance mode should
     * not be shown if the client IP is whitelisted.
     *
     * @return void
     */
    public function testOutputModeWhitelist()
    {
        Configure::write('Wrench.enable', true);
        $request = ServerRequestFactory::fromGlobals([
            'HTTP_HOST' => 'localhost',
            'REQUEST_URI' => '/',
            'REMOTE_ADDR' => '127.0.0.1'
        ]);
        $response = new Response();
        $next = function ($req, $res) {
            return $res;
        };
        $middleware = new MaintenanceMiddleware([
            'whitelist' => ['127.0.0.1'],
            'mode' => [
                'className' => 'Wrench\Mode\Output'
            ]
        ]);
        $res = $middleware($request, $response, $next);

        $this->assertEquals(200, $res->getStatusCode());

        $this->assertEquals($res->getBody(), '');
    }
}
