<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
function JObject($data) {
	return json_encode($data, 320);
}

function bot($self_id) {
	$db = think\Db::table('bot');
	$db->where('self_id', $self_id);
	$data = $db->find();
	unset($data['id']);
	return $data;
}

function bots() {
	$db = think\Db::table('bot');
	$db->where('active', true);
	$db->order('rank asc');
	$data = $db->select();
	$arr = [];
	foreach ($data as $value) {
		unset($value['id']);
		$arr[$value['self_id']] = $value;
	}
	return $arr;
}

function C($key) {
	$db = think\Db::table('system_param');
	$db->where('param', $key);
	return $db->find()['val'];
}
