<?php

namespace app\home\model;

use think\Model;

class Bjx extends Model {

     
    public function getRandName(){
        $id = rand(1, 355);
        return $this->field('name')->find($id);
    }

}
