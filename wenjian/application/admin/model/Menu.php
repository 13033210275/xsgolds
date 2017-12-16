<?php
 
namespace app\admin\model;

use think\Model;

/**
 * 菜单模型
 */
class Menu extends Model {

    
        
    const rule_url = 1;
    const rule_main = 2;
    
    protected $resultSetType = 'collection';
    
    
    protected $autoWriteTimestamp = false;
    protected $auto = ['title'];
    // 新增
    protected $insert = ['status' => 1];

    //属性修改器
    protected function setTitleAttr($value, $data) {
        return htmlspecialchars($value);
    }
    
    
    

}
