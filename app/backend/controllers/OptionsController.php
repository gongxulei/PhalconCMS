<?php

/**
 *
 * @category PhalconCMS
 * @copyright Copyright (c) 2016 PhalconCMS team (http://www.marser.cn)
 * @license GNU General Public License 2.0
 * @link www.marser.cn
 */

namespace Marser\App\Backend\Controllers;
use \Marser\App\Backend\Controllers\BaseController;

class OptionsController extends BaseController{

    /**
     * 站点基础配置
     */
    public function baseAction(){
        $this -> view -> pick('options/base');
    }

    /**
     * 站点基础配置变更
     */
    public function updatebaseAction(){
        try{
            $siteName = $this -> request -> get('siteName', 'trim');
            $siteUrl = $this -> request -> get('siteUrl', 'trim');
            $description = $this -> request -> get('description', 'trim');
            $keywords = $this -> request -> get('keywords', 'trim');
            $timezone = $this -> request -> get('timezone', 'trim');
            /** 添加验证规则 */
            $this -> validator -> add_rule('siteName', 'required', '请填写站点名称')
                -> add_rule('siteName', 'chinese_alpha_numeric_dash', '站点名称由中英文字符、数字和中下划线组成');
            $this -> validator -> add_rule('siteUrl', 'required', '请填写一个合法的URL地址')
                -> add_rule('siteUrl', 'url', '站点网址格式错误');
            $this -> validator -> add_rule('description', 'xss_check', '请不要在站点描述中使用特殊字符');
            $this -> validator -> add_rule('keywords', 'xss_check', '请不要在关键词中使用特殊字符');

            $data = array(
                'site_name' => $siteName,
                'site_url' => $siteUrl,
                'site_description' => $description,
                'site_keywords' => $keywords,
                'site_timezone' => $timezone
            );
            foreach($data as $k=>$v){

            }
        }catch(\Exception $e){
            $this -> write_exception_log($e);

            $code = !empty($e -> getCode()) ? $e -> getCode() : 500;
            $this -> ajax_return($e -> getMessage(), $code);
        }
    }
}
