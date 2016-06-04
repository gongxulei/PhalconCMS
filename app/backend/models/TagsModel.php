<?php

/**
 * 标签model
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Models;

use \Marser\App\Backend\Models\BaseModel;

class TagsModel extends BaseModel{

    const TABLE_NAME = 'tags';

    public function initialize(){
        parent::initialize();
        $this -> set_table_source(self::TABLE_NAME);
    }

    /**
     * 标签列表
     * @param int $status
     * @param array $ext
     * @return array
     * @throws \Exception
     */
    public function get_list($status=1, array $ext=array()){
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
        $tagsList = $result -> toArray();
        return $tagsList;
    }

    /**
     * 标签数据入库
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
        $tid = $this -> db -> lastInsertId();
        return $tid;
    }

    /**
     * 标签更新
     * @param array $data
     * @param $tid
     * @return int
     * @throws \Exception
     */
    public function update_tag(array $data, $tid){
        $data = array_filter($data);
        $cid = intval($tid);
        if(!is_array($data) || count($data) == 0 || $tid <= 0){
            throw new \Exception('参数错误');
        }
        $keys = array_keys($data);
        $values = array_values($data);
        $result = $this -> db -> update(
            $this->getSource(),
            $keys,
            $values,
            array(
                'conditions' => 'tid = ?',
                'bind' => array($tid)
            )
        );
        if(!$result){
            throw new \Exception('更新失败');
        }
        $affectedRows = $this -> db -> affectedRows();
        return $affectedRows;
    }
}