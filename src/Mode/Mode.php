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

abstract class Mode
{

    use InstanceConfigTrait;

    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = [
        'code' => 503
    ];

    public function __construct($config = [])
    {
        $this->config($config);
    }

    abstract public function process(Request $request, Response $response);
}
