<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v3\model;

/**
 * Description of PluginAdListModel
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class PluginAdListModel {

	public static $table = 'plugin_ad_list';

	public static function api_select() {
		$db = \think\Db::table(self::$table);
		return $db->select();
	}

	public static function api_select_in_ids($ids) {
		$db = \think\Db::table(self::$table);
		$where = [
			'id' => ['in', $ids],
		];
		$db->where($where);
		return $db->select();
	}

}
