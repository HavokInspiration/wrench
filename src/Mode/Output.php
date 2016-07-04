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

use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Stream;

/**
 * `Output` Maintenance Mode.
 * When used, it will send the content of the configured file as a response
 */
class Output extends Mode
{
    /**
     * Default config
     *
     * - `code` : The status code to be sent along with the response.
     * - `path` : location of the file
     * - `headers` : Additional headers to be set with the response
     *
     * @var array
     */
    protected $_defaultConfig = [
        'code' => 503,
        'path' => '',
        'headers' => []
    ];

    /**
     * {@inheritDoc}
     *
     * Will set the location where to redirect the request with the specified code
     * and optional additional headers.
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response)
    {
        $path = $this->_getPath();

        if (!file_exists($path)) {
            throw new LogicException(sprintf('The file (path : `%s`) does not exist.', $path));
        }

        $stream = new Stream(fopen($path, 'rb'));
        $response = $response->withBody($stream);
        $response = $response->withStatus($this->_config['code']);

        $response = $this->addHeaders($response);
        return $response;
    }

    /**
     * Return the path where the file to display is located.
     * If no path is provided, it is assumed that the file is located in {ROOT}/maintenance.html
     *
     * @return string File path
     */
    protected function _getPath()
    {
        $path = $this->_config['path'];

        if (empty($path)) {
            $path = ROOT . DS . 'maintenance.html';
        }

        return $path;
    }
}
