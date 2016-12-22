<?php

/**
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Frontend;

use \Phalcon\Loader,
    \Phalcon\Mvc\View,
    \Phalcon\DiInterface,
    \Phalcon\Mvc\Dispatcher,
    \Phalcon\Mvc\ModuleDefinitionInterface;

class FrontendModule implements ModuleDefinitionInterface{

    public function registerAutoloaders(DiInterface $di=null){

    }

    public function registerServices(DiInterface $di){
        $systemConfig = $di -> get('systemConfig');

        /**
         * DI注册前台dispatcher
         */
        $di->set('dispatcher', function() use ($systemConfig) {
            $eventsManager = new \Phalcon\Events\Manager();
            $eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) {
                if ($event->getType() == 'beforeException') {
                    switch ($exception->getCode()) {
                        case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                            $dispatcher->forward(array(
                                'controller' => 'Index',
                                'action' => 'notfound'
                            ));
                            return false;
                        case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $dispatcher->forward(array(
                                'controller' => 'Index',
                                'action' => 'notfound'
                            ));
                            return false;
                    }
                }
            });
            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setEventsManager($eventsManager);
            //默认设置为前台的调度器
            $dispatcher->setDefaultNamespace($systemConfig -> app -> root_namespace . '\\App\\Frontend\\Controllers');
            return $dispatcher;
        }, true);

        /**
         * DI注册url服务
         */
        $di -> setShared('url', function() use($systemConfig) {
            $url = new \Marser\App\Core\PhalBaseUrl();
            $url -> setBaseUri($systemConfig -> app -> frontend -> module_pathinfo);
            return $url;
        });

        /**
         * DI注册前台view
         */
        $di -> setShared('view', function() use($systemConfig) {
            $view = new \Phalcon\Mvc\View();
            $view -> setViewsDir($systemConfig -> app -> frontend -> views);
            $view -> registerEngines(array(
                '.phtml' => function($view, $di) use($systemConfig) {
                    //$volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
                    $volt = new \Marser\App\Core\PhalBaseVolt($view, $di);
                    $volt -> setOptions(array(
                        'compileAlways' => $systemConfig -> app -> frontend -> is_compiled,
                        'compiledPath'  =>  $systemConfig -> app -> frontend -> compiled_path
                    ));
                    $volt -> initFunction();
                    return $volt;
                },
            ));
            return $view;
        });
    }
}