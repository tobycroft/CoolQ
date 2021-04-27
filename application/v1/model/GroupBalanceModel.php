<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class GroupBalanceModel {

	public static function api_find($group_id, $user_id) {
		$db = db('group_balance');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		$ret = $db->find();
		if ($ret) {
			return $ret;
		} else {
			self::api_insert($group_id, $user_id);
			return self::api_find($group_id, $user_id);
		}
	}

	public static function api_select($group_id) {
		$db = db('group_balance');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_select_groups($user_id) {
		$db = db('group_balance');
		$where = [
			'user_id' => $user_id,
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_insert($group_id, $user_id) {
		$db = db('group_balance');
		$data = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_update($group_id, $user_id, $balance) {
		$db = db('group_balance');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		$data = [
			'balance' => $balance,
		];
		$db->data($data);
		return $db->update();
	}

	public static function api_update_balance($group_id, $user_id, $inc_balance) {
		$db = db('group_balance');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		$db->inc('balance', $inc_balance);
		return $db->update();
	}

}
