<?php

namespace app\admin\model;
use app\admin\model\Member;
/**
 * 配置模型
 */
class RecommendEnter extends \think\Model{
    protected $updateTime = false;
    protected $create_time = false;
    public function getDatas($id=1, $status = 1, $isNormal = true)
    {
        $list = $this->field('leftName, leftUrl, rightName, rightUrl')->where("id = " . $id . " and status = ".$status)->select();
        if ($list[0])
        {
            return $list[0];
        }
        elseif($isNormal)
        {
            $list = $this->field('leftName, leftUrl, rightName, rightUrl')->where("id = 1")->select();
            return $list[0];
        }
        else
        {
            return $list[0];
        }
    }
    public function getAllData()
    {
        $list = $this->field('id, leftUrl as link, status')->select();
        foreach ($list as $data){
            $datas = Member::field("id, mobile")->where("id = ".$data["id"])->find();
            $data["mobile"] = $datas->mobile;
        }
        return $list;
    }
    public function getDatasNoStatus($id=1)
    {
        $list = $this->field('leftName, leftUrl, rightName, rightUrl, status')->where("id = " . $id)->select();
        return $list[0];
    }
    public function updateDatas($id, $leftName, $leftUrl, $rightName, $rightUrl, $status=1)
    {
        $data = [];
        $data['leftName'] = $leftName;
        $data['leftUrl'] = $leftUrl;
        $data['rightName'] = $rightName;
        $data['rightUrl'] = $rightUrl;
        $data['id'] = $id;
        $data['status'] = $status;
        $result = $this->where("id = " . $id)->update($data);
        return $result == 1;
    }
    public function insertData($id, $leftName, $leftUrl, $rightName, $rightUrl, $status=1)
    {
        $data = [];
        $data['leftName'] = $leftName;
        $data['leftUrl'] = $leftUrl;
        $data['rightName'] = $rightName;
        $data['rightUrl'] = $rightUrl;
        $data['id'] = $id;
        $data['status'] = $status;
        \think\Db::name('RecommendEnter')->insert($data);
    }
    public function checkCheckStatus($id, $status)
    {
        $data = [];
        $data['status'] = $status;
        $result = $this->where("id = " . $id)->update($data);
    }
    public function deleteById($id)
    {
        $result = $this->where("id=".$id )->delete();
    }
}