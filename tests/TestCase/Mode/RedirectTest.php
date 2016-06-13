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
        $res = $middleware($request, $response, $next);

        $this->assertEquals(307, $res->getStatusCode());
        $this->assertEquals('http://localhost/maintenance.html', $res->getHeaderLine('location'));
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

        $res = $middleware($request, $response, $next);
        $this->assertEquals(503, $res->getStatusCode());
        $this->assertEquals('http://www.example.com/maintenance.html', $res->getHeaderLine('location'));
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

        $res = $middleware($request, $response, $next);
        $this->assertEquals(503, $res->getStatusCode());
        $this->assertEquals('http://www.example.com/maintenance.html', $res->getHeaderLine('location'));
        $this->assertEquals('someValue', $res->getHeaderLine('someHeader'));
        $this->assertEquals('additionalValue', $res->getHeaderLine('additionalHeader'));
    }
}
