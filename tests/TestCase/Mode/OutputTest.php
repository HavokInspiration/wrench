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

        $filter = new MaintenanceModeFilter([
            'mode' => [
                'className' => 'Wrench\Mode\Output'
            ]
        ]);

        $request = new Request();
        $response = $this->getMock('Cake\Network\Response', ['statusCode', 'body']);
        $response->expects($this->once())
            ->method('statusCode')
            ->with(503);
        $content = file_get_contents(ROOT . DS . 'maintenance.html');
        $response->expects($this->once())
            ->method('body')
            ->with($content);

        $filter->beforeDispatch(new Event('name', null, ['request' => $request, 'response' => $response]));
    }

    /**
     * Test the Output filter mode with additional headers
     * @return void
     */
    public function testMaintenanceModeFilterOutputHeaders()
    {
        Configure::write('Wrench.enable', true);

        $filter = new MaintenanceModeFilter([
            'mode' => [
                'className' => 'Wrench\Mode\Output',
                'config' => [
                    'code' => 404,
                    'headers' => ['someHeader' => 'someValue']
                ]
            ]
        ]);

        $request = new Request();
        $response = $this->getMock('Cake\Network\Response', ['statusCode', 'body', 'header']);
        $response->expects($this->once())
            ->method('statusCode')
            ->with(404);
        $content = file_get_contents(ROOT . DS . 'maintenance.html');
        $response->expects($this->once())
            ->method('body')
            ->with($content);
        $response->expects($this->once())
            ->method('header')
            ->with(['someHeader' => 'someValue']);

        $filter->beforeDispatch(new Event('name', null, ['request' => $request, 'response' => $response]));
    }

    /**
     * Test the Output filter mode with a wrong file path
     * @return void
     *
     * @expectedException \LogicException
     */
    public function testOutputModeCustomParams()
    {
        Configure::write('Wrench.enable', true);

        $filter = new MaintenanceModeFilter([
            'mode' => [
                'className' => 'Wrench\Mode\Output',
                'config' => [
                    'path' => ROOT . DS . 'wonky.html'
                ]
            ]
        ]);

        $request = new Request();
        $response = $this->getMock('Cake\Network\Response');

        $filter->beforeDispatch(new Event('name', null, ['request' => $request, 'response' => $response]));
    }
}
