<?php

/**
 * 登录控制器
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace marser\app\backend\controllers;
use \marser\app\backend\controllers\BaseController;

class PassportController extends BaseController{

    /**
     * 登录页
     */
    public function indexAction(){
        $this -> view -> pick();
    }

    /**
     * 登录处理
     * @throws \Exception
     * @author Marser
     */
    public function loginAction(){
        try {
            $username = $this->request->get('username', 'trim');
            $password = $this->request->get('password', 'trim');
            if(empty($username) || empty($password)){
                throw new \Exception('用户名和姓名不能为空');
            }

            $this->ajax_return('success', 1);
        }catch(\Exception $e){
            $this -> write_exception_log($e);

            $code = !empty($e -> getCode()) ? $e -> getCode() : 500;
            $this -> ajax_return($e -> getMessage(), $code);
        }
    }
}