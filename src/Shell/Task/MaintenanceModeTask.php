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
namespace Wrench\Shell\Task;

use Bake\Shell\Task\SimpleBakeTask;
use Cake\Utility\Inflector;

/**
 * Bake task responsible of generating Maintenance Mode skeleton
 */
class MaintenanceModeTask extends SimpleBakeTask
{

    /**
     * {@inheritDoc}
     */
    public $pathFragment = 'Maintenance/Mode/';

    /**
     * {@inheritDoc}
     */
    public function name()
    {
        return 'maintenance_mode';
    }

    /**
     * {@inheritDoc}
     */
    public function fileName($name)
    {
        return $name . '.php';
    }

    /**
     * {@inheritDoc}
     */
    public function template()
    {
        return 'Wrench.mode';
    }

    /**
     * {@inheritDoc}
     *
     * Adds the necessary class suffix and type in the Bake\Test instance responsible
     * of baking tests class files for the maintenance mode
     */
    public function bakeTest($className)
    {
        $suffixName = $typeName = $this->name();

        if (isset($this->Test->classSuffixes['entity'])) {
            $typeName = ucfirst($typeName);
        } else {
            $suffixName = $typeName = Inflector::camelize($typeName);
        }

        if (!isset($this->Test->classSuffixes[$suffixName])) {
            $this->Test->classSuffixes[$suffixName] = '';
        }

        if (!isset($this->Test->classTypes[$typeName])) {
            $this->Test->classTypes[$typeName] = 'Maintenance\Mode';
        }

        return parent::bakeTest($className);
    }
}
