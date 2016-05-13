<?php

/**
 * Phalcon模型扩展
 *
 */

namespace Marser\App\Core;

class PhalBaseModel extends \Phalcon\Mvc\Model{

    /**
     * 设置表（补上表前缀）
     * @param string $tableName
     * @author Marser
     */
    public function set_table_source($tableName){
        $prefix = $this -> getDI() -> get('systemConfig') -> get('database', 'prefix');
        $this -> setSource($prefix.$tableName);
    }
}