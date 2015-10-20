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

use Cake\Event\Event;
use Cake\Network\Request;
use Cake\TestSuite\TestCase;
use Wrench\Mode\Redirect;
use Wrench\Routing\Filter\MaintenanceModeFilter;

/**
 * Maintenance Mode filter test.
 */
class MaintenanceModeFilterTest extends TestCase
{

    /**
     * Test loading the filter without passing params.
     *
     * @return void
     */
    public function testMaintenanceModeFilterNoParams()
    {
        $filter = new MaintenanceModeFilter();
        $this->assertEquals('Wrench\Mode\Redirect', $filter->config('mode.className'));
        $this->assertEquals([], $filter->config('mode.config'));
        $this->assertInstanceOf('Wrench\Mode\Redirect', $filter->mode());
    }

    /**
     * Test loading the filter by passing a Mode instance in the `mode` key
     * of the filter config
     *
     * @return void
     */
    public function testMaintenanceModeFilterModeInstance()
    {
        $filter = new MaintenanceModeFilter([
            'mode' => new Redirect([
                'url' => 'http://example.com/maintenance/'
            ])
        ]);

        $this->assertInstanceOf('Wrench\Mode\Redirect', $filter->mode());

        $expected = [
            'code' => 307,
            'url' => 'http://example.com/maintenance/',
            'headers' => []
        ];
        $this->assertEquals($expected, $filter->mode()->config());
    }
}
