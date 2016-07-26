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
     * 获取站点基本设置的配置
     * @return array
     */
    public function get_base_options(){
        $array = array();
        $key = "'site_name', 'site_url', 'site_description', 'site_keywords'";
        $options = $this -> get_model('OptionsModel') -> get_list($key, array(
            'columns' => 'op_key, op_value',
        ));
        if(is_array($options) && count($options) > 0){
            foreach($options as $ok=>$ov){
                $array[$ov['op_key']] = $ov;
            }
        }
        return $array;
    }

    /**
     * 更新配置数据
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function update(array $data){
        if(count($data) == 0){
            throw new \Exception('暂无配置需要更新');
        }
        foreach($data as $k=>$v){
            $this -> get_model('OptionsModel') -> update_record(array(
                "op_value" => $v
            ), "{$k}");
        }
        return true;
    }
}
