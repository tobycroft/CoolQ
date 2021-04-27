<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class PrivateFriendModel {

	public static function api_pre_insertAll($self_id, $user_id, $nickname, $remark) {
		$data = [
			'self_id' => $self_id,
			'user_id' => $user_id,
			'nickname' => $nickname,
			'remark' => $remark,
		];
		return $data;
	}

	public static function api_insertAll($data) {
		$db = db('private_friend');
		return $db->insertAll($data);
	}

	public static function api_clear() {
		$db = db('private_friend');
		return $db->query('TRUNCATE `cq_private_friend`');
	}

}
