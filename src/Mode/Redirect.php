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

/**
 * `Redirect` Maintenance Mode.
 * When used, it will perform a redirect to a specific URL with the
 * status code specified.
 *
 * If no URL is provided, a default one will be built with the current base path
 * and pointing to a `maintenance.html` file.
 */
class Redirect extends Mode
{

    /**
     * Default config
     *
     * - `code` : The status code to be sent along with the response.
     * Should be a code in the 3XX range. Other code range may not work
     * - `url` : URL where to redirect the request. If no url is provided,
     * a URL will be built based on the base URL path and pointing to a
     * "maintenance.html" file (located under /webroot
     * - `headers` : Additional headers to be set with the response
     *
     * @var array
     */
    protected $_defaultConfig = [
        'code' => 307,
        'url' => '',
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
        $url = $this->_getUrl($request);

        $response->statusCode($this->_config['code']);
        $response->location($url);

        $headers = $this->_config['headers'];
        if (!empty($headers)) {
            $response->header($headers);
        }
        return $response;
    }

    /**
     * Return the URL where to redirect the request.
     * If no URL is provided, a default one will be built with the current base path
     * and pointing to a `maintenance.html` file.
     *
     * @param \Cake\Network\Request $request Request that can be used to get the URL.
     *
     * @return string URL where to redirect
     */
    protected function _getUrl(Request $request)
    {
        $url = $this->_config['url'];

        if (empty($url)) {
            $url = $request->base . '/maintenance.html';
        }

        return $url;
    }
}
