<?php
// +----------------------------------------------------------------------
// | xs
// +----------------------------------------------------------------------
// | Copyright (c) 2013 All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------

return array(
	'title'=>array(//配置在表单中的键名 ,这个会是config[title]
		'title'=>'显示标题:',//表单的文字
		'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'糗事百科',			 //表单的默认值
	),
	'width'=>array(
		'title'=>'显示宽度:',
		'type'=>'select',
		'options'=>array(
			'1'=>'1格',
			'2'=>'2格',
			'4'=>'4格'
		),
		'value'=>'2'
	),
	'display'=>array(
		'title'=>'是否显示:',
		'type'=>'radio',
		'options'=>array(
			'1'=>'显示',
			'0'=>'不显示'
		),
		'value'=>'1'
	),
	'cache_time'=>array(
		'title'=>'缓存采集时间:',
		'type'=>'text',
		'value'=>'60',
		'tip'=>'（单位 秒）'
	)
);
