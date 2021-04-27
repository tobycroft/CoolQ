<?php

namespace app\v1\model;

class GroupAutoreplyModel {

	static $table = 'group_autoreply';

	public static function api_insert($group_id, $key, $val) {
		$db = \think\Db::table(self::$table);
		$data = [
			'group_id' => $group_id,
			'key' => $key,
			'val' => $val,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_count($group_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->count(0);
	}

	public static function api_select($group_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_find($group_id, $key) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
			'key' => $key,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_delete($group_id, $key) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
			'key' => $key,
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

	public static function api_clear($group_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->delete();
	}

}
