<?php

/**
 *
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Models\Services;

Abstract class Services{

    /**
     * 返回服务对象
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public static function get_service($name){
        $className = __NAMESPACE__ . "\\" . ucfirst($name);
        if(!class_exists($className)){
            throw new \Exception("{$className}类不存在");
        }
        return new $className();
    }
}
