<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class GroupBanModel {

	public static function api_insert($self_id, $group_id, $user_id, $duration) {
		if ($duration > 2592000) {
			$duration = 2592000;
		}
		$db = db('group_ban');
		$data = [
			'self_id' => $self_id,
			'group_id' => $group_id,
			'user_id' => $user_id,
			'duration' => $duration,
			'date' => time(),
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_count($group_id, $user_id) {
		$db = db('group_ban');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		return $db->count(0);
	}

	public static function api_select($group_id, $user_id) {
		$db = db('group_ban');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_find($group_id, $user_id) {
		$db = db('group_ban');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_delete($id) {
		$db = db('group_ban');
		$where = [
			'id' => $id,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_deletes($group_ids) {
		$db = db('group_ban');
		$where = [
			'group_id' => ['in', $group_ids],
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_clear($group_id) {
		$db = db('group_ban');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->delete();
	}

}
