<?php

namespace app\admin\model;
use app\admin\model\Member;
/**
 * 配置模型
 */
class RecommendSet extends \think\Model{

    protected $autoWriteTimestamp = false;
    protected $updateTime = false;
    public function insertData($id, $leftUrl, $status)
    {
        $data = [];
        $data['leftUrl'] = $leftUrl;
        $data['id'] = $id;
        $data['status'] = $status;
        \think\Db::name('RecommendSet')->insert($data);
    }
    public function checkCheckStatus($id, $status)
    {
        $data = [];
        $data['status'] = $status;
        $result = $this->where("id = " . $id)->update($data);
    }
    public function updateDatas($id, $leftUrl, $status=1)
    {
        $data = [];
        $data['leftUrl'] = $leftUrl;
        $data['status'] = $status;
        $result = $this->where("id = " . $id)->update($data);
        return $result == 1;
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
        $list = $this->field('leftUrl, status')->where("id = " . $id)->select();
        return $list[0];
    }
    public function deleteById($id)
    {
        $result = $this->where("id=".$id )->delete();
    }
    public function getDatas($id=1, $status = 1)
    {
        $list = $this->field('leftUrl')->where("id = " . $id . " and status = ".$status)->select();
        return $list[0];
    }
}
