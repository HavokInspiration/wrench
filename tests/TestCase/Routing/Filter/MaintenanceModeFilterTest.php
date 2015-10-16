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
use Wrench\Routing\Filter\MaintenanceModeFilter;

/**
 * Maintenance Mode filter test.
 */
class MaintenanceModeFilterTest extends TestCase
{

    /**
     * Test loading the filter without passing params.
     *
     * @expectedException \Wrench\Mode\Exception\MissingModeException
     */
    public function testMaintenanceModeFilterException()
    {
        $filter = new MaintenanceModeFilter();
        $request = new Request();
        $filter->beforeDispatch(new Event('name', null, ['request' => $request]));
    }
}
