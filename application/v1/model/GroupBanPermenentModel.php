<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class GroupBanPermenentModel {

	public static function api_insert($self_id, $group_id, $user_id, $totime) {
		$db = db('group_ban_permenent');
		$data = [
			'self_id' => $self_id,
			'group_id' => $group_id,
			'user_id' => $user_id,
			'totime' => $totime,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_select($totime) {
		$db = db('group_ban_permenent');
		$where = [
			'totime' => ['<', $totime]
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_update($group_id, $user_id, $totime) {
		$db = db('group_ban_permenent');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$data = [
			'totime' => $totime,
		];
		$db->where($where);
		$db->data($data);
		return $db->update();
	}

	public static function api_select_byGroupId($group_id) {
		$db = db('group_ban_permenent');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		$db->order('id asc');
		$db->limit(10);
		return $db->select();
	}

	public static function api_find($group_id, $user_id, $totime) {
		$db = db('group_ban_permenent');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'totime' => ['<', $totime]
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_delete($group_id, $user_id) {
		$db = db('group_ban_permenent');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_deletes($group_ids) {
		$db = db('group_ban_permenent');
		$where = [
			'group_id' => ['in', $group_ids],
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_clear($group_id) {
		$db = db('group_ban_permenent');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->delete();
	}

}
