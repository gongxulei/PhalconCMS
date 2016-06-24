<?php

/**
 * 站点配置业务仓库
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

use \Marser\App\Backend\Repositories\BaseRepository;

class Options extends BaseRepository{

    public function __construct(){
        parent::__construct();
    }

    /**
     * 配置列表
     * @param $opkey
     * @param array $ext
     * @return mixed
     */
    public function get_list($opkey, array $ext=array()){
        $optionsList = $this -> get_model('OptionsModel') -> get_list($opkey, $ext);
        return $optionsList;
    }

    /**
     * 保存配置数据
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function save(array $data){
        if(count($data) == 0 || empty($data)){
            throw new \Exception('暂无配置需要更新');
        }
        foreach($data as $k=>$v){
            $this -> update_record(array(
                "op_value" => $v
            ), "{$k}");
        }
        return true;
    }

    /**
     * 更新配置
     * @param array $data
     * @param $opkey
     * @return int
     * @throws \Exception
     */
    public function update_record(array $data, $opkey){
        if(!isset($data['modify_time']) || empty($data['modify_time'])){
            $data['modify_time'] = time();
        }
        $affectedRows = $this -> get_model('OptionsModel') -> update_record($data, $opkey);
        return $affectedRows;
    }
}
