<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\model;

/**
 * Description of GroupLuckydrawLogModel
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class GroupLuckydrawLogModel {

	public static $table = 'group_luckydraw_log';

	//put your code here
	public static function api_find($group_id, $user_id, $draw_id) {
		$db = db(self::$table);
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'draw_id' => $draw_id,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_insert($group_id, $user_id, $draw_id) {
		$db = db(self::$table);
		$data = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'draw_id' => $draw_id,
		];
		$db->data($data);
		return $db->insert();
	}

}
