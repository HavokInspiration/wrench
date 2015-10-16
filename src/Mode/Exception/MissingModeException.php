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
namespace Wrench\Mode\Exception;

use Cake\Core\Exception\Exception;

class MissingModeException extends Exception
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'Maintenance Mode `%s` could not be found.';
}
