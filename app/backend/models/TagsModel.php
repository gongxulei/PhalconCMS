<?php

/**
 * 标签模型
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
     * @param array $ext
     * @return array
     * @throws \Exception
     */
    public function get_list(array $ext=array()){
        $result = $this -> find();
        if(!$result){
            throw new \Exception('查询数据失败');
        }
        $tagsList = $result -> toArray();
        return $tagsList;
    }

    /**
     * 统计数量
     * @param int $status
     * @return mixed
     */
    public function get_count(){
        $count = $this -> count();
        return $count;
    }

    /**
     * 获取标签数据
     * @param $tid
     * @return array
     * @throws \Exception
     */
    public function detail($tid){
        $tag = array();
        $tid = intval($tid);
        if($tid <= 0){
            throw new \Exception('参数错误');
        }
        $result = $this -> findFirst(array(
            'conditions' => 'tid = :tid:',
            'bind' => array(
                'tid' => $tid,
            ),
        ));
        if($result){
            $tag = $result -> toArray();
        }
        return $tag;
    }

    /**
     * 	Is executed before the fields are validated for not nulls/empty strings
     *  or foreign keys when an insertion operation is being made
     */
    public function beforeValidationOnCreate(){
        $this -> create_by = $this->_user['uid'];
        $this -> create_time = date('Y-m-d H:i:s');
        $this -> modify_by = $this->_user['uid'];
        $this -> modify_time = date('Y-m-d H:i:s');
    }

    /**
     * 标签数据入库
     * @param array $data
     * @return bool|int
     * @throws \Exception
     */
    public function insert_record(array $data){
        if(count($data) == 0){
            throw new \Exception('参数错误');
        }
        $result = $this -> create($data);
        if(!$result){
            throw new \Exception(implode(',', $this -> getMessages()));
        }
        $tid = $this -> tid;
        return $tid;
    }

    /**
     * 自定义的update事件
     * @param array $data
     * @return array
     */
    protected function before_update(array $data){
        $data['modify_by'] = $this->_user['uid'];
        $data['modify_time'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * 标签更新
     * @param array $data
     * @param $tid
     * @return int
     * @throws \Exception
     */
    public function update_record(array $data, $tid){
        $tid = intval($tid);
        $data = $this -> before_update($data);
        if(count($data) == 0 || $tid <= 0){
            throw new \Exception('参数错误');
        }
        $this -> tid = $tid;
        $result = $this -> iupdate($data);
        if(!$result){
            throw new \Exception(implode(',', $this -> getMessages()));
        }
        $affectedRows = $this -> db -> affectedRows();
        return $affectedRows;
    }

    /**
     * 根据tagname获取tid
     * @param $tagname
     * @return int
     * @throws \Exception
     */
    public function get_tid_by_tagname($tagname){
        if(empty($tagname)){
            throw new \Exception('参数错误');
        }
        $params = array(
            'columns' => 'tid',
            'conditions' => 'tag_name = :tagname:',
            'bind' => array(
                'tagname' => "{$tagname}",
            ),
        );
        $result = $this -> findFirst($params);
        if($result){
            $tid = $result -> tid;
            $tid = intval($tid);
            if($tid > 0){
                return $tid;
            }
        }
        return false;
    }

    /**
     * 标签是否存在
     * @param null $tagName
     * @param null $slug
     * @param null $tid
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     * @throws \Exception
     */
    public function tag_is_exist($tagName=null, $slug=null, $tid=null){
        if(empty($tagName) && empty($slug)){
            throw new \Exception('参数错误');
        }
        $params = array();
        if(!empty($tagName) && !empty($slug)){
            $params['conditions'] = " (tag_name = :tagName: OR slug = :slug:) ";
            $params['bind']['tagName'] = $tagName;
            $params['bind']['slug'] = $slug;
        }else if(!empty($tagName)){
            $params['conditions'] = " tag_name = :tagName: ";
            $params['bind']['tagName'] = $tagName;
        }else if(!empty($slug)){
            $params['conditions'] = " slug = :slug: ";
            $params['bind']['slug'] = $slug;
        }
        $tid = intval($tid);
        $tid > 0 && $params['conditions'] .= " AND tid != {$tid} ";

        $result = $this -> find($params);
        return $result;
    }
}