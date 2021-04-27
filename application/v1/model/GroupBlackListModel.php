<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class GroupBlackListModel {

	public static function api_insert($group_id, $user_id, $reason, $type = 2) {
		$db = db('group_blacklist');
		$data = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'type' => $type,
			'reason' => $reason,
			'date' => time(),
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_find($group_id, $user_id, $type = '1,2') {
		$db = db('group_blacklist');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'type' => ['IN', $type],
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_find_user($user_id) {
		$db = db('group_blacklist');
		$where = [
			'user_id' => $user_id,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_deletes($group_ids) {
		$db = db('group_blacklist');
		$where = [
			'group_id' => ['in', $group_ids],
		];
		$db->where($where);
		return $db->delete();
	}

}
