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
use Zend\Diactoros\Stream;

class CallbackTest extends TestCase
{

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        Configure::write('Wrench.enable', false);
    }

    /**
     * Test the Callback filter mode
     * @return void
     */
    public function testMaintenanceModeCallback()
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
                'className' => 'Wrench\Mode\Callback',
                'config' => [
                    'callback' => function ($request, $response) {
                        $string = 'Some content from a callback';

                        $stream = new Stream(fopen('php://memory', 'r+'));
                        $stream->write($string);
                        $response = $response->withBody($stream);
                        $response = $response->withStatus(503);
                        return $response;
                    }
                ]
            ]
        ]);
        $res = $middleware($request, $response, $next);

        $this->assertEquals('Some content from a callback', (string) $res->getBody());
        $this->assertEquals(503, $res->getStatusCode());
    }

    /**
     * Test the Callback filter mode
     * @return void
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMaintenanceModeCallbackException()
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
                'className' => 'Wrench\Mode\Callback',
                'config' => [
                    'callback' => 'wonkycallable'
                ]
            ]
        ]);

        $res = $middleware($request, $response, $next);
    }
}
