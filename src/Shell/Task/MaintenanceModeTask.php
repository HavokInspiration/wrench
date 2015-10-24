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

/**
 * Bake task responsible of generating Maintenance Mode skeleton
 */
class MaintenanceModeTask extends SimpleBakeTask
{

    /**
     * {@inheritdoc}
     */
    public $pathFragment = 'Maintenance/Mode/';

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return 'maintenance_mode';
    }

    /**
     * {@inheritdoc}
     */
    public function fileName($name)
    {
        return $name . '.php';
    }

    /**
     * {@inheritdoc}
     */
    public function template()
    {
        return 'Wrench.mode';
    }

    /**
     * {@inheritdoc}
     *
     * Adds the necessary class suffix and type in the Bake\Test instance responsible
     * of baking tests class files for the maintenance mode
     */
    public function bakeTest($className)
    {
        if (!isset($this->Test->classSuffixes[$this->name()])) {
            $this->Test->classSuffixes[$this->name()] = '';
        }

        $name = ucfirst($this->name());
        if (!isset($this->Test->classTypes[$name])) {
            $this->Test->classTypes[$name] = 'Maintenance\Mode';
        }

        return parent::bakeTest($className);
    }
}