<?php

/**
 * 分类Model
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Models;

use \Marser\App\Backend\Models\BaseModel;
use Sabre\DAVACL\Exception\AceConflict;

class CategorysModel extends BaseModel{

    const TABLE_NAME = 'categorys';

    public function initialize(){
        parent::initialize();
        $this -> set_table_source(self::TABLE_NAME);
    }

    /**
     * 分类列表
     * @param int $status
     * @return array
     * @throws \Exception
     */
    public function get_list($status=1){
        $status = intval($status);
        $params = array(
            'conditions' => 'status = :status:',
            'bind' => array(
                'status' => $status,
            ),
        );
        $result = $this -> find($params);
        if(!$result){
            throw new \Exception('查询数据失败');
        }
        return $result -> toArray();
    }

    /**
     * 获取分类数据
     * @param $cid
     * @return array
     * @throws \Exception
     */
    public function detail($cid){
        $category = array();
        $cid = intval($cid);
        if($cid < 0){
            throw new \Exception('参数错误');
        }

        $params = array(
            'conditions' => 'cid = :cid:',
            'bind' => array(
                'cid' => $cid
            )
        );
        $result = $this -> findFirst($params);
        if($result){
            $category = $result -> toArray();
        }
        return $category;
    }

    /**
     * 获取分类树
     * @param int $status
     * @return array
     */
    public function get_category_for_tree($status=1){
        $categoryList = array();
        $status = intval($status);
        $result = $this -> find(array(
            'columns' => 'cid, category_name, slug, root_cid, parent_cid',
            'conditions' => 'status = :status:',
            'bind' => array(
                'status' => $status,
            ),
            'order' => 'parent_cid DESC,  root_cid DESC',
        ));
        if($result){
            $categoryList = $result -> toArray();
        }
        return $categoryList;
    }

    /**
     * 分类数据入库
     * @param array $data
     * @return bool|int
     * @throws \Exception
     */
    public function add(array $data){
        $data = array_filter($data);
        if(!is_array($data) || count($data) == 0){
            throw new \Exception('参数错误');
        }
        $fields = array_keys($data);
        $values = array_values($data);

        $result = $this -> db -> insert($this -> getSource(), $values, $fields);
        if(!$result){
            throw new \Exception('数据入库失败');
        }
        $cid = $this -> db -> lastInsertId();
        return $cid;
    }

    /**
     * 更新分类数据
     * @param array $data
     * @param $cid
     * @return int
     * @throws \Exception
     */
    public function update_category(array $data, $cid){
        $data = array_filter($data);
        $data = array_unique($data);
        $cid = intval($cid);
        if(!is_array($data) || count($data) == 0 || $cid <= 0){
            throw new \Exception('参数错误');
        }
        $keys = array_keys($data);
        $values = array_values($data);
        $result = $this -> db -> update(
            $this->getSource(),
            $keys,
            $values,
            array(
                'conditions' => 'cid = ?',
                'bind' => array($cid)
            )
        );
        if(!$result){
            throw new \Exception('更新失败');
        }
        $affectedRows = $this -> db -> affectedRows();
        return $affectedRows;
    }


}
