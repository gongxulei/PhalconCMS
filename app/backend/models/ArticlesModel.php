<?php

/**
 * 文章模型
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Models;

use \Marser\App\Backend\Models\BaseModel;

class ArticlesModel extends BaseModel{

    const TABLE_NAME = 'articles';

    public function initialize(){
        parent::initialize();
        $this -> set_table_source(self::TABLE_NAME);
    }

    /**
     * 插入记录
     * @param array $data
     * @return bool|int
     * @throws \Exception
     */
    public function insert_record(array $data){
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
        $aid = $this -> db -> lastInsertId();
        return $aid;
    }

    /**
     * 更新记录
     * @param array $data
     * @param $aid
     * @return int
     * @throws \Exception
     */
    public function update_record(array $data, $aid){
        $aid = intval($aid);
        $data = array_filter($data);
        if($aid <= 0 || !is_array($data) || count($data) == 0){
            throw new \Exception('参数错误');
        }
        $keys = array_keys($data);
        $values = array_values($data);
        $result = $this -> db -> update(
            $this->getSource(),
            $keys,
            $values,
            array(
                'conditions' => 'aid = ?',
                'bind' => array($aid)
            )
        );
        if(!$result){
            throw new \Exception('更新失败');
        }
        $affectedRows = $this -> db -> affectedRows();
        return $affectedRows;
    }

    /**
     * 统计数量
     * @param int $status
     * @return mixed
     */
    public function get_count($status=1){
        $status = intval($status);
        $count = $this -> count(array(
            'conditions' => 'status = :status:',
            'bind' => array(
                'status' => $status
            ),
        ));
        return $count;
    }
}