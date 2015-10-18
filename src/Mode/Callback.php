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

use Cake\Network\Request;
use Cake\Network\Response;
use InvalidArgumentException;

/**
 * `Callback` Maintenance Mode.
 * When used, it will perform the callable given as parameter `callback`
 *
 * This callable should return a Response object if the request should be intercepted
 * and short-circuit the rest of the request life cycle.
 */
class Callback extends Mode
{

    /**
     * Will try to call the callback pass as parameter
     *
     * @inheritDoc
     * @throw \InvalidArgumentException if the callback parameter is not a proper callable.
     */
    public function process(Request $request, Response $response)
    {
        $callback = $this->config('callback');
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('You must pass a valid callable as the `callback` argument.');
        }

        return $callback($request, $response);
    }
}