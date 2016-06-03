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

class ArchivesController extends BaseController{

    public function initialize(){
        parent::initialize();
    }

    /**
     * 撰写新文章
     */
    public function writeAction(){
        $this -> view -> pick('archives/write');
    }

    public function addAction(){

    }
}
