<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\model;

use think\Db;

/**
 * Description of GroupBalanceModel
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class GroupBalanceModel {

	public static $table = 'group_balance';

	public static function api_insert($group_id, $user_id, $balance = 0) {
		$db = Db::table(self::$table);
		$data = [
			'user_id' => $user_id,
			'group_id' => $group_id,
			'balance' => $balance,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_find_byGroupId($group_id, $user_id) {
		$db = Db::table(self::$table);
		$where = [
			'user_id' => $user_id,
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_select_byGroupId($group_id, $limit = 10) {
		$db = Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
		];

		$db->where($where);
		$db->limit($limit);
		$db->order('balance desc');
		return $db->select();
	}

	public static function api_update_balance($group_id, $user_id, $balance) {
		$db = Db::table(self::$table);
		$where = [
			'user_id' => $user_id,
			'group_id' => $group_id,
		];
		$db->where($where);
		$db->data([
			'balance' => $balance,
		]);
		return $db->update();
	}

	public static function api_inc_balance($group_id, $user_id, $inc_balance) {
		$db = Db::table(self::$table);
		$where = [
			'user_id' => $user_id,
			'group_id' => $group_id,
		];
		$db->where($where);
		$db->inc('balance', $inc_balance);
		return $db->update();
	}

}
