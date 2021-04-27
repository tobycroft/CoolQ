<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class GroupSignModel {

	public static function api_insert($group_id, $user_id) {
		$db = db('group_sign');
		$endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
		$data = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'date' => $endToday,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_count($group_id, $user_id) {
		$db = db('group_sign');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		return $db->count(0);
	}

	public static function api_select($group_id, $user_id) {
		$db = db('group_sign');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_find($group_id, $user_id) {
		$db = db('group_sign');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'date' => ['>', time()]
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_count_today($group_id, $user_id) {
		$db = db('group_sign');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'date' => ['>', time()]
		];
		$db->where($where);
		return $db->count(0);
	}

	public static function api_clear($group_id) {
		$db = db('group_sign');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_delete($group_id, $user_id, $limit = 10000) {
		$db = db('group_sign');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		$db->limit($limit);
		return $db->delete();
	}

	public static function api_deletes($group_ids) {
		$db = db('group_sign');
		$where = [
			'group_id' => ['in', $group_ids],
		];
		$db->where($where);
		return $db->delete();
	}

}
