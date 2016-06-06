<?php

/**
 *
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

use \Marser\App\Backend\Repositories\BaseRepository,
    \Marser\App\Backend\Models\ArticlesModel;

class Articles extends BaseRepository{

    /**
     * model对象
     * @var ArticlesModel
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this -> model = new ArticlesModel();
    }

    /**
     * 插入文章
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
        /** 文章数据入库 */
        /** 分类数据入库 */
        /** 标签数据入库 */

        $aid = $this -> add($data);
        return $aid;
    }

}