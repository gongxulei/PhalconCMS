<?php

/**
 *
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Controllers;

use \Marser\App\Backend\Controllers\BaseController,
    \Marser\App\Backend\Models\Services\Services;

class CategoryController extends BaseController{

    public function initialize(){
        parent::initialize();
    }

    /**
     * 新增分类页面
     */
    public function writeAction(){
        $categoryService = Services::get_service('categorys');
        $categoryList = $categoryService -> get_category_tree();

        $this -> view -> setVars(array(
            'categoryList' => $categoryList,
        ));
        $this -> view -> pick('category/write');
    }

    /**
     * 添加分类
     */
    public function addAction(){
        try{
            if($this -> request -> isAjax() || !$this -> request -> isPost()){
                throw new \Exception('非法请求');
            }
            $name = $this -> request -> getPost('name', 'trim');
            $slug = $this -> request -> getPost('slug', 'trim');
            $description = $this -> request -> getPost('description', 'trim');
            $parentcid = intval($this -> request -> getPost('parentcid', 'trim'));
            /** 添加验证规则 */
            $this -> validator -> add_rule('name', 'required', '请填写分类名称')
                -> add_rule('name', 'chinese_alpha_numeric_dash', '站点名称由中英文字符、数字和中下划线组成');
            $this -> validator -> add_rule('slug', 'alpha_dash', '分类缩略名由英文字符、数字和中下划线组成');
            $this -> validator -> add_rule('description', 'xss_check', '请不要在分类描述中使用特殊字符');
            /** 截获验证异常 */
            if ($error = $this -> validator -> run(array(
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
            ))) {
                $error = array_values($error);
                $error = $error[0];
                throw new \Exception($error['message'], $error['code']);
            }
            /** 根据parentcid获取root_cid */
            $categoryService = Services::get_service('categorys');
            $rootcid = 0;
            if($parentcid > 0) {
                $categoryArray = $categoryService->detail($parentcid);
                if (!is_array($categoryArray) || count($categoryArray) == 0) {
                    throw new \Exception('获取父分类失败');
                }
                $rootcid = !empty($categoryArray['root_cid']) ? $categoryArray['root_cid'] : $parentcid;
            }
            /** 分类数据入库 */
            $cid = $categoryService -> add(array(
                'category_name' => $name,
                'slug' => $slug,
                'description' => $description,
                'root_cid' => $rootcid,
                'parent_cid' => $parentcid,
            ));

            $this -> flashSession -> success('添加分类成功');
        }catch(\Exception $e){
            $this -> write_exception_log($e);

            $code = $e -> getCode();
            $message = $e -> getMessage();
            if($code == 23000){
                $message = '分类已存在，请重新填写';
            }
            $this -> flashSession -> error($message);
        }
        $url = $this -> get_module_uri('category/write');
        $this -> response -> redirect($url);
    }

}
