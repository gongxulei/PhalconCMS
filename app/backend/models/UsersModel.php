<?php

/**
 *
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Models;
use \Marser\App\Backend\Models\BaseModel;

class UsersModel extends BaseModel{

    const TABLE_NAME = 'users';

    public function initialize(){
        parent::initialize();
        $this -> set_table_source(self::TABLE_NAME);
    }

    /**
     * 获取用户详细数据
     * @param $username
     * @param array $ext
     * @return \Phalcon\Mvc\Model
     * @throws \Exception
     */
    public function user_detail($username, array $ext=array()){
        if(empty($username)){
            throw new \Exception('参数错误');
        }
        $params = array(
            'conditions' => 'username=:username:',
            'bind' => [
                'username' => $username,
            ],
        );
        if(isset($ext['columns']) && !empty($ext['columns'])){
            $params['columns'] = $ext['columns'];
        }
        $result = $this -> findFirst($params);
        return $result;
    }

}