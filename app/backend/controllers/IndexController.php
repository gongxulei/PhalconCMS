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

    public function indexAction(){
        $url = $this -> get_module_uri('dashboard/index');
        return $this->redirect($url);
    }

    public function notfoundAction(){
        echo 'backend 404';exit;
    }
}