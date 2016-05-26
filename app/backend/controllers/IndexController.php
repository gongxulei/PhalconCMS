<?php

/**
 *
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */
namespace Marser\App\Backend\Controllers;

use \Marser\App\Backend\Controllers\BaseController;

class IndexController extends BaseController{

    public function testAction(){
        $this -> view -> setVars(
            array(
                'menu' =>  array(
                    'menu_1' => array(
                        'title'     =>  'fuck fuck fuck',
                        'addLink'   =>  'www.baidu.com',
                    ),
                ),
            )
        );
        $this -> view -> pick('index/test');
    }

    public function aAction(){
        $this -> view -> title = 'aaaa';
        $this -> view -> pick('index/a');
    }

    public function notfoundAction(){
        echo 'backend 404';exit;
    }
}