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
namespace Wrench\Test\TestCase\Shell\Task;

use Bake\Shell\Task\BakeTemplateTask;
use Cake\Core\Plugin;
use Cake\TestSuite\StringCompareTrait;
use Cake\TestSuite\TestCase;

class MaintenanceModeTaskTest extends TestCase
{

    use StringCompareTrait;

    public $Task;

    /**
     * setup method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->_compareBasePath = Plugin::path('Wrench') . 'tests' . DS . 'comparisons' . DS . 'Maintenance' . DS . 'Mode' . DS;

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')
            ->disableOriginalConstructor()
            ->getMock();

        $this->Task = $this->getMockBuilder('Wrench\Shell\Task\MaintenanceModeTask')
            ->setMethods(['in', 'err', 'createFile', '_stop'])
            ->setConstructorArgs([$io])
            ->getMock();

        $this->Task->Test = $this->getMockBuilder('Bake\Shell\Task\TestTask')
            ->setMethods(['in', 'err', 'createFile', '_stop'])
            ->setConstructorArgs([$io])
            ->getMock();

        $this->Task->BakeTemplate = new BakeTemplateTask($io);
        $this->Task->BakeTemplate->initialize();
        $this->Task->BakeTemplate->interactive = false;
        $this->Task->Test->BakeTemplate = new BakeTemplateTask($io);
        $this->Task->Test->BakeTemplate->initialize();
        $this->Task->Test->BakeTemplate->interactive = false;
    }

    /**
     * Load a plugin from the tests folder, and add to the autoloader
     *
     * @param string $name plugin name to load
     * @return void
     */
    protected function _loadTestPlugin($name)
    {
        $root = dirname(dirname(__FILE__)) . DS;
        $path = $root . 'test_app' . DS . 'Plugin' . DS . $name . DS;

        Plugin::load($name, [
            'path' => $path,
            'autoload' => true
        ]);
    }

/**
 * Test the excute method.
 *
 * @return void
 */
//    public function testMain()
//    {
//        $this->Task->Test->expects($this->once())
//            ->method('createFile')
//            ->with(
//                $this->_normalizePath(ROOT . DS . 'tests/TestCase/Maintenance/Mode/ExampleTest.php'),
//                $this->logicalAnd(
//                    $this->stringContains('namespace App\Test\TestCase\Maintenance\Mode'),
//                    $this->stringContains('class ExampleTest extends TestCase')
//                )
//            );
//
//        $this->Task->expects($this->at(0))
//            ->method('createFile')
//            ->with(
//                $this->_normalizePath(APP . 'Maintenance/Mode/Example.php'),
//                $this->stringContains('class Example extends Mode')
//            );
//
//        $this->Task->main('Example');
//    }

/**
 * Test main within a plugin.
 *
 * @return void
 */
//    public function testMainPlugin()
//    {
//        $this->_loadTestPlugin('MaintenanceTest');
//        $path = Plugin::path('MaintenanceTest');
//
//        $this->Task->Test->expects($this->once())
//            ->method('createFile')
//            ->with(
//                $this->_normalizePath($path . 'tests/TestCase/Maintenance/Mode/ExampleTest.php'),
//                $this->logicalAnd(
//                    $this->stringContains('namespace MaintenanceTest\Test\TestCase\Maintenance\Mode'),
//                    $this->stringContains('class ExampleTest extends TestCase')
//                )
//            );
//
//        $this->Task->expects($this->at(0))
//            ->method('createFile')
//            ->with(
//                $this->_normalizePath($path . 'src/Maintenance/Mode/Example.php'),
//                $this->logicalAnd(
//                    $this->stringContains('namespace MaintenanceTest\Maintenance\Mode'),
//                    $this->stringContains('class Example extends Mode')
//                )
//            );
//
//        $this->Task->main('MaintenanceTest.Example');
//    }

    /**
     * Test bake.
     *
     * @return void
     */
    public function testBake()
    {
        $this->Task->expects($this->at(0))
            ->method('createFile')
            ->with(
                $this->_normalizePath(APP . 'Maintenance/Mode/Example.php'),
                $this->logicalAnd(
                    $this->stringContains('namespace App\Maintenance\Mode'),
                    $this->stringContains('class Example extends Mode')
                )
            );

        $result = $this->Task->bake('Example');
        $this->assertSameAsFile('Example.php', $result);
    }

    /**
     * Test baking within a plugin.
     *
     * @return void
     */
    public function testBakePlugin()
    {
        $this->_loadTestPlugin('MaintenanceTest');
        $path = Plugin::path('MaintenanceTest');

        $this->Task->plugin = 'MaintenanceTest';
        $this->Task->expects($this->at(0))
            ->method('createFile')
            ->with(
                $this->_normalizePath($path . 'src/Maintenance/Mode/PluginExample.php'),
                $this->logicalAnd(
                    $this->stringContains('namespace MaintenanceTest\Maintenance\Mode'),
                    $this->stringContains('class PluginExample extends Mode')
                )
            );

        $result = $this->Task->bake('PluginExample');
        $this->assertSameAsFile('PluginExample.php', $result);
    }
}
