<?php

/**
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Frontend\Controllers;

class IndexController extends \Phalcon\Mvc\Controller{

    public function testAction(){
        $this -> view -> pick('index/test');
    }

    public function notfoundAction(){
        echo 'frontend - 404';
    }
}