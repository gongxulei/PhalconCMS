<?php

/**
 * 用户数据仓库
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

use \Marser\App\Backend\Repositories\BaseRepository,
    \Marser\App\Backend\Models\UsersModel;

class Users extends BaseRepository{

    /**
     * model对象
     * @var UsersModel
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this -> model = new UsersModel();
    }

    /**
     * 用户数据
     * @param string $username
     * @param array $ext
     * @return \Phalcon\Mvc\Model
     * @throws \Exception
     */
    public function detail($username, array $ext=array()){
        $user = $this -> model -> detail($username, $ext);
        return $user;
    }

    /**
     * 更新用户数据
     * @param array $data
     * @param $uid
     * @return int
     * @throws \Exception
     */
    public function update(array $data, $uid){
        if(!isset($data['modify_time']) || empty($data['modify_time'])){
            $data['modify_time'] = time();
        }
        $affectedRows = $this -> model -> update_user($data, $uid);
        return $affectedRows;
    }
}