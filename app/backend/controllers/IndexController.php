<?php

/**
 *
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */
namespace marser\app\backend\controllers;

class IndexController extends \Phalcon\Mvc\Controller{

    public function testAction(){
        $this -> view -> title = 'index/test';
        $this -> view -> pick('index/test');
        $this -> view -> setMainView('main');
    }

    public function aAction(){
        $this -> view -> title = 'aaaa';
        $this -> view -> pick('index/a');
    }

    public function notfoundAction(){
        echo 'backend 404';exit;
    }
}