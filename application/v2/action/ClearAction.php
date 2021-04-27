<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\action;

/**
 * Description of ClearAction
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class ClearAction {

	//put your code here

	public static function exists_groups() {
		$db = db('group_info');
		$db->field('group_id');
		$arr = [];
		foreach ($db->select() as $value) {
			array_push($arr, $value['group_id']);
		}
		return $arr;
	}

	public static function check_exists() {
		$data = self::exists_groups();
		$groups = implode(',', $data);
		$db = db();
		$db->query("SELECT group_id FROM `group_info` where group_id not in ($groups);");
		$db->query("DELETE FROM `group_function_open` where group_id not in ($groups);");
		$db->query("DELETE FROM `group_blacklist` where group_id not in ($groups);");
		$db->query("DELETE FROM `group_ban_permenent` where group_id not in ($groups);");
		$db->query("DELETE FROM `group_ban` where group_id not in ($groups);");
		$db->query("DELETE FROM `group_balance` where group_id not in ($groups);");
		$db->query("DELETE FROM `group_tag` where group_id not in ($groups);");
		$db->query("DELETE FROM `group_sign` where group_id not in ($groups);");
		$db->query("DELETE FROM `group_send` where group_id not in ($groups);");
		$db->query("DELETE FROM `group_request` where group_id not in ($groups);");
		$db->query("DELETE FROM `group_recieve` where group_id not in ($groups);");
		$db->query("DELETE FROM `group_member` where group_id not in ($groups);");
	}

}
