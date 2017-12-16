<?php

namespace app\home\model;

use think\Model;

class Region extends Model {

    public function getRandCity(){
        $id = rand(1, 369);
        return $this->field('name')->find($id);
    }

}
