<?php

namespace app\home\model;

/**
 * 配置模型
 */
class Document extends \think\Model{
    
    protected $autoWriteTimestamp = true;
    
    
    public function picture() {
        return $this->hasOne('Picture','id','img')->field('id,path');
    }
    
    
}