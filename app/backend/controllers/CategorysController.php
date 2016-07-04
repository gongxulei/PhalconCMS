<?php

/**
 * 分类
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Controllers;

use \Marser\App\Backend\Controllers\BaseController,
    \Marser\App\Helpers\PaginatorHelper;

class CategorysController extends BaseController{

    public function initialize(){
        parent::initialize();
    }

    /**
     * 分类列表
     */
    public function indexAction(){
        $page = intval($this -> request -> get('page', 'trim'));
        $page <= 0 && $page = 1;
        $pagesize = 20;

        $paginator = $this -> get_repository('Categorys') -> get_list($page, $pagesize);
        $categoryList = $paginator -> items -> toArray();
        $pageNum = PaginatorHelper::get_paginator($paginator -> total_items, $page, $pagesize);



        $this -> view -> setVars(array(
            'categoryList' => $categoryList,
            'paginator' => $paginator,
            'pageNum' => $pageNum,
        ));
        $this -> view -> pick('categorys/index');
    }

    /**
     * 新增分类页面
     */
    public function writeAction(){
        $cid = intval($this -> request -> get('cid', 'trim'));

        /** 编辑操作，获取分类数据 */
        $category = array();
        if($cid > 0){
            $category = $this -> get_repository('Categorys') -> detail($cid);
        }
        /** 获取分类树 */
        $categoryTree = $this -> get_repository('Categorys') -> get_category_tree();

        $this -> view -> setVars(array(
            'cid' => $cid,
            'category' => $category,
            'categoryTree' => $categoryTree,
        ));
        $this -> view -> pick('categorys/write');
    }

    /**
     * 保存分类
     */
    public function saveAction(){
        try{
            if($this -> request -> isAjax() || !$this -> request -> isPost()){
                throw new \Exception('非法请求');
            }
            $cid = intval($this -> request -> get('cid', 'trim'));
            $name = $this -> request -> getPost('name', 'trim');
            $slug = $this -> request -> getPost('slug', 'trim');
            $description = $this -> request -> getPost('description', 'trim');
            $parentcid = intval($this -> request -> getPost('parentcid', 'trim'));
            /** 添加验证规则 */
            !empty($cid) && $this -> validator -> add_rule('cid', 'required', '系统错误，请刷新页面后重试');
            $this -> validator -> add_rule('name', 'required', '请填写分类名称')
                -> add_rule('name', 'chinese_alpha_numeric_dash', '站点名称由中英文字符、数字和中下划线组成');
            $this -> validator -> add_rule('slug', 'alpha_dash', '分类缩略名由英文字符、数字和中下划线组成');
            $this -> validator -> add_rule('description', 'xss_check', '请不要在分类描述中使用特殊字符');
            /** 截获验证异常 */
            if ($error = $this -> validator -> run(array(
                'cid' => $cid,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
            ))) {
                $error = array_values($error);
                $error = $error[0];
                throw new \Exception($error['message'], $error['code']);
            }
            /** 保存分类数据 */
            $result = $this -> get_repository('Categorys') -> save(array(
                'category_name' => $name,
                'slug' => $slug,
                'description' => $description,
                'parent_cid' => $parentcid,
            ), $cid);

            $this -> flashSession -> success('保存分类成功');
        }catch(\Exception $e){
            $this -> write_exception_log($e);

            $code = $e -> getCode();
            $message = $e -> getMessage();
            if($code == 23000){
                $message = '分类已存在，请重新填写';
            }
            $this -> flashSession -> error($message);
        }
        $url = $this -> get_module_uri('categorys/write');
        return $this -> response -> redirect($url);
    }

}
