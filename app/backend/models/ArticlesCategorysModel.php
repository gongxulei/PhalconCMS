<?php

/**
 * 文章-分类关联模型
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Models;

use \Marser\App\Backend\Models\BaseModel;

class ArticlesCategorysModel extends BaseModel{

    const TABLE_NAME = 'articles_categorys';

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
     * 删除文章和分类的关联记录（物理删除）
     * @param $aid
     * @return bool
     * @throws \Exception
     */
    public function delete_record($aid){
        $aid = intval($aid);
        if($aid <= 0){
            throw new \Exception('参数错误');
        }

        $result = $this -> db -> delete($this -> getSource(), "aid = ?", array($aid));
        return $result;
    }
}