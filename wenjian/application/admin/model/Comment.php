<?php

namespace app\admin\model;

/**
 * 配置模型
 */
class Comment extends \think\Model {

    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    
    

    public function getAvarageScore($mert_id) {
        $map = [
            'mert_id' => $mert_id,
            'status' => 1
        ];
        $totalScore = $this->where($map)->sum('score');
        $num = $this->where($map)->count();
        if($num<=0){
            return 0;
        }
        $average = round($totalScore / $num, 1);
        return $average;
    }


}
