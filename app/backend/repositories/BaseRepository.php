<?php

/**
 * 数据仓库基类
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

use \Phalcon\Di;

class BaseRepository {

    /**
     * DI对象
     * @var \Phalcon\Di
     */
    protected $_di;

    public function __construct(){
        $this -> _di = Di::getDefault();
    }
}
