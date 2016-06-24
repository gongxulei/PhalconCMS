<?php

/**
 * 文章
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
     * 发布文章（添加、编辑）
     */
    public function publishAction(){
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
            $this -> validator -> add_rule('title', 'required', '请填写标题');
            $this -> validator -> add_rule('content', 'required', '请填写文章内容');
            $this -> validator -> add_rule('cid', 'required', '请选择分类');
            !empty($aid) && $this -> validator -> add_rule('aid', 'required', '系统错误，请刷新页面后重试');
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
            /** 发布文章 */
            $this -> get_repository('Articles') -> save(array(
                'title' => $title,
                'content' => $content,
                'modify_time' => strtotime($modifyTime),
                'cid' => $cid,
                'tag_name' => $tagName,
                'introduce' => $content,
            ), $aid);

            $this -> flashSession -> success('发布文章成功');
        }catch(\Exception $e){
            $this -> write_exception_log($e);

            $this -> flashSession -> error($e -> getMessage());
        }
        $url = $this -> get_module_uri('archives/index');
        return $this -> response -> redirect($url);
    }
}
