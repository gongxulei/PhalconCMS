<?php

/**
 * 标签数据仓库
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

use \Marser\App\Backend\Repositories\BaseRepository,
    \Marser\App\Backend\Models\TagsModel;

class Tags extends BaseRepository{

    /**
     * @var TagsModel
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this -> model = new TagsModel();
    }

    /**
     * 标签列表
     * @param int $status
     * @param array $ext
     * @return array
     * @throws \Exception
     */
    public function get_list($status=1, array $ext=array()){
        $tagsList = $this -> model -> get_list($status, $ext);
        return $tagsList;
    }

    /**
     * 标签数据入库
     * @param array $data
     * @return bool|int
     * @throws \Exception
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
        $tid = $this -> model -> insert_record($data);
        return $tid;
    }

    /**
     *
     * @param array $data
     * @param $tid
     * @return int
     * @throws \Exception
     */
    public function update(array $data, $tid){
        if(!isset($data['modify_by']) || empty($data['modify_by'])){
            $data['modify_by'] = $this -> _di -> get('session') -> get('user')['uid'];
        }
        if(!isset($data['modify_time']) || empty($data['modify_time'])){
            $data['modify_time'] = time();
        }
        $affectedRows = $this -> model -> update_record($data, $tid);
        return $affectedRows;
    }

}