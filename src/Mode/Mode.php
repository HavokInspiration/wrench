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
namespace Wrench\Mode;

use Cake\Core\InstanceConfigTrait;
use Cake\Network\Request;
use Cake\Network\Response;

/**
 * Base class that Maintenance mode should extend
 */
abstract class Mode
{

    use InstanceConfigTrait;

    /**
     * Constructor.
     * Will set the config using the methods from the InstanceConfigTrait
     *
     * @param array $config Array of parameters for the Mode
     */
    public function __construct($config = [])
    {
        $this->config($config);
    }

    /**
     * Main method that will be called if the MaintenanceModeFilter has to be used
     * This method should return the response that will be sent in order to warn the
     * user that the current request can not be processed because the app is undergoing
     * maintenance
     *
     * Maintenance modes should extend and implement this method to return the proper
     * response to the user.
     *
     * @param \Cake\Network\Request $request Current request being intercepted
     * @param \Cake\Network\Response $response Current response being sent
     * @return \Cake\Network\Response|null The response that will be sent
     */
    abstract public function process(Request $request, Response $response);
}
