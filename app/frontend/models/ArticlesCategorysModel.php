<?php

/**
 * 文章-分类关联模型
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Frontend\Models;

use \Marser\App\Frontend\Models\BaseModel;

class ArticlesCategorysModel extends BaseModel{

    const TABLE_NAME = 'articles_categorys';

    public function initialize(){
        parent::initialize();
        $this->set_table_source(self::TABLE_NAME);
    }

    /**
     * 获取文章所属分类
     * @param array $aids
     * @return mixed
     * @throws \Exception
     */
    public function get_categorys_by_aids(array $aids){
        if(!is_array($aids) || count($aids) == 0){
            throw new \Exception('参数错误');
        }
        $builder = $this->getModelsManager()->createBuilder();
        $builder->from(array('ac' => __CLASS__));
        $builder->addFrom(__NAMESPACE__.'\CategorysModel', 'c');
        $builder->columns(array(
            'ac.aid', 'c.cid', 'c.category_name'
        ));
        $result = $builder->where('ac.aid IN ({aid:array})', array('aid'=>$aids))
            ->andWhere('ac.cid = c.cid')
            ->andWhere('c.status = :status:', array('status' => 1))
            ->getQuery()
            ->execute();
        if(!$result){
            throw new \Exception('获取文章所属分类失败');
        }
        return $result;
    }
}