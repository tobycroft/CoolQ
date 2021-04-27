<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class GroupMemberModel {

	static $table = 'group_member';

	public static function api_insert($self_id, $group_id, $user_id, $nickname, $age, $join_time, $last_sent_time, $sex, $role, $card, $level = 0) {
		$db = \think\Db::table(self::$table);
		$data = [
			'self_id' => $self_id,
			'group_id' => $group_id,
			'user_id' => $user_id,
			'nickname' => $nickname,
			'age' => $age,
			'join_time' => $join_time,
			'last_sent_time' => $last_sent_time,
			'sex' => $sex,
			'role' => $role,
			'card' => $card,
			'level' => $level,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_pre_insertAll($self_id, $group_id, $user_id, $nickname, $age, $join_time, $last_sent_time, $sex, $role, $card, $level = 0) {
		$data = [
			'self_id' => $self_id,
			'group_id' => $group_id,
			'user_id' => $user_id,
			'nickname' => $nickname,
			'age' => $age,
			'join_time' => $join_time,
			'last_sent_time' => $last_sent_time,
			'sex' => $sex,
			'role' => $role,
			'card' => $card,
			'level' => $level,
		];
		return $data;
	}

	public static function api_insertAll($data) {
		$db = \think\Db::table(self::$table);
		return $db->insertAll($data);
	}

	public static function api_delete($group_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_deletes($group_ids) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => ['in', $group_ids],
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_delete_byUserId($group_id, $user_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'user_id' => $user_id,
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_update($group_id, $user_id, $nickname, $age, $join_time, $last_sent_time, $sex, $role, $card) {
		$db = \think\Db::table(self::$table);
		$where = [
			'user_id' => $user_id,
			'group_id' => $group_id,
		];
		$db->where($where);
		$data = [
			'nickname' => $nickname,
			'age' => $age,
			'join_time' => $join_time,
			'last_sent_time' => $last_sent_time,
			'sex' => $sex,
			'role' => $role,
			'card' => $card,
		];
		$db->data($data);
		return $db->update();
	}

	public static function api_update_v2($self_id, $group_id, $user_id, $nickname, $age, $join_time, $last_sent_time, $sex, $role, $card) {
		$db = \think\Db::table(self::$table);
		$where = [
			'self_id' => $self_id,
			'user_id' => $user_id,
			'group_id' => $group_id,
		];
		$db->where($where);
		$data = [
			'nickname' => $nickname,
			'age' => $age,
			'join_time' => $join_time,
			'last_sent_time' => $last_sent_time,
			'sex' => $sex,
			'role' => $role,
			'card' => $card,
		];
		$db->data($data);
		return $db->update();
	}

	public static function api_update_role($group_id, $user_id, $role) {
		$db = \think\Db::table(self::$table);
		$where = [
			'user_id' => $user_id,
			'group_id' => $group_id,
		];
		$db->where($where);
		$data = [
			'role' => $role,
		];
		$db->data($data);
		return $db->update();
	}

	public static function api_find($group_id, $user_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'user_id' => $user_id,
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_find_bySelfid($self_id, $group_id, $user_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'user_id' => $user_id,
			'self_id' => $self_id,
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_find_groupExists($group_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_count_group($group_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->count(0);
	}

	public static function api_like_nickname($group_id, $nickname) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
			'nickname' => ['like', '%' . $nickname . '%'],
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_like_card($group_id, $card) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
			'card' => ['like', '%' . $card . '%'],
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_select_admins($group_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
			'role' => ['IN', 'admin,owner']
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_select_group($user_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'user_id' => $user_id,
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_update_member($group_id, $user_id, $nickname, $role, $card) {
		$db = \think\Db::table(self::$table);
		$where = [
			'user_id' => $user_id,
			'group_id' => $group_id,
		];
		$db->where($where);
		$data = [
			'nickname' => $nickname,
			'role' => $role,
			'card' => $card,
		];
		$db->data($data);
		return $db->update();
	}

}
