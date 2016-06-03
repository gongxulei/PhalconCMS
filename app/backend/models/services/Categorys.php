<?php

/**
 *
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Models\Services;

use \Marser\App\Backend\Models\Repositories\Repositories,
    \Marser\App\Backend\Models\Services\BaseService;

class Categorys extends BaseService{

    /**
     * model对象
     * @var \Marser\App\Backend\Models\Repositories\CategorysModel
     */
    protected $categorysModel;

    public function __construct(){
        parent::__construct();
        $this -> categorysModel = Repositories::get_repository('CategorysModel');
    }

    /**
     * 获取分类树
     * @return array
     * @throws \Exception
     */
    public function get_category_tree(){
        $categoryList = $this -> categorysModel -> get_category_for_tree();
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
        $category = $this -> categorysModel -> detail($cid);
        return $category;
    }

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
        $cid = $this -> categorysModel -> add($data);
        return $cid;
    }
}