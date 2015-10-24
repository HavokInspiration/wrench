<%
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
%>
<?php
namespace <%= $namespace %>\Maintenance\Mode;

use Cake\Network\Request;
use Cake\Network\Response;
use Wrench\Mode\Mode;

/**
 * <%= $name %> Maintenance Mode
 */
class <%= $name %> extends Mode
{

    /**
     * Array containing the default config value for your maintenance mode
     * This value can be overridden when loading the mode
     * You can access a config value using $this->config('configkey');
     *
     * @see \Cake\Core\InstanceConfigTrait
     */
    protected $_defaultConfig = [];

    /**
     * Main method of the mode.
     *
     * If the mode is to take over the response of the current request, this
     * method should return a Response object. It can return null if the request
     * should follow the classic request cycle
     *
     * {@inheritDoc}
     */
    public function process(Request $request, Response $response)
    {
        return $response;
    }
}