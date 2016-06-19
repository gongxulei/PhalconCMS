<?php

/**
 * 文章
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Controllers;

use \Marser\App\Backend\Controllers\BaseController,
    \Marser\App\Backend\Repositories\Repository;

class ArchivesController extends BaseController{

    /**
     * @var \Marser\App\Backend\Repositories\Articles
     */
    protected $repository;

    public function initialize(){
        parent::initialize();
        $this -> repository = Repository::get_repository('Articles');
    }

    /**
     * 文章列表
     */
    public function indexAction(){
        $this -> view -> pick('archives/index');
    }

    /**
     * 撰写新文章
     */
    public function writeAction(){
        $this -> view -> pick('archives/write');
    }

    /**
     * 发布文章
     */
    public function addAction(){
        try{
            if($this -> request -> isAjax() || !$this -> request -> isPost()){
                throw new \Exception('非法请求');
            }
            $title = $this -> request -> getPost('title', 'trim');
            $content = $this -> request -> getPost('content', 'trim');
            $modifyTime = $this -> request -> getPost('modifyTime', 'trim');
            $cid = $this -> request -> getPost('cid', 'trim');
            $tagName = $this -> request -> getPost('tagName', 'trim');
            /** 添加验证规则 */
            $this -> validator -> add_rule('title', 'required', '请填写标题');
            $this -> validator -> add_rule('content', 'required', '请填写文章内容');
            $this -> validator -> add_rule('cid', 'required', '请选择分类');
            !empty($modifyTime) && $this -> validator -> add_rule('modifyTime', 'check_time', '请选择发布时间');
            /** 截获验证异常 */
            if ($error = $this -> validator -> run(array(
                'title' => $title,
                'content' => $content,
                'modifyTime' => $modifyTime,
                'cid' => $cid,
            ))) {
                $error = array_values($error);
                $error = $error[0];
                throw new \Exception($error['message'], $error['code']);
            }
            /** 文章数据入库 */
            $this -> repository -> add(array(
                'title' => $title,
                'content' => $content,
                'modify_time' => strtotime($modifyTime),
                'cid' => $cid,
                'tag_name' => $tagName,
                'introduce' => $content,
            ));

            $this -> flashSession -> success('发布文章成功');
        }catch(\Exception $e){
            $this -> write_exception_log($e);

            $this -> flashSession -> error($e -> getMessage());
        }
        $url = $this -> get_module_uri('archives/index');
        return $this -> response -> redirect($url);
    }

    public function editAction(){
        try{
            if($this -> request -> isAjax() || !$this -> request -> isPost()){
                throw new \Exception('非法请求');
            }
            $aid = intval($this -> request -> getPost('aid', 'trim'));
            $title = $this -> request -> getPost('title', 'trim');
            $content = $this -> request -> getPost('content', 'trim');
            $modifyTime = $this -> request -> getPost('modifyTime', 'trim');
            $cid = $this -> request -> getPost('cid', 'trim');
            $tagName = $this -> request -> getPost('tagName', 'trim');
            /** 添加验证规则 */
            $this -> validator -> add_rule('aid', 'required', '系统错误，请刷新页面后重试');
            $this -> validator -> add_rule('title', 'required', '请填写标题');
            $this -> validator -> add_rule('content', 'required', '请填写文章内容');
            $this -> validator -> add_rule('cid', 'required', '请选择分类');
            !empty($modifyTime) && $this -> validator -> add_rule('modifyTime', 'check_time', '请选择发布时间');
            /** 截获验证异常 */
            if ($error = $this -> validator -> run(array(
                'aid' => $aid,
                'title' => $title,
                'content' => $content,
                'modifyTime' => $modifyTime,
                'cid' => $cid,
            ))) {
                $error = array_values($error);
                $error = $error[0];
                throw new \Exception($error['message'], $error['code']);
            }
            /** 更新文章数据 */
            $this -> repository -> edit(array(
                'title' => $title,
                'content' => $content,
                'modify_time' => strtotime($modifyTime),
                'cid' => $cid,
                'tag_name' => $tagName,
                'introduce' => $content,
            ), $aid);
            echo 'success';
        }catch(\Exception $e){
            $this -> write_exception_log($e);

            $this -> flashSession -> error($e -> getMessage());

            echo 'fail';
        }
        $this -> view -> disable();
    }
}
