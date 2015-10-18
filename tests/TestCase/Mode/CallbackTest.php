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
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;
use Wrench\Routing\Filter\MaintenanceModeFilter;

class CallbackTest extends TestCase
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
     * Test the Callback filter mode
     * @return void
     */
    public function testMaintenanceModeCallback()
    {
        Configure::write('Wrench.enable', true);

        $filter = new MaintenanceModeFilter([
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

        $request = new Request();
        $response = new Response();

        $response = $filter->beforeDispatch(new Event('name', null, ['request' => $request, 'response' => $response]));
        $this->assertEquals('This is from a callback', $response->body());
        $this->assertEquals(503, $response->statusCode());
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

        $filter = new MaintenanceModeFilter([
            'mode' => [
                'className' => 'Wrench\Mode\Callback',
                'config' => [
                    'callback' => 'wonkycallable'
                ]
            ]
        ]);

        $request = new Request();
        $response = new Response();

        $filter->beforeDispatch(new Event('name', null, ['request' => $request, 'response' => $response]));
    }
}
