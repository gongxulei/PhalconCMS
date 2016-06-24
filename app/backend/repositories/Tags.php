<?php

/**
 * 标签业务仓库
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

use \Marser\App\Backend\Repositories\BaseRepository;

class Tags extends BaseRepository{

    public function __construct(){
        parent::__construct();
    }

    /**
     * 标签列表
     * @param int $status
     * @param array $ext
     * @return array
     * @throws \Exception
     */
    public function get_list($status=1, array $ext=array()){
        $tagsList = $this -> get_model('TagsModel') -> get_list($status, $ext);
        return $tagsList;
    }

    /**
     * 获取标签数据
     * @param $tid
     * @return array
     * @throws \Exception
     */
    public function detail($tid){
        $tag = $this -> get_model('TagsModel') -> detail($tid);
        return $tag;
    }

    /**
     * 保存标签
     * @param array $data
     * @param $tid
     * @return bool|int
     */
    public function save(array $data, $tid){
        $tid = intval($tid);
        if($tid <= 0){
            /** 添加标签 */
            $tid = $this -> insert_record($data);
            return $tid;
        }else{
            $affectedRows = $this -> update_record($data, $tid);
            if(!$affectedRows){
                throw new \Exception('保存标签失败');
            }
            return $affectedRows;
        }
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
     * 标签数据入库
     * @param array $data
     * @return bool|int
     * @throws \Exception
     */
    protected function insert_record(array $data){
        $data = $this -> before_insert($data);
        $tid = $this -> get_model('TagsModel') -> insert_record($data);
        return $tid;
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
     * 更新标签
     * @param array $data
     * @param $tid
     * @return int
     * @throws \Exception
     */
    protected function update_record(array $data, $tid){
        $data = $this -> before_update($data);
        $affectedRows = $this -> get_model('TagsModel') -> update_record($data, $tid);
        return $affectedRows;
    }

}