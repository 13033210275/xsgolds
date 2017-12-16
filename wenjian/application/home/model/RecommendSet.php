<?php

namespace app\home\model;

/**
 * 配置模型
 */
class RecommendSet extends \think\Model{
    protected $updateTime = false;
    protected $create_time = false;
    public function getDatas($id=1, $status = 1)
    {
        $list = $this->field('leftUrl')->where("id = " . $id . " and status = ".$status)->select();
        return $list[0];
    }
}