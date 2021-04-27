<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class SystemParamModel {

	public static function api_find($param) {
		$db = db('system_param');
		$where = [
			'param' => $param
		];
		$db->where($where);
		return $db->find()['val'];
	}

}
