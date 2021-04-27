<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class FunctionDetailModel {

	public static function api_find_v($k) {
		$v = cache(__CLASS__ . __FUNCTION__ . $k);
		if (!$v) {
			$db = db('function_detail');
			$where = [
				'k' => $k,
			];
			$db->where($where);
			$v = $db->find();
			cache(__CLASS__ . __FUNCTION__ . $k, $v, 600);
		}
		if (isset($v['v'])) {
			return $v['v'];
		}
	}

	public static function api_find_k($v) {
		$k = self::api_find_k_all($v);
		if (isset($k['k'])) {
			return $k['k'];
		}
	}

	public static function api_find_k_all($v) {
		$k = cache(__CLASS__ . __FUNCTION__ . $v);
		if (!$k) {
			$db = db('function_detail');
			$where = [
				'v' => $v,
			];
			$db->where($where);
			$k = $db->find();
			cache(__CLASS__ . __FUNCTION__ . $v, $k, 600);
		}
		return $k;
	}

}
