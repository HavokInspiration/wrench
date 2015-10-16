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
namespace Wrench\Routing\Filter;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Response;
use Cake\Routing\DispatcherFilter;
use Wrench\Mode\Exception\MissingModeException;

/**
 * Dispatcher filter responsible of intercepting request to
 * deal with the application being under maintenance
 */
class MaintenanceModeFilter extends DispatcherFilter
{

    /**
     * Configuration of the mode for this instance of the filter
     *
     * @var \Wrench\Mode\ModeInterface
     */
    protected $_mode;

    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = [
        'when' => null,
        'for' => null,
        'priority' => 1,
        'ipWhiteList' => []
    ];

    /**
     * @inheritDoc
     * @throws \Wrench\Mode\Exception\MissingModeException When the specified mode can not be loaded
     */
    public function __construct($config = [])
    {
        if (empty($config['mode']['className'])) {
            throw new MissingModeException(['mode' => '']);
        }

        $className = $config['mode']['className'];

        $filterConfig = !empty($config['mode']['config']) ? $config['mode']['config'] : [];
        unset($config['mode']);

        $this->mode($className, $filterConfig);

        parent::__construct($config);
    }

    /**
     * Sets the mode instance. If a string is passed it will be treated
     * as a class name and will be instantiated.
     *
     * If no params are passed it will return the current mode instance.
     *
     * @param \Wrench\Mode\Mode|string|null $mode The mode instance to use.
     * @param array $config Either config for a new driver or null.
     * @return \Wrench\Mode\Mode
     *
     * @throws \Wrench\Mode\Exception\MissingModeException When the specified mode can not be loaded
     */
    public function mode($mode = null, $config = [])
    {
        if ($mode === null) {
            return $this->_mode;
        }

        if (is_string($mode)) {
            if (!class_exists($mode)) {
                throw new MissingModeException(['mode' => $mode]);
            }

            $this->_mode = new $mode($config);
        }

        return $this->_mode;
    }

    /**
     * @inheritDoc
     *
     * @return \Cake\Network\Response|null
     */
    public function beforeDispatch(Event $event)
    {
        if (!Configure::read('Wrench.enable')) {
            return null;
        }

        $request = $event->data['request'];
        $ip = $request->clientIp();
        $whiteList = $this->config('ipWhiteList');

        if (!empty($whiteList) && in_array($ip, $whiteList)) {
            return null;
        }

        $response = $event->data['response'];
        $response = $this->mode()->process($request, $response);

        if ($response instanceof Response) {
            $event->stopPropagation();
            return $response;
        }
    }
}
