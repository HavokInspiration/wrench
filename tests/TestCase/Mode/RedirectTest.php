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
use Cake\TestSuite\TestCase;
use Wrench\Routing\Filter\MaintenanceModeFilter;

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
     * Test the Redirect filter mode
     */
    public function testMaintenanceModeFilterRedirectNoParam()
    {
        Configure::write('Wrench.enable', true);

        $filter = new MaintenanceModeFilter();

        $request = new Request();
        $request->base = 'http://localhost';
        $response = $this->getMock('Cake\Network\Response', ['statusCode', 'location']);
        $response->expects($this->once())
            ->method('statusCode')
            ->with(307);
        $response->expects($this->once())
            ->method('location')
            ->with('http://localhost/maintenance.html');

        $filter->beforeDispatch(new Event('name', null, ['request' => $request, 'response' => $response]));
    }

    /**
     * Test the Redirect filter mode
     */
    public function testMaintenanceModeFilterRedirect()
    {
        Configure::write('Wrench.enable', true);

        $filter = new MaintenanceModeFilter([
            'mode' => [
                'className' => 'Wrench\Mode\Redirect',
                'config' => [
                    'code' => 503,
                    'url' => 'http://www.example.com/maintenance.html'
                ]
            ]
        ]);

        $request = new Request();
        $response = $this->getMock('Cake\Network\Response', ['statusCode', 'location']);
        $response->expects($this->once())
            ->method('statusCode')
            ->with(503);
        $response->expects($this->once())
            ->method('location')
            ->with('http://www.example.com/maintenance.html');

        $filter->beforeDispatch(new Event('name', null, ['request' => $request, 'response' => $response]));
    }
}
