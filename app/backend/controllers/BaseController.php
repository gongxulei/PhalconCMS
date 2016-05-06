<?php

/**
 * 后台基类控制器
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace marser\app\backend\controllers;
use \marser\app\core\PhalBaseController;

class BaseController extends PhalBaseController{

    public function initialize(){
        parent::initialize();
    }

    /**
     * ajax输出
     * @param $message
     * @param int $code
     * @param array $data
     * @author Marser
     */
    protected function ajax_return($message, $code=1, array $data=array()){
        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data,
        );
        $this -> response -> setContent(json_encode($result));
        $this -> response -> send();
    }
}