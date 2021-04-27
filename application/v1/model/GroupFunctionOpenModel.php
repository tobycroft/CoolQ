<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class GroupFunctionOpenModel {

	public static function __check_exists($group_id) {
		if (!self::api_find($group_id)) {
			self::api_insert($group_id);
		}
	}

	public static function api_insert($group_id) {
		$db = db('group_function_open');
		$data = [
			'group_id' => $group_id,
		];
		$db->data($data);
		self::api_cache_clear($group_id);
		return $db->insert();
	}

	public static function api_delete($group_id) {
		$db = db('group_function_open');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_deletes($group_ids) {
		$db = db('group_function_open');
		$where = [
			'group_id' => ['in', $group_ids],
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_find($group_id) {
		$find = cache('_group_func1_' . $group_id);
		if ($find) {
			return $find;
		} else {
			$db = db('group_function_open');
			$where = [
				'group_id' => $group_id,
			];
			$db->where($where);
			$find = $db->find();
			if (!$find) {
				self::api_insert($group_id);
				return self::api_find($group_id);
			} else {
				cache('_group_func1_' . $group_id, $find, 60);
				return $find;
			}
		}
	}

	public static function api_update_manual($group_id, $key, $value) {
		self::api_cache_clear($group_id);
		self::__check_exists($group_id);
		$db = db('group_function_open');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		$data = [
			$key => $value,
		];
		$db->data($data);
		return $db->update();
	}

	public static function api_update_on($group_id, $key) {
		self::api_cache_clear($group_id);
		self::__check_exists($group_id);
		$db = db('group_function_open');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		$data = [
			$key => 1,
		];
		$db->data($data);
		return $db->update();
	}

	public static function api_update_off($group_id, $key) {
		self::api_cache_clear($group_id);
		self::__check_exists($group_id);
		$db = db('group_function_open');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		$data = [
			$key => 0,
		];
		$db->data($data);
		return $db->update();
	}

	public static function api_update_all_on($group_id) {
		self::api_cache_clear($group_id);
		self::__check_exists($group_id);
		$db = db('group_function_open');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		$data = [
			'shuapin' => 1,
			'shuamany' => 1,
			'guanggao' => 1,
//			'ban_url' => 1,
			'ban_word' => 1,
			'kick_ban' => 1,
			'kick_note' => 1,
			'welcome' => 1,
			'auto_in' => 1,
			'member_inc' => 1,
			'member_dec' => 1,
			'sign' => 1,
			'refugee' => 1,
			'auto_reply' => 1,
			'protect_level' => 1,
			'join_ban' => 1,
			'kick_word' => 1,
			'ban_game' => 1,
			'lottery' => 1,
			'ban_music' => 1,
			'ban_qun' => 1,
			'auto_msg' => 1,
			'word_limit' => 1,
		];
		$db->data($data);
		return $db->update();
	}

	public static function api_update_all_off($group_id) {
		self::api_cache_clear($group_id);
		self::__check_exists($group_id);
		$db = db('group_function_open');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		$data = [
			'shuapin' => 0,
			'shuamany' => 0,
			'guanggao' => 0,
			'ban_url' => 0,
			'ban_word' => 0,
			'kick_ban' => 0,
			'kick_note' => 0,
			'welcome' => 0,
			'auto_in' => 0,
			'member_inc' => 0,
			'member_dec' => 0,
			'sign' => 0,
			'auto_reply' => 0,
			'protect_level' => 0,
			'join_ban' => 0,
			'kick_word' => 0,
			'refugee' => 0,
			'ban_game' => 0,
			'lottery' => 0,
			'ban_music' => 0,
			'ban_qun' => 0,
			'auto_msg' => 0,
			'word_limit' => 0,
		];
		$db->data($data);
		return $db->update();
	}

	public static function api_cache_clear($group_id) {
		cache('_group_func1_' . $group_id, 1, 1);
	}

}
