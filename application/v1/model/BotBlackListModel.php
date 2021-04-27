<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

/**
 * Description of BotSettings
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class BotBlackListModel {

	static $table = 'bot_black_list';

	public static function api_find($group_id) {
		$db = \think\Db::table(self::$table);
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_insert($group_id) {
		$db = \think\Db::table(self::$table);
		$data = [
			'group_id' => $group_id,
		];
		$db->data($data);
		return $db->insert();
	}

}
