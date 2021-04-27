<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\model;

/**
 * Description of GroupLuckydrawModel
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class GroupLuckydrawModel {

	public static $table = 'group_luckydraw';

	//put your code here
	public static function api_find($group_id) {
		$db = db(self::$table);
		$where = [
			'group_id' => $group_id,
			'expire' => ['>', time()],
			'compelete' => 0,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_insert($group_id, $reward, $num = 1, $expire = 3600, $type = 1) {
		$db = db(self::$table);
		$data = [
			'group_id' => $group_id,
			'expire' => $expire + time(),
			'type' => $type,
			'reward' => $reward,
			'num' => $num,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_update($id, $winner) {
		$db = db(self::$table);
		$where = [
			'id' => $id,
			'compelete' => 0,
		];
		$db->where($where);
		$data = [
			'winner' => $winner,
			'compelete' => 1,
		];
		$db->data($data);
		return $db->insert();
	}

}
