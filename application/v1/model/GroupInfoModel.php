<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class GroupInfoModel {

	public static function api_insert($self_id, $group_id, $group_name, $admin_count, $owner_id, $admins, $category, $create_time, $introduction, $max_admin_count, $max_member_count, $member_count) {
		$db = \think\Db::table('group_info');
		$data = [
			'self_id' => $self_id,
			'group_id' => $group_id,
			'group_name' => $group_name,
			'admin_count' => $admin_count,
			'owner_id' => $owner_id,
			'admins' => JObject($admins),
			'category' => $category,
			'create_time' => $create_time,
			'introduction' => $introduction,
			'max_admin_count' => $max_admin_count,
			'max_member_count' => $max_member_count,
			'member_count' => $member_count,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_delete($group_id) {
		$db = \think\Db::table('group_info');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_count() {
		$db = \think\Db::table('group_info');
		return $db->count(0);
	}

	public static function api_deletes($group_ids) {
		$db = \think\Db::table('group_info');
		$where = [
			'group_id' => ['in', $group_ids],
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_release($group_ids) {
		$db = \think\Db::table('group_info');
		$where = [
			'group_id' => ['not in', $group_ids],
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_find($group_id) {
		$db = \think\Db::table('group_info');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->find();
	}

}
