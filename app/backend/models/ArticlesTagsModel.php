<?php

/**
 * 文章-标签关联模型
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Models;

use \Marser\App\Backend\Models\BaseModel,
    \Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;

class ArticlesTagsModel extends BaseModel{

    const TABLE_NAME = 'articles_tags';

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
        $tid = $this -> db -> lastInsertId();
        return $tid;
    }

    /**
     * 删除标签关联关系（物理删除）
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

    /**
     * 根据文章ID获取标签数据
     * @param $aid
     * @return mixed
     * @throws \Exception
     */
    public function get_tag_name_by_aid($aid){
        $aid = intval($aid);
        if($aid <= 0){
            throw new \Exception('参数错误');
        }
        $builder = $this->getModelsManager()->createBuilder();
        $builder->columns(array(
            'atags.aid', 't.tid', 't.tag_name'
        ));
        $builder->from(array('atags' => __CLASS__));
        $builder->addFrom(__NAMESPACE__ . '\\TagsModel', 't');
        $result = $builder->where("atags.aid = :aid:", array('aid' => $aid))
            ->andWhere("atags.tid = t.tid")
            ->getQuery()
            ->execute();
        if(!$result){
            throw new \Exception('获取标签数据失败');
        }
        return $result;
    }
}
