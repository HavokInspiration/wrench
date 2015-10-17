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
 * `Redirect` Maintenance Mode
 * When used, it will perform a redirect to a specific URL with the
 * status code specified
 *
 * If no URL is provided, a default one will be built with the current base path
 * and pointing to a `maintenance.html` file
 */
class Redirect extends Mode
{

    /**
     * Default config
     *
     * @var array
     */
    protected $_defaultConfig = [
        'code' => 307,
        'url' => ''
    ];

    public function process(Request $request, Response $response)
    {
        $url = $this->getUrl($request);

        $response->statusCode($this->config('code'));
        $response->location($url);
        return $response;
    }

    protected function getUrl(Request $request)
    {
        $url = $this->config('url');

        if (empty($url)) {
            $url = $request->base . '/maintenance.html';
        }

        return $url;
    }
}