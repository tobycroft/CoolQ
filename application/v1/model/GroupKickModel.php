<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class GroupKickModel {

	public static function api_insert($group_id, $user_id, $reject_add_request) {
		$db = db('group_kick');
		$data = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'reject_add_request' => $reject_add_request,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_delete($group_id) {
		$db = db('group_kick');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_deletes($group_ids) {
		$db = db('group_kick');
		$where = [
			'group_id' => ['in', $group_ids],
		];
		$db->where($where);
		return $db->delete();
	}

}
