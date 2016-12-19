<?php

/**
 * 文章
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Frontend\Controllers;

use \Marser\App\Frontend\Controllers\BaseController;

class ArticlesController extends BaseController{

    /**
     * 文章详情
     */
    public function detailAction(){
        try{
            /** 获取文章数据 */
            $aid = intval($this->dispatcher->getParam('aid', 'trim'));
            $article = $this -> get_repository('Articles') -> detail($aid);
            if(!is_array($article) || count($article) == 0){
                throw new \Exception('文章不存在', 404);
            }
            /** 根据aid获取分类 */
            $categorys = $this -> get_repository('Articles') -> get_categorys_by_aids([$aid]);
            foreach($categorys as $ck=>$cv){
                $article['categorys'][] = array(
                    'cid' => $cv->cid,
                    'category_name' => $cv->category_name,
                );
            }
            /** 根据aid获取标签 */
            $tags = $this -> get_repository('Articles') -> get_tags_by_aids([$aid]);
            foreach($tags as $tk=>$tv){
                $article['tags'][] = array(
                    'tid' => $tv->tid,
                    'tag_name' => $tv->tag_name,
                );
            }

            $this -> view -> setVars(array(
                'article' => $article,

            ));
            $this -> view -> pick('articles/detail');
        }catch(\Exception $e){
            $this -> write_exception_log($e);

            return $this -> response -> redirect('index/notfound');
        }
    }

}