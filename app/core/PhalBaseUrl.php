<?php

/**
 * Phalcon URL扩展
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Core;

class PhalBaseUrl extends \Phalcon\Mvc\Url{

    /**
     * 分模块生成URL
     * @param $modulePathinfo
     * @param $uri
     * @return string
     */
    public function get_module_uri($modulePathinfo, $uri){
        $uri = "{$modulePathinfo}/{$uri}";
        $url = $this -> get($uri);
        return $url;
    }
}
