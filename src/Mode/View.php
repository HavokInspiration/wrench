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
use Cake\Http\RequestTransformer;
use Cake\Http\ResponseTransformer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Stream;

/**
 * `View` Maintenance Mode.
 *
 * When used, it will render the defined View and use it as the body of the
 * response to return
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
     * Will render the view and use the content as the body of the response.
     * It will also set the specified HTTP code and optional additional headers.
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response)
    {
        $className = $this->_config['view']['className'];
        if (empty($className)) {
            $className = 'App\View\AppView';
        }

        $viewConfig = $this->_config['view'] ?: [];
        $view = new $className(
            $request,
            $response,
            null,
            $viewConfig
        );

        $stream = new Stream(fopen('php://memory', 'r+'));
        $stream->write($view->render());
        $response = $response->withBody($stream);
        $response = $response->withStatus($this->_config['code']);

        $response = $this->addHeaders($response);

        return $response;
    }
}
