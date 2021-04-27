<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class PrivateLikeModel {

	public static function api_auto($user_id, $times) {
		if (self::api_find($user_id)) {
			return self::api_update_time_inc($user_id, $times);
		} else {
			return self::api_insert($user_id, $times);
		}
	}

	public static function api_insert($user_id, $times) {
		$db = db('private_like');
		$data = [
			'user_id' => $user_id,
			'times' => $times,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_find($user_id) {
		$db = db('private_like');
		$where = [
			'user_id' => $user_id,
		];
		$db->$where($where);
		return $db->insert();
	}

	public static function api_update_time_inc($user_id, $times) {
		$db = db('private_like');
		$where = [
			'user_id' => $user_id,
		];
		$db->$where($where);
		$db->inc('times', $times);
		return $db->update();
	}

}
