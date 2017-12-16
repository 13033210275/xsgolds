<?php

namespace app\home\model;

/**
 * 配置模型
 */
class RecommendEnter extends \think\Model{
    protected $updateTime = false;
    protected $create_time = false;
    public function getDatas($id=1, $status = 1)
    {
        $list = $this->field('leftName, leftUrl, rightName, rightUrl')->where("id = " . $id . " and status = ".$status)->select();
        if ($list[0])
        {
            return $list[0];
        }
        $list = $this->field('leftName, leftUrl, rightName, rightUrl')->where("id = 1")->select();
        return $list[0];
    }
    public function getDatasNoStatus($id=1)
    {
        $list = $this->field('leftName, leftUrl, rightName, rightUrl, status')->where("id = " . $id)->select();
        return $list[0];
    }
}