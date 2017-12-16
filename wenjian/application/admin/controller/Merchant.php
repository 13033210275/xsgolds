<?php

namespace app\admin\controller;
use app\admin\model;
use app\admin\model\Member;

class Merchant extends Admin {


    //ok
    public function index() {
        $wd = input('wd');
        $type = input('type/d',0);
        $cate_id = input('cate_id/d',0);
        $is_index = input('is_index/d',0);
        $status = input('status/d',0);
        if (is_numeric($wd)) {
            $map['id'] = $wd;
        } else {
            $map['name'] = array('like', '%' . (string) $wd . '%');
        }
        if($type>0){
            $map['type'] = $type;
        }
        if($cate_id>0){
            $map['cate_id'] = $cate_id;
        }
        if($is_index>0){
            $map['is_index'] = $is_index-1;
        }
        if($status>0){
            $map['status'] = $status-1;
        }
        $list = model\Merchant::where($map)->order('id desc')->paginate(20,false,[
            'query' => request()->param()
        ]);
        $num=model\Merchant::where($map)->order('id desc')->count('id');

        $page = $list->render();
        $this->assign('_list',$list);
        $this->assign('_num',$num);
        $this->assign('_page',$page);
        $typeSelect = bulid_form('select', [
            0=>'全部',
            1=>'借款',
            2=>'信用卡'
        ], 'type',$type);
        $this->assign('typeSelect',$typeSelect);

        $cateSelect = bulid_form('select', [
            0=>'全部',
            1=>'现金贷',
            2=>'空放',
            3=>'车辆抵押',
            4=>'信用卡'
        ], 'cate_id',$cate_id);
        $this->assign('cateSelect',$cateSelect);


        $indexSelect = bulid_form('select', [
            0=>'全部',
            1=>'否',
            2=>'是'
        ], 'is_index',$is_index);
        $this->assign('indexSelect',$indexSelect);


        $statusSelect = bulid_form('select', [
            0=>'全部',
            1=>'否',
            2=>'是'
        ], 'status',$status);
        $this->assign('statusSelect',$statusSelect);
        $this->assign('meta_title', '合作机构');


        $url = url('export',[
            'wd'=>$wd,
            'type'=>$type,
            'cate_id'=>$cate_id,
            'is_index'=>$is_index,
            'status'=>$status,
        ]);

        $this->assign('exportUrl',$url);


        return $this->fetch();

    }


     //导出(ok)
    public function export(){
        $wd = input('wd');
        $type = input('type/d',0);
        $cate_id = input('cate_id/d',0);
        $is_index = input('is_index/d',0);
        $status = input('status/d',0);
        if (is_numeric($wd)) {
            $map['id'] = $wd;
        } else {
            $map['name'] = array('like', '%' . (string) $wd . '%');
        }
        if($type>0){
            $map['type'] = $type;
        }
        if($cate_id>0){
            $map['cate_id'] = $cate_id;
        }
        if($is_index>0){
            $map['is_index'] = $is_index-1;
        }
        if($status>0){
            $map['status'] = $status-1;
        }
        $list = model\Merchant::where($map)->order('id desc')->select();
        if(!$list){
            $this->error('无数据可以导出');
        }
        $data = [];
        foreach ($list as $vo) {
            $data[]=[
                'id'=>$vo->id,
                'name'=>$vo->name,
                'type'=>$vo->type_text,
                'cate'=>$vo->cate_text,
                'zt_name'=>$vo->zt->zt_name,
                'index'=>$vo->index_text,
                'status'=>$vo->status_text,
                'adv'=>$vo->adv_text,
                'platform'=>$vo->platform
            ];
        }

        $xlsName  = "Shoper";
        $xlsCell  = array(
            array('id','机构编号'),
            array('name','机构名称'),
            array('type','类型'),
            array('cate','归属类别'),
            array('zt_name','专题类别'),
            array('index','首页推荐'),
            array('status','启用'),
            array('adv','广告语'),
            array('platform','归属平台')
        );
        $this->exportExcel($xlsName,$xlsCell,$data);
    }


   //添加机构(ok)
    public function add() {
        if (request()->isPost()) {
            $type = input('type/d',1);
            if($type==2){ //信用卡
                $result = $this->validate($_POST,[
                    'name'=>'require',
                    'logo'=>'require',
                    'img'=>'require',
                    'url'=>'require',
                ],[
                    'name.require'=>'请填写机构名称',
                    'logo.require'=>'请上传机构logo',
                    'img.require'=>'请上传宣传Banner',
                    'url.require'=>'机构申请url必填'
                ]);
            }else{
                $result = $this->validate($_POST,[
                    'name'=>'require',
                    'logo'=>'require',
                    'img'=>'require',
                    'tag'=>'require',
                    'url'=>'require',
                    'min_cash'=>'require',
                    'max_cash'=>'require',
                    'min_date'=>'require',
                    'max_date'=>'require',
                    'min_rate'=>'require',
                    'max_rate'=>'require',
                    'cailiao'=>'require',
                    'adv_txt'=>'require',
                    'contents'=>'require',
                    'youshi'=>'require',
                    'apply_tj'=>'require',
                    'use_area'=>'require',
                    'to_people'=>'require',
                    'get_type'=>'require',
                    'platform'=>'require',
                    'tips'=>'require',

                ],[
                    'name.require'=>'请填写机构名称',
                    'logo.require'=>'请上传机构logo',
                    'img.require'=>'请上传宣传Banner',
                    'tag.require'=>'请选择标签',
                    'url.require'=>'机构申请url必填',
                    'min_cash.require'=>'请填写贷款额度',
                    'max_cash.require'=>'请填写贷款额度',
                    'min_date.require'=>'请填写贷款期限',
                    'max_date.require'=>'请填写贷款期限',
                    'min_rate.require'=>'请填写贷款利率',
                    'max_rate.require'=>'请填写贷款利率',
                    'cailiao.require'=>'请填写贷款材料',
                    'adv_txt.require'=>'请填写产品广告语',
                    'contents.require'=>'请填写产品详情',
                    'youshi.require'=>'请填写产品优势',
                    'apply_tj.require'=>'请填写申请条件',
                    'use_area.require'=>'请选择贷款用途',
                    'to_people.require'=>'请选择贷款面向人群',
                    'get_type.require'=>'请填写到账方式',
                    'platform.require'=>'请填写产品平台',
                    'tips.require'=>'请填写贷款小提示',
                ]);
            }

            if(true !== $result){
               $this->error($result);
            }
            $field = ['use_area','to_people','cailiao','tag'];
            foreach ($field as $vo) {
                $val = input($vo.'/a');
                if($val){
                    $_POST[$vo] = implode(',', $val);
                }
            }
            $mode = new model\Merchant($_POST);
            $flag = $mode->allowField(true)->save();
            if($flag){
                $this->success('添加成功！', url('index'));
            }
            $this->error('添加失败');
        } else {
            $creditZt = [];
            $loanZt = [];
            $rs = model\Zuanti::all();
            if($rs){
                foreach ($rs as $vo) {
                    if($vo['is_credit']){
                        $creditZt[$vo['id']] = $vo['zt_name'];
                    }else{
                        $loanZt[$vo['id']] = $vo['zt_name'];
                    }
                }
            }


            $creditZtSelect = bulid_form('select', $creditZt, 'zt_id');
            $this->assign('creditZtSelect',$creditZtSelect);

            $loanZtSelect = bulid_form('select', $loanZt, 'zt_id');
            $this->assign('loanZtSelect',$loanZtSelect);



            $typeRadio = bulid_form('radio', [1=>'借款',2=>'信用卡'], 'type',1);
            $this->assign('typeRadio',$typeRadio);


            $cateSelect = bulid_form('select', [
                1=>'现金贷',
                2=>'空放',
                3=>'车辆抵押',
            ], 'cate_id',1);
            $this->assign('cateSelect',$cateSelect);

            $creditCateSelect = bulid_form('select', [
                4=>'信用卡',
            ], 'cate_id',1);
            $this->assign('creditCateSelect',$creditCateSelect);

            $indexRadio = bulid_form('radio', ['否','是'], 'is_index',0);
            $this->assign('indexRadio',$indexRadio);


            $objAttr = new model\Label;
            $attr = $objAttr->getAllType();
            $tag = isset($attr[1])?$attr[1]:"";
            $cailiao = isset($attr[2])?$attr[2]:"";
            $uarea =isset($attr[3])?$attr[3]:"";

            $labelCheckbox = bulid_form('checkbox',$tag, 'tag[]','',"",false, true);
            $this->assign('labelCheckbox',$labelCheckbox);

            $fileCheckbox = bulid_form('checkbox',$cailiao, 'cailiao[]','',"",false, true);
            $this->assign('fileCheckbox',$fileCheckbox);

            $madeCheckbox = bulid_form('checkbox', $uarea, 'use_area[]','',"",false, true);
            $this->assign('madeCheckbox',$madeCheckbox);


            $statusRadio = bulid_form('radio', ['禁用','启用'], 'status',1);
            $this->assign('statusRadio',$statusRadio);


            $minUnitSelect = bulid_form('select', ['日','月','年'], 'min_day_unit','',"",false, true);
            $this->assign('minUnitSelect',$minUnitSelect);


            $maxUnitSelect = bulid_form('select', ['日','月','年'], 'max_day_unit','',"",false, true);
            $this->assign('maxUnitSelect',$maxUnitSelect);



            $peopleCheckbox = bulid_form('checkbox', get_jobs(), 'to_people[]','',"",false, true);
            $this->assign('peopleCheckbox',$peopleCheckbox);


            $passRadio = bulid_form('radio', [1=>'人工审核',2=>'自动审核'], 'pass_type',1);
            $this->assign('passRadio',$passRadio);


            $advRadio = bulid_form('radio', [0=>'否',1=>'是'], 'use_adv',0);
            $this->assign('advRadio',$advRadio);

            $getbackRadio = bulid_form('radio', [1=>'等额本息',2=>'一次性还清'], 'back_type',1,'',false,true);
            $this->assign('getbackRadio',$getbackRadio);

            $this->assign('meta_title', '新增机构');
            return $this->fetch();
        }
    }



    //ok
     public function edit() {
        $id = input('id', '');
        if (empty($id)) {
            $this->error('参数不能为空！');
        }
        if (request()->isPost()) {
            $result = $this->validate($_POST,[
                'name'=>'require',
            ],[
                'name.require'=>'请填机构名称',
            ]);
            if(true !== $result){
               $this->error($result);
            }

            $mode = new model\Merchant();

            $field = ['use_area','to_people','cailiao','tag'];
            foreach ($field as $vo) {
                $val = input($vo.'/a');
                if($val){
                    $_POST[$vo] = implode(',', $val);
                }
            }
            $flag = $mode->isUpdate(true)->allowField(true)->save($_POST,['id'=>$id]);
            if($flag){
                $this->success('更新成功！', url('index'));
            }
            $this->error('更新失败');
        } else {
            $row = model\Merchant::get($id);
            if(empty($row)){
                $this->error('记录已经不存在');
            }




            $creditZt = [];
            $loanZt = [];
            $rs = model\Zuanti::all();
            if($rs){
                foreach ($rs as $vo) {
                    if($vo['is_credit']){
                        $creditZt[$vo['id']] = $vo['zt_name'];
                    }else{
                        $loanZt[$vo['id']] = $vo['zt_name'];
                    }
                }
            }




            $creditZtSelect = bulid_form('select', $creditZt, 'zt_id',$row->zt_id);
            $this->assign('creditZtSelect',$creditZtSelect);

            $loanZtSelect = bulid_form('select', $loanZt, 'zt_id',$row->zt_id);
            $this->assign('loanZtSelect',$loanZtSelect);





            $typeRadio = bulid_form('radio', [1=>'借款',2=>'信用卡'], 'type',$row->type);
            $this->assign('typeRadio',$typeRadio);


              $cateSelect = bulid_form('select', [
                1=>'现金贷',
                2=>'空放',
                3=>'车辆抵押',
            ], 'cate_id',$row->cate_id);
            $this->assign('cateSelect',$cateSelect);

            $creditCateSelect = bulid_form('select', [
                4=>'信用卡',
            ], 'cate_id',$row->cate_id);
            $this->assign('creditCateSelect',$creditCateSelect);

            $indexRadio = bulid_form('radio', ['否','是'], 'is_index',$row->is_index);
            $this->assign('indexRadio',$indexRadio);


            $objAttr = new model\Label;
            $attr = $objAttr->getAllType();
            $tag = isset($attr[1])?$attr[1]:"";
            $cailiao = isset($attr[2])?$attr[2]:"";
            $uarea =isset($attr[3])?$attr[3]:"";

            $labelCheckbox = bulid_form('checkbox',$tag, 'tag[]',$row->tag,"",false, true);
            $this->assign('labelCheckbox',$labelCheckbox);

            $statusRadio = bulid_form('radio', ['禁用','启用'], 'status',$row->status);
            $this->assign('statusRadio',$statusRadio);


            $minUnitSelect = bulid_form('select', ['日','月','年'], 'min_day_unit',$row->min_day_unit,"",false, true);
            $this->assign('minUnitSelect',$minUnitSelect);


            $maxUnitSelect = bulid_form('select', ['日','月','年'], 'max_day_unit',$row->max_day_unit,"",false, true);
            $this->assign('maxUnitSelect',$maxUnitSelect);


            $fileCheckbox = bulid_form('checkbox', $cailiao, 'cailiao[]',$row->cailiao,"",false, true);
            $this->assign('fileCheckbox',$fileCheckbox);


            $madeCheckbox = bulid_form('checkbox',$uarea, 'use_area[]',$row->use_area,"",false, true);
            $this->assign('madeCheckbox',$madeCheckbox);


            $peopleCheckbox = bulid_form('checkbox', get_jobs(), 'to_people[]',$row->to_people,"",false, true);
            $this->assign('peopleCheckbox',$peopleCheckbox);


            $passRadio = bulid_form('radio', [1=>'人工审核',2=>'自动审核'], 'pass_type',$row->pass_type);
            $this->assign('passRadio',$passRadio);


            $getbackRadio = bulid_form('radio', [1=>'等额本息',2=>'一次性还清'], 'back_type',$row->back_type,'',false,true);
            $this->assign('getbackRadio',$getbackRadio);


            $this->assign('meta_title', '编辑机构');
            $this->assign('row',$row);
            return $this->fetch();
        }
    }


    /**
     * 状态修改 (ok)
     */
    public function changeStatus($method = null) {
        $data = input('id/a');
        $id = array_unique($data);
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] = array('in', $id);
        switch (strtolower($method)) {
            case 'deletezt':
                $this->_delete('zuanti', $map);
                break;
            case 'deletemert':
                $this->_delete('merchant', $map);
                break;
            case 'resumemert':
                $this->resume('merchant', $map);
                break;
            case 'forbidmert':
                $this->forbid('merchant', $map);
                break;
            default:
                $this->error('参数非法');
        }
    }


    //机构列表状态修改

    public function changeStatusOpen($method = null) {
        $data = input('id/a');
        $id = array_unique($data);
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] = array('in', $id);
        $this->resume('merchant', $map);

    }

    public function changeStatusClose($method = null) {
        $data = input('id/a');
        $id = array_unique($data);
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] = array('in', $id);
        $this->forbid('merchant', $map);

    }

    public function changeStatusDel($method = null) {
        $data = input('id/a');
        $id = array_unique($data);
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] = array('in', $id);
        $this->_delete('merchant', $map);

    }

     //ok
    public function zuanti() {
        $list = model\Zuanti::order('id', 'desc')->paginate(10);
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page', $page);
        $this->assign('meta_title', '专题管理');
        return $this->fetch();
    }

    //ok
    public function zuantiAdd() {
        if (request()->isPost()) {
            $result = $this->validate($_POST,[
                'zt_name'=>'require'
            ],[
                'zt_name.require'=>'请填写专题名称'
            ]);
            if(true !== $result){
               $this->error($result);
            }
            $mode = new model\Zuanti();
            $flag = $mode->allowField(true)->save($_POST);
            if($flag){
                $this->success('添加成功！', url('zuanti'));
            }
            $this->error('添加失败');
        } else {
            $statusRadio = bulid_form('radio', ['禁用','启用'], 'status',1);
            $this->assign('statusRadio',$statusRadio);
            $this->assign('meta_title', '新增专题');
            return $this->fetch();
        }
    }


    //ok
    public function zuantiEdit() {
        $id = input('id', '');
        if (empty($id)) {
            $this->error('参数不能为空！');
        }
        if (request()->isPost()) {
            $result = $this->validate($_POST,[
                'zt_name'=>'require',
            ],[
                'zt_name.require'=>'请填专题名称',
            ]);
            if(true !== $result){
               $this->error($result);
            }
            $mode = new model\Zuanti();
            $flag = $mode->isUpdate(true)->allowField(true)->save($_POST,['id'=>$id]);
            if($flag){
                $this->success('更新成功！', url('zuanti'));
            }
            $this->error('更新失败');
        } else {
            $row = model\Zuanti::where('id',$id)->find();
            if(empty($row)){
                $this->error('记录已经不存在');
            }
            $this->assign('meta_title', '编辑专题');
            $this->assign('row',$row);
            return $this->fetch();
        }
    }


    //ok
    public function tongji(){
        $id = session('user_auth.id');
        $status='none';
        if($id==1){
            $status='';
        }else {

            $role_id = model\RoleUser::field('user_id,role_id')->where('user_id', $id)->select();

            if ($role_id) {
                foreach ($role_id as $k => $v) {
                    $role = model\Role::where('id', $v->role_id)->find();
                    if ($role) {
                        $roles = 'xs,' . $role['rules'];//防止在开头出现
                        $statuss = strpos($roles,'233');
                        if ($statuss) {
                            $status = '';//显示
                        }
                    }
                }
            }

        }

        $this->assign('status',$status);
        $mert_id = input('mert_id',0);

        $mert = [];
        $res = model\Merchant::field('id,name')->where([
            'status'=>1,
            'type'=>1
        ])->select();
        foreach ($res as $vo) {
            $mert[$vo->id] = $vo->name;
        }
        $mert[0] = '全部';
        $mertSelect = bulid_form('select',$mert, 'mert_id',$mert_id);


        $sdate = input('sdate',date('Y-m-d',strtotime("-1 week")));
        $edate = input('edate',date('Y-m-d'));
        $map =  [];
        if($mert_id){
            $map['mert_id'] = $mert_id;
        }
        if($sdate && $edate){
            $map['date'] = ['between',[$sdate,$edate]];
        }elseif($sdate){
            $map['date'] = ['between',[$sdate,$sdate]];
        }elseif($edate){
            $map['date'] = ['between',[$edate,$edate]];
        }
        $list = model\TongjiMert::order('id', 'desc')->where($map)->paginate(20);
        $page = $list->render();
        $this->assign('_list',$list);
        $this->assign('_page', $page);

        $this->assign('sdate',$sdate);
        $this->assign('edate',$edate);
        $this->assign('mertSelect',$mertSelect);
        $this->assign('meta_title', '下单统计');

        $exportUrl = url('exportXd',[
            'mert_id'=>$mert_id,
            'sdate'=>$sdate,
            'edate'=>$edate
        ]);
        $this->assign('exportUrl',$exportUrl);

        return $this->fetch();
    }


    public function detail($date=null){
        if(is_null($date)){
            $this->error('页面不存在');
        }
       $obj = new model\TongjiXiadan;
       $list = $obj->where('date',$date)->select();
       $this->assign('list',$list);
       $this->assign('meta_title', $date. '- 下单统计');
       return $this->fetch();
    }

    //导出(ok)
    public function exportXd(){
        $sdate = input('sdate');
        $edate = input('edate');
        $mert_id = input('mert_id',0);
        $map =  [];
        if($mert_id){
            $map['mert_id'] = $mert_id;
        }
        if($sdate && $edate){
            $map['date'] = ['between',[$sdate,$edate]];
        }elseif($sdate){
            $map['date'] = ['between',[$sdate,$sdate]];
        }elseif($edate){
            $map['date'] = ['between',[$edate,$edate]];
        }
        $list = model\TongjiMert::order('id', 'desc')->where($map)->select();
        if(!$list){
            $this->error('无数据可以导出');
        }
        $xlsName  = "Xiadan";
        $xlsCell  = array(
            array('date','机构编号'),
            array('mert_name','机构名称'),
            array('apply_cash','申请金额(元)'),
            array('loan_cash','放款金额(元)'),
        );
        $this->exportExcel($xlsName,$xlsCell,$list);
    }

    //ok
    public function uploadOrderExcel() {

        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');
        $file = request()->file('download');
        if (empty($file)) {
            $this->error('请选择上传文件');
        }
//        $File = model('file');
        $File = request()->file('download');
        $info = $File->move(UPLOAD_PATH);

        $return = [];
        if ($info) {
            vendor("PHPExcel.PHPExcel");
            $file_name =  UPLOAD_PATH.$info->getSaveName();
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            $field = [
                'A'=>'date',
                'B'=>'mert_id',
                'C'=>'apply_cash',
                'D'=>'loan_cash',
                'E'=>'phone',
            ];

            $regs = [];
            for($i=2;$i<=$highestRow;$i++){
                $data = [];
                foreach ($field as $key => $vo) {
                    $val = $objPHPExcel->getActiveSheet()->getCell($key.$i)->getValue();
                    if($vo=='date'){
                        $val = \PHPExcel_Shared_Date::ExcelToPHP($val);
                        $val = date("Y-m-d",$val);
                    }
                    $data[$vo] = $val;
                }
                if(empty($data)){
                    continue;
                }
                $obj= new model\TongjiXiadan;

                if(!$data['mert_id'] || is_null($data['mert_id']) || !$data['apply_cash']){
                    continue;
                }

                $regs[$data['mert_id']] ++;
            }

            for($i=2;$i<=$highestRow;$i++){
                $data = [];
                foreach ($field as $key => $vo) {
                    $val = $objPHPExcel->getActiveSheet()->getCell($key.$i)->getValue();
                    if($vo=='date'){
                        $val = \PHPExcel_Shared_Date::ExcelToPHP($val);
                        $val = date("Y-m-d",$val);
                    }
                    $data[$vo] = $val;
                }
                if(empty($data)){
                    continue;
                }
                $obj= new model\TongjiXiadan;

                if(!$data['mert_id'] || is_null($data['mert_id']) || !$data['apply_cash']){
                    continue;
                }

                $data['regs'] = $regs[$data['mert_id']];
                //检测是否已经导入存在
                if(!$obj->where([
                    'mert_id'=>$data['mert_id'],
                    'loan_cash'=>$data['loan_cash'],
                    'date'=>$data['date'],
                    'apply_cash'=>$data['apply_cash'],
                    'phone'=>$data['phone'],
                    'regs'=>$data['regs'],
                ])->find()){
                    $obj->isUpdate(false)->save($data);
                }
            }
            unlink($file_name);
            $return['status'] = 1;
            $return['data'] = think_encrypt(json_encode($info));
            $return['info'] = $info->getFilename;
        } else {
            $return['status'] = 0;
            $return['info'] = $File->getError();
        }
        return json($return);
    }






}
