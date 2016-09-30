<?php

/**
 * 文章业务仓库
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

use \Marser\App\Backend\Repositories\BaseRepository;

class Articles extends BaseRepository{

    public function __construct(){
        parent::__construct();
    }

    public function get_list(){

    }

    /**
     * 保存文章
     * @param array $data
     * @param null $aid
     * @throws \Exception
     */
    public function save(array $data, $aid = null){
        empty($data['create_by']) && $data['create_by'] = $this -> getDI() -> get('session') -> get('user')['uid'];
        empty($data['create_time']) && $data['create_time'] = time();
        empty($data['modify_by']) && $data['modify_by'] = $this -> getDI() -> get('session') -> get('user')['uid'];
        empty($data['modify_time']) && $data['modify_time'] = time();

        $aid = intval($aid);
        if(empty($aid)){
            /** 新增文章 */
            $this -> create($data);
        }else{
            /** 更新文章 */
            $this -> update($data, $aid);
        }
    }

    /**
     * 新增文章
     * @param array $data
     * @throws \Exception
     */
    protected function create(array $data){
        try {
            $db = $this -> getDI() -> get('db');
            /** 事务开始 */
            $db -> begin();
            /** 文章基本数据入库 */
            $aid = $this -> create_article($data);
            /** 文章内容数据入库 */
            $cid = $this -> create_article_content($aid, $data['content']);
            /** 关联分类数据入库 */
            $this -> create_article_categorys($aid, $data['cid']);
            /** 标签数据入库 */
            $tagidArray = $this -> get_tagid_list($data['tag_name'], $data);
            $this -> create_article_tags($aid, $tagidArray);
            /** 提交事务 */
            $db -> commit();
        }catch(\Exception $e){
            /** 回滚事务 */
            $db -> rollback();

            throw new \Exception($e -> getMessage(), intval($e -> getCode()));
        }
    }

    /**
     * 更新文章
     * @param array $data
     * @param $aid
     * @throws \Exception
     */
    protected function update(array $data, $aid){
        try{
            $db = $this -> getDI() -> get('db');
            /** 事务开始 */
            $db -> begin();
            /** 更新文章基本数据 */
            $this -> update_article($data, $aid);
            /** 更新文章内容数据 */
            $this -> update_article_content($data['content'], $aid);
            /** 更新文章关联的分类数据 */
            $this -> delete_article_categorys($aid);
            $this -> create_article_categorys($aid, $data['cid']);
            /** 更新文章关联的标签数据 */
            $this -> delete_article_tags($aid);
            $tagidArray = $this -> get_tagid_list($data['tag_name'], $data);
            $this -> create_article_tags($aid, $tagidArray);
            /** 提交事务 */
            $db -> commit();
        }catch(\Exception $e){
            /** 回滚事务 */
            $db -> rollback();

            throw new \Exception($e -> getMessage(), intval($e -> getCode()));
        }
    }

    /**
     * 统计数量
     * @return mixed
     */
    public function get_count(){
        $count = $this -> get_model('ArticlesModel') -> get_count();
        return $count;
    }

    /**
     * 文章数据入库
     * @param array $data
     * @return bool|int
     * @throws \Exception
     */
    protected function create_article(array $data){
        $aid = $this -> get_model('articlesModel') -> insert_record(array(
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
        $affectedRows = $this -> get_model('articlesModel') -> update_record(array(
            'title' => $data['title'],
            'head_image' => $data['head_image'],
            'introduce' => $data['introduce'],
            'status' => $data['status'],
            'modify_by' => $data['modify_by'],
            'modify_time' => $data['modify_time'],
        ), $aid);
        return $affectedRows;
    }

    /**
     * 文章内容数据入库
     * @param $aid
     * @param string $content
     * @return bool|int
     * @throws \Exception
     */
    protected function create_article_content($aid, $content){
        $aid = intval($aid);
        if($aid <= 0){
            throw new \Exception('参数错误');
        }
        $cid = $this -> get_model('contentsModel') -> insert_record(array(
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
        $affectedRows = $this -> get_model('contentsModel') -> update_record(array(
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
    protected function create_article_categorys($aid, $cid){
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
        $articlesCategorysModel = $this -> get_model('articlesCategorysModel');
        foreach($cidArray as $ck=>$cv){
            $articlesCategorysModel -> insert_record(array(
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
        $result = $this -> get_model('articlesCategorysModel') -> delete_record($aid);
        if(!$result){
            throw new \Exception('更新文章关联的分类数据失败');
        }
        return $result;
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
            $tagsModel = $this -> get_model('tagsModel');
            foreach($tagNameArray as $tk=>$tv){
                $tid = $tagsModel -> get_tid_by_tagname($tv);
                if($tid){//标签存在
                    $tagidArray[] = $tid;
                }else{//标签不存在，则添加标签
                    $tid = $tagsModel -> insert_record(array(
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
    protected function create_article_tags($aid, array $tagidArray){
        $aid = intval($aid);
        if($aid <= 0 || !is_array($tagidArray) || count($tagidArray) == 0){
            return false;
        }
        $articlesTagsModel = $this -> get_model('articlesTagsModel');
        foreach($tagidArray as $tk=>$tv){
            $articlesTagsModel -> insert_record(array(
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
        $result = $this -> get_model('articlesTagsModel') -> delete_record($aid);
        if(!$result){
            throw new \Exception('更新文章关联的标签数据失败');
        }
        return $result;
    }

}