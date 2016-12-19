<?php

/**
 * 文章-标签关联模型
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Frontend\Models;

use \Marser\App\Frontend\Models\BaseModel,
    \Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;

class ArticlesTagsModel extends BaseModel{

    const TABLE_NAME = 'articles_tags';

    public function initialize(){
        parent::initialize();
        $this -> set_table_source(self::TABLE_NAME);
    }

    /**
     * 根据文章ID获取标签数据
     * @param $aid
     * @return mixed
     * @throws \Exception
     */
    public function get_tags_by_aids(array $aids){
        if(!is_array($aids) || count($aids) == 0){
            throw new \Exception('参数错误');
        }
        $builder = $this->getModelsManager()->createBuilder();
        $builder->columns(array(
            'atags.aid', 't.tid', 't.tag_name'
        ));
        $builder->from(array('atags' => __CLASS__));
        $builder->addFrom(__NAMESPACE__ . '\\TagsModel', 't');
        $result = $builder->where("atags.aid IN ({aid:array})", array('aid' => $aids))
            ->andWhere("atags.tid = t.tid")
            ->andWhere("t.status = 1")
            ->getQuery()
            ->execute();
        if(!$result){
            throw new \Exception('获取文章关联的标签数据失败');
        }
        return $result;
    }
}
