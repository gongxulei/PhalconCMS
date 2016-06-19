<?php

/**
 *
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

use \Marser\App\Backend\Repositories\BaseRepository;

class Articles extends BaseRepository{

    /**
     * 模型对象容器
     * @var array
     */
    protected $_models = array();

    public function __construct(){
        parent::__construct();
    }

    /**
     * 自动实例化依赖模型
     * @param $modelName
     * @return mixed
     */
    public function __get($modelName){
        $modelName = ucfirst($modelName);
        $namespace = $this -> _di -> get('systemConfig') -> get('app', 'namespace');
        $modelName = "\\{$namespace}\\App\\Backend\\Models\\{$modelName}";
        if(!class_exists($modelName)){
            throw new \Exception("{$modelName}不存在");
        }
        if(!isset($this -> _models[$modelName])){
            $this -> _models[$modelName] = new $modelName();
        }
        return $this -> _models[$modelName];
    }

    /**
     * 添加文章数据
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
        try {
            $db = $this -> _di -> get('db');
            /** 事务开始 */
            $db -> begin();
            /** 文章基本数据入库 */
            $aid = $this -> add_article($data);
            /** 文章内容数据入库 */
            $cid = $this -> add_article_content($aid, $data['content']);
            /** 关联分类数据入库 */
            $this -> add_article_categorys($aid, $data['cid']);
            /** 标签数据入库 */
            $tagidArray = $this -> get_tagid_list($data['tag_name'], $data);
            $this -> add_article_tags($aid, $tagidArray);
            /** 提交事务 */
            $db -> commit();
        }catch(\Exception $e){
            /** 回滚事务 */
            $db -> rollback();

            throw new \Exception($e -> getMessage(), intval($e -> getCode()));
        }
    }

    /**
     * 更新文章数据
     * @param array $data
     * @param $aid
     * @throws \Exception
     */
    public function edit(array $data, $aid){
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
        try{
            $db = $this -> _di -> get('db');
            /** 事务开始 */
            $db -> begin();
            /** 更新文章基本数据 */
            $this -> update_article($data, $aid);
            /** 更新文章内容数据 */
            $this -> update_article_content($data['content'], $aid);
            /** 更新文章关联的分类数据 */
            $this -> delete_article_categorys($aid);
            $this -> add_article_categorys($aid, $data['cid']);
            /** 更新文章关联的标签数据 */
            $this -> delete_article_tags($aid);
            $tagidArray = $this -> get_tagid_list($data['tag_name'], $data);
            $this -> add_article_tags($aid, $tagidArray);
            /** 提交事务 */
            $db -> commit();
        }catch(\Exception $e){
            /** 回滚事务 */
            $db -> rollback();

            throw new \Exception($e -> getMessage(), intval($e -> getCode()));
        }
    }

    /**
     * 文章数据入库
     * @param array $data
     * @return bool|int
     * @throws \Exception
     */
    protected function add_article(array $data){
        $aid = $this -> articlesModel -> add(array(
            'title' => $data['title'],
            'head_image' => $data['head_image'],
            'introduce' => $data['introduce'],
            'status' => $data['status'],
            'create_by' => $data['create_by'],
            'create_time' => $data['create_time'],
            'modify_by' => $data['modify_by'],
            'modify_time' => $data['modify_time'],
        ));
        return $aid;
    }

    /**
     * 更新文章数据
     * @param array $data
     * @param $aid
     * @return int
     * @throws \Exception
     */
    protected function update_article(array $data, $aid){
        $affectedRows = $this -> articlesModel -> update_record(array(
            'title' => $data['title'],
            'head_image' => $data['head_image'],
            'introduce' => $data['introduce'],
            'status' => $data['status'],
            'modify_by' => $data['modify_by'],
            'modify_time' => $data['modify_time'],
        ), $aid);
        if($affectedRows == 0){
            throw new \Exception('更新失败');
        }
        return $affectedRows;
    }

    /**
     * 文章内容数据入库
     * @param $aid
     * @param string $content
     * @return bool|int
     * @throws \Exception
     */
    protected function add_article_content($aid, $content){
        $aid = intval($aid);
        if($aid <= 0){
            throw new \Exception('参数错误');
        }
        $cid = $this -> contentsModel -> add(array(
            'relateid' => $aid,
            'content' => $content,
        ));
        return $cid;
    }

    /**
     * 更新文章内容数据
     * @param $content
     * @param $aid
     * @return int
     * @throws \Exception
     */
    protected function update_article_content($content, $aid){
        $aid = intval($aid);
        if($aid <= 0){
            throw new \Exception('参数错误');
        }
        $affectedRows = $this -> contentsModel -> update_record(array(
            'content' => $content,
        ), $aid);
        return $affectedRows;
    }

    /**
     * 文章所属分类数据入库
     * @param $aid
     * @param string $cid
     * @throws \Exception
     */
    protected function add_article_categorys($aid, $cid){
        $aid = intval($aid);
        if($aid <= 0){
            throw new \Exception('参数错误');
        }
        $cidArray = explode(',', $cid);
        $cidArray = array_map('trim', $cidArray);
        $cidArray = array_map('intval', $cidArray);
        $cidArray = array_filter($cidArray);
        $cidArray = array_unique($cidArray);
        if(!is_array($cidArray) || count($cidArray) == 0){
            throw new \Exception('请选择文章所属分类');
        }
        foreach($cidArray as $ck=>$cv){
            $this -> articlesCategorysModel -> add(array(
                'aid' => $aid,
                'cid' => $cv
            ));
        }
        return true;
    }

    /**
     * 删除文章关联的分类数据
     * @param $aid
     * @return bool
     * @throws \Exception
     */
    protected function delete_article_categorys($aid){
        $success = $this -> articlesCategorysModel -> delete_record($aid);
        if(!$success){
            throw new \Exception('更新文章关联的分类数据失败');
        }
        return $success;
    }

    /**
     * 根据tagname获取tagid列表
     * @param $tagName 多个标签名以“,”分隔
     * @param array $data
     * @return array
     * @throws \Exception
     */
    protected function get_tagid_list($tagName, array $data=array()){
        $tagidArray = array();
        $tagNameArray = explode(',', $tagName);
        $tagNameArray = array_map('trim', $tagNameArray);
        $tagNameArray = array_filter($tagNameArray);
        $tagNameArray = array_unique($tagNameArray);
        if(is_array($tagNameArray) && count($tagNameArray) > 0){
            foreach($tagNameArray as $tk=>$tv){
                $tid = $this -> tagsModel -> get_tid_by_tagname($tv);
                if($tid){//标签存在
                    $tagidArray[] = $tid;
                }else{//标签不存在，则添加标签
                    $tid = $this -> tagsModel -> add(array(
                        'tag_name' => $tv,
                        'create_by' => $data['create_by'],
                        'create_time' => $data['create_time'],
                        'modify_by' => $data['modify_by'],
                        'modify_time' => $data['modify_time'],
                    ));
                    $tagidArray[] = $tid;
                }
            }
        }
        return $tagidArray;
    }

    /**
     * 文章关联的标签数据入库
     * @param $aid
     * @param array $tagidArray
     * @return bool
     * @throws \Exception
     */
    protected function add_article_tags($aid, array $tagidArray){
        $aid = intval($aid);
        if($aid <= 0 || !is_array($tagidArray) || count($tagidArray) == 0){
            return false;
        }
        foreach($tagidArray as $tk=>$tv){
            $this -> articlesTagsModel -> add(array(
                'aid' => $aid,
                'tid' => $tv,
            ));
        }
        return true;
    }

    /**
     * 删除文章关联的标签数据
     * @param $aid
     * @return bool
     * @throws \Exception
     */
    protected function delete_article_tags($aid){
        $success = $this -> articlesTagsModel -> delete_record($aid);
        if(!$success){
            throw new \Exception('更新文章关联的标签数据失败');
        }
        return $success;
    }

}