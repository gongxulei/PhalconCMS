<?php

/**
 * 数据仓库
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

Abstract class Repository{

    /**
     * 返回model对象
     * @param $modelName
     * @return mixed
     * @throws \Exception
     */
    public static function get_repository($modelName){
        $className = __NAMESPACE__ . "\\" . ucfirst($modelName);
        if(!class_exists($className)){
            throw new \Exception("{$className}类不存在");
        }
        return new $className();
    }
}
