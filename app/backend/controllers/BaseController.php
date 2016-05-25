<?php

/**
 * 后台基类控制器
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Controllers;
use \Marser\App\Core\PhalBaseController;

class BaseController extends PhalBaseController{

    public function initialize(){
        parent::initialize();
        $this -> set_common_vars();
    }

    /**
     * 设置模块公共变量
     */
    public function set_common_vars(){
        $this -> view -> setVars(array(
            'title' => $this -> systemConfig -> get('app', 'app_name'),
            'assetsUrl' => $this -> systemConfig -> get('app', 'backend', 'assets_url'),
            'assetsVersion' => strtotime(date('Y-m-d H', time()) . ":00:00"),
            'modulePathinfo' => $this -> systemConfig -> get('app', 'backend', 'module_pathinfo'),
        ));
    }

    /**
     * 生成后台模块URL
     * @param $uri
     * @return mixed
     */
    public function get_module_uri($uri){
        $modulePathinfo = $this -> systemConfig -> get('app', 'backend', 'module_pathinfo');
        return $this -> url -> get_module_uri($modulePathinfo, $uri);
    }
}