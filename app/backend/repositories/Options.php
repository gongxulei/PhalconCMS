<?php

/**
 *
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Repositories;

use \Marser\App\Backend\Repositories\BaseRepository,
    \Marser\App\Backend\Models\OptionsModel;

class Options extends BaseRepository{

    /**
     * model对象
     * @var OptionsModel
     */
    protected $model;

    public function __construct(){
        parent::__construct();
        $this -> model = new OptionsModel();
    }

    /**
     * 配置列表
     * @param $opkey
     * @param array $ext
     * @return mixed
     */
    public function get_list($opkey, array $ext=array()){
        $optionsList = $this -> model -> get_list($opkey, $ext);
        return $optionsList;
    }

    /**
     * 更新配置
     * @param array $data
     * @param $opkey
     * @return int
     * @throws \Exception
     */
    public function update(array $data, $opkey){
        if(!isset($data['modify_time']) || empty($data['modify_time'])){
            $data['modify_time'] = time();
        }
        $affectedRows = $this -> model -> update_options($data, $opkey);
        return $affectedRows;
    }
}
