<?php

namespace Marser\App\Frontend\Controllers;

class IndexController extends \Phalcon\Mvc\Controller{

    public function testAction(){
        $this -> view -> pick('index/test');
    }

    public function notfoundAction(){
        echo 'frontend - 404';
    }
}