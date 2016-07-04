<?php

/**
 * 分类业务仓库
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

use \Marser\App\Backend\Repositories\BaseRepository;

class Categorys extends BaseRepository{

    public function __construct(){
        parent::__construct();
    }

    /**
     * 分类列表
     * @param int $page
     * @param int $pagesize
     * @return mixed
     */
    public function get_list($page = 1, $pagesize = 20){
        $page = intval($page);
        $page <= 0 && $page = 1;
        $pagesize = intval($pagesize);
        $pagesize <= 0 && $pagesize = 20;

        $paginator = $this -> get_model('CategorysModel') -> get_list(1, array(
            'limit' => array(
                'page' => $page,
                'number' => $pagesize
            )
        ));
        return $paginator;
    }

    /**
     * 统计数量
     * @return mixed
     */
    public function get_count(){
        $count = $this -> get_model('CategorysModel') -> get_count();
        return $count;
    }

    /**
     * 获取分类树
     * @return array
     * @throws \Exception
     */
    public function get_category_tree(){
        $categoryList = $this -> get_model('CategorysModel') -> get_category_for_tree();
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
        $category = $this -> get_model('CategorysModel') -> detail($cid);
        return $category;
    }

    /**
     * 保存分类数据
     * @param array $data
     * @param $cid
     * @return int|mixed
     * @throws \Exception
     */
    public function save(array $data, $cid){
        $cid = intval($cid);
        if($cid <= 0){
            /** 添加分类 */
            $cid = $this -> insert_record($data);
            return $cid;
        }else{
            /** 更新分类 */
            $affectedRows = $this -> update_record($data, $cid);
            if(!$affectedRows){
                throw new \Exception('保存分类失败');
            }
            return $affectedRows;
        }
    }

    /**
     * 根据parent_cid获取root_cid
     * @param $parentcid
     * @return int
     * @throws \Exception
     */
    protected function get_rootcid_by_parentcid($parentcid){
        $parentcid = intval($parentcid);
        $rootcid = 0;
        if($parentcid > 0) {
            $category = $this->detail($parentcid);
            if (!is_array($category) || count($category) == 0) {
                throw new \Exception('获取父分类失败');
            }
            $rootcid = !empty($category['root_cid']) ? $category['root_cid'] : $parentcid;
        }
        return $rootcid;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function before_insert(array $data){
        empty($data['create_by']) && $data['create_by'] = $this -> getDI() -> get('session') -> get('user')['uid'];
        empty($data['create_time']) && $data['create_time'] = time();
        empty($data['modify_by']) && $data['modify_by'] = $this -> getDI() -> get('session') -> get('user')['uid'];
        empty($data['modify_time']) && $data['modify_time'] = time();
        return $data;
    }

    /**
     * 添加分类
     * @param array $data
     * @return mixed
     */
    public function insert_record(array $data){
        $data['root_cid'] = $this -> get_rootcid_by_parentcid($data['parent_cid']);
        $data = $this -> before_insert($data);
        $cid = $this -> get_model('CategorysModel') -> insert_record($data);
        return $cid;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function before_update(array $data){
        empty($data['modify_by']) && $data['modify_by'] = $this -> getDI() -> get('session') -> get('user')['uid'];
        empty($data['modify_time']) && $data['modify_time'] = time();
        return $data;
    }

    /**
     * 更新分类
     * @param array $data
     * @param $cid
     * @return int
     * @throws \Exception
     */
    public function update_record(array $data, $cid){
        if(isset($data['parent_cid']) && ($data['parent_cid'] == $cid)){
            throw new \Exception('不能选择本分类为父分类');
        }
        $data['root_cid'] = $this -> get_rootcid_by_parentcid($data['parent_cid']);

        $data = $this -> before_update($data);
        $affectedRows = $this -> get_model('CategorysModel') -> update_record($data, $cid);
        return $affectedRows;
    }
}