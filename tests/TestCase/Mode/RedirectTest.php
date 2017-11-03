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

class RedirectTest extends TestCase
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
     * Test the Redirect filter mode without params
     * @return void
     */
    public function testRedirectModeNoParams()
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
        $middleware = new MaintenanceMiddleware();
        $middlewareResponse = $middleware($request, $response, $next);

        $this->assertEquals(307, $middlewareResponse->getStatusCode());
        $this->assertEquals('http://localhost/maintenance.html', $middlewareResponse->getHeaderLine('location'));
    }

    /**
     * Test the Redirect filter mode with params
     * @return void
     */
    public function testRedirectModeCustomParams()
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
                'className' => 'Wrench\Mode\Redirect',
                'config' => [
                    'code' => 503,
                    'url' => 'http://www.example.com/maintenance.html'
                ]
            ]
        ]);

        $middlewareResponse = $middleware($request, $response, $next);
        $this->assertEquals(503, $middlewareResponse->getStatusCode());
        $this->assertEquals('http://www.example.com/maintenance.html', $middlewareResponse->getHeaderLine('location'));
    }

    /**
     * Test the Redirect filter mode with additional headers
     * @return void
     */
    public function testMaintenanceModeFilterRedirectHeaders()
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
                'className' => 'Wrench\Mode\Redirect',
                'config' => [
                    'code' => 503,
                    'url' => 'http://www.example.com/maintenance.html',
                    'headers' => ['someHeader' => 'someValue', 'additionalHeader' => 'additionalValue']
                ]
            ]
        ]);

        $middlewareResponse = $middleware($request, $response, $next);
        $this->assertEquals(503, $middlewareResponse->getStatusCode());
        $this->assertEquals('http://www.example.com/maintenance.html', $middlewareResponse->getHeaderLine('location'));
        $this->assertEquals('someValue', $middlewareResponse->getHeaderLine('someHeader'));
        $this->assertEquals('additionalValue', $middlewareResponse->getHeaderLine('additionalHeader'));
    }

    /**
     * Test the Redirect filter mode without params when using the "whitelist" option. Meaning the maintenance mode
     * should not be shown if the client IP is whitelisted.
     *
     * @return void
     */
    public function testRedirectModeWhitelist()
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
        ]);
        $middlewareResponse = $middleware($request, $response, $next);

        $this->assertEquals(200, $middlewareResponse->getStatusCode());
    }
}
