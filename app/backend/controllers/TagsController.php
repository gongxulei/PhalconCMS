<?php

/**
 * 标签
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Controllers;

use \Marser\App\Backend\Controllers\BaseController,
    \Marser\App\Backend\Repositories\Repository;

class TagsController extends BaseController{

    /**
     * @var \Marser\App\Backend\Repositories\Tags
     */
    protected $repository;

    public function initialize(){
        parent::initialize();
        $this -> repository = Repository::get_repository('Tags');
    }

    /**
     * 标签列表页
     */
    public function indexAction(){
        $tagsList = $this -> repository -> get_list();

        $this -> view -> pick('tags/index');
    }

    /**
     * 添加标签
     */
    public function addAction(){
        try{
            if($this -> request -> isAjax() || !$this -> request -> isPost()){
                throw new \Exception('非法请求');
            }
            $tagName = $this -> request -> get('name', 'trim');
            $slug = $this -> request -> get('slug', 'trim');
            /** 添加验证规则 */
            $this -> validator -> add_rule('name', 'required', '请填写标签名称')
                -> add_rule('name', 'chinese_alpha_numeric_dash', '站点名称由中英文字符、数字和中下划线组成');
            $this -> validator -> add_rule('slug', 'alpha_dash', '分类缩略名由英文字符、数字和中下划线组成');
            /** 截获验证异常 */
            if ($error = $this -> validator -> run(array(
                'name' => $tagName,
                'slug' => $slug,
            ))) {
                $error = array_values($error);
                $error = $error[0];
                throw new \Exception($error['message'], $error['code']);
            }
            /** 标签数据入库 */
            $tid = $this -> repository -> add(array(
                'tag_name' => $tagName,
                'slug' => $slug,
            ));

            $this -> flashSession -> success('添加标签成功');
        }catch(\Exception $e){
            $this -> write_exception_log($e);

            $code = $e -> getCode();
            $message = $e -> getMessage();
            if($code == 23000){
                $message = '标签已存在，请重新填写';
            }
            $this -> flashSession -> error($message);
        }
        $url = $this -> get_module_uri('tags/index');
        return $this -> response -> redirect($url);
    }

    /**
     * 更新标签
     */
    public function editAction(){
        try{
            if($this -> request -> isAjax() || !$this -> request -> isPost()){
                throw new \Exception('非法请求');
            }
            $tid = intval($this -> request -> get('tid', 'trim'));
            $tagName = $this -> request -> get('name', 'trim');
            $slug = $this -> request -> get('slug', 'trim');
            /** 添加验证规则 */
            $this -> validator -> add_rule('tid', 'required', '系统错误，请刷新页面后重试');
            $this -> validator -> add_rule('name', 'required', '请填写标签名称')
                -> add_rule('name', 'chinese_alpha_numeric_dash', '标签名称由中英文字符、数字和中下划线组成');
            $this -> validator -> add_rule('slug', 'alpha_dash', '标签缩略名由英文字符、数字和中下划线组成');
            /** 截获验证异常 */
            if ($error = $this -> validator -> run(array(
                'tid' => $tid,
                'name' => $tagName,
                'slug' => $slug,
            ))) {
                $error = array_values($error);
                $error = $error[0];
                throw new \Exception($error['message'], $error['code']);
            }
            /** 更新标签数据 */
            $affectedRows = $this -> repository -> update(array(
                'tag_name' => $tagName,
                'slug' => $slug,
            ), $tid);
            if(!$affectedRows){
                throw new \Exception('标签数据更新失败');
            }
        }catch(\Exception $e){
            $this -> write_exception_log($e);

            $this -> flashSession -> error($e -> getMessage());
        }
        $url = $this -> get_module_uri('tags/index');
        return $this -> response -> redirect($url);
    }

}