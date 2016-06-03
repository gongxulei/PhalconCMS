<?php

/**
 *
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Models\Services;

use \Phalcon\Di;

class BaseService {

    /**
     * DI对象
     * @var \Phalcon\Di
     */
    protected $_di;

    public function __construct(){
        $this -> _di = Di::getDefault();
    }
}
