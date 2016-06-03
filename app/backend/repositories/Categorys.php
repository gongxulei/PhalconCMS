<?php

/**
 * 分类数据仓库
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

use \Marser\App\Backend\Repositories\BaseRepository,
    \Marser\App\Backend\Models\CategorysModel;

class Categorys extends BaseRepository{

    /**
     * model对象
     * @var \Marser\App\Backend\Models\CategorysModel
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this -> model = new CategorysModel();
    }

    /**
     * 获取分类树
     * @return array
     * @throws \Exception
     */
    public function get_category_tree(){
        $categoryList = $this -> model -> get_category_for_tree();
        if(!is_array($categoryList) || count($categoryList) == 0){
            return $categoryList;
        }
        $categoryArray = $categoryTree = array();
        foreach($categoryList as $clk=>$clv){
            $categoryArray[$clv['cid']] = $clv;
        }
        unset($categoryList);
        foreach($categoryArray as $cak=>&$cav){
            if(isset($categoryArray[$cav['parent_cid']])){
                $categoryArray[$cav['parent_cid']]['son'][$cav['cid']] = &$cav;
            }else{
                $categoryTree[$cav['cid']] = &$cav;
            }
        }
        return $categoryTree;
    }

    /**
     * 获取分类数据
     * @param $cid
     * @return array
     * @throws \Exception
     */
    public function detail($cid){
        $category = $this -> model -> detail($cid);
        return $category;
    }

    /**
     * 添加分类
     * @param array $data
     * @return mixed
     */
    public function add(array $data){
        if(!isset($data['create_by']) || empty($data['create_by'])){
            $data['create_by'] = $this -> _di -> get('session') -> get('user')['uid'];
        }
        if(!isset($data['create_time']) || empty($data['create_time'])){
            $data['create_time'] = time();
        }
        if(!isset($data['modify_by']) || empty($data['modify_by'])){
            $data['modify_by'] = $this -> _di -> get('session') -> get('user')['uid'];
        }
        if(!isset($data['modify_time']) || empty($data['modify_time'])){
            $data['modify_time'] = time();
        }
        $cid = $this -> model -> add($data);
        return $cid;
    }

    /**
     * 更新分类
     * @param array $data
     * @param $cid
     * @return int
     * @throws \Exception
     */
    public function update(array $data, $cid){
        if(isset($data['parent_cid']) && ($data['parent_cid'] == $cid)){
            throw new \Exception('不能选择本分类为父分类');
        }
        /** 根据parent_cid获取root_cid */
        $rootcid = 0;
        if(isset($data['parent_cid']) && ($data['parent_cid'] > 0)) {
            $category = $this -> detail($data['parent_cid']);
            if (!is_array($category) || count($category) == 0) {
                throw new \Exception('获取父分类失败');
            }
            $rootcid = !empty($category['root_cid']) ? $category['root_cid'] : $data['parent_cid'];
        }
        $data['root_cid'] = $rootcid;

        if(!isset($data['modify_by']) || empty($data['modify_by'])){
            $data['modify_by'] = $this -> _di -> get('session') -> get('user')['uid'];
        }
        if(!isset($data['modify_time']) || empty($data['modify_time'])){
            $data['modify_time'] = time();
        }
        $affectedRows = $this -> model -> update_category($data, $cid);
        return $affectedRows;
    }
}