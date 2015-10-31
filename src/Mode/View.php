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

use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\Network\Response;

/**
 * `Output` Maintenance Mode.
 * When used, it will send the content of the configured file as a response
 */
class View extends Mode
{

    /**
     * Default config
     *
     * - `code` : The status code to be sent along with the response.
     * - `view` : Array of parameters to pass to the View class constructor. Only the following options are supported :
     *      - `className` : Fully qualified class name of the View class to use. Default to AppView
     *      - `templatePath` : Path to the template you wish to display (relative to your ``src/Template`` directory).
     *         You can use plugin dot notation.
     *      - `template` : Template name to use. Default to "template".
     *      - `plugin` : Theme where to find the layout and template
     *      - `theme` : Same thing than plugin
     *      - `layout` : Layout name to use. Default to "default"
     *      - `layoutPath` : Path to the layout you wish to display (relative to your ``src/Template/Layout``directory).
     *         You can use plugin dot notation. Default to "Layout"
     *      All other options are not supported (they might work though)
     * - `headers` : Additional headers to be set with the response
     *
     * @var array
     */
    protected $_defaultConfig = [
        'code' => 503,
        'view' => [
            'className' => 'App\View\AppView',
            'templatePath' => null,
            'template' => 'maintenance',
            'plugin' => null,
            'theme' => null,
            'layout' => null,
            'layoutPath' => null
        ],
        'headers' => []
    ];

    /**
     * {@inheritDoc}
     *
     * Will set the location where to redirect the request with the specified code
     * and optional additional headers.
     */
    public function process(Request $request, Response $response)
    {
        $this->_backwardCompatibility();

        $className = $this->config('view.className');
        if (empty($className)) {
            $className = 'App\View\AppView';
        }

        $viewConfig = $this->config('view') ?: [];
        $view = new $className($request, $response, null, $viewConfig);
        $response->body($view->render());
        $response->statusCode($this->config('code'));

        $headers = $this->config('headers');
        if (!empty($headers)) {
            $response->header($headers);
        }
        return $response;
    }

    /**
     * Generate correct View constructor parameter key if CakePHP version
     * is below 3.1 where important changes were introduced regarding naming
     * Also compensate for a fix that is not existant prior to 3.0.5 in the InstanceConfigTrait
     *
     * @return void
     */
    protected function _backwardCompatibility()
    {
        if ($this->config('view') === null) {
            $this->config('view', $this->_defaultConfig['view']);
        }

        if (version_compare(Configure::version(), '3.1.0', '<')) {
            $config = $this->config('view');
            $config['view'] = $this->config('view.template');
            $config['viewPath'] = $this->config('view.templatePath');
            $this->config('view', $config);
        }
    }
}
