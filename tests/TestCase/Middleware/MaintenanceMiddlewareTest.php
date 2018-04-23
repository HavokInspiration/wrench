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
namespace Wrench\Test\TestCase\Routing\Filter;

use Cake\TestSuite\TestCase;
use Wrench\Middleware\MaintenanceMiddleware;
use Wrench\Mode\Redirect;

/**
 * Maintenance Mode filter test.
 */
class MaintenanceMiddlewareTest extends TestCase
{

    /**
     * Test loading the filter without passing params.
     *
     * @return void
     */
    public function testMaintenanceModeFilterNoParams()
    {
        $middleware = new MaintenanceMiddleware();
        $this->assertEquals('Wrench\Mode\Redirect', $middleware->getConfig('mode.className'));
        $this->assertEquals([], $middleware->getConfig('mode.config'));
        $this->assertInstanceOf('Wrench\Mode\Redirect', $middleware->mode());
    }

    /**
     * Test loading the filter by passing a Mode instance in the `mode` key
     * of the filter config
     *
     * @return void
     */
    public function testMaintenanceModeFilterModeInstance()
    {
        $middleware = new MaintenanceMiddleware([
            'mode' => new Redirect([
                'url' => 'http://example.com/maintenance/'
            ])
        ]);

        $this->assertInstanceOf('Wrench\Mode\Redirect', $middleware->mode());

        $expected = [
            'code' => 307,
            'url' => 'http://example.com/maintenance/',
            'headers' => []
        ];
        $this->assertEquals($expected, $middleware->mode()->getConfig());
    }
}
