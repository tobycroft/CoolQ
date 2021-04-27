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
class BotSettingsModel {

	//put your code here

	public static function api_find($self_id) {
		$db = db('bot_settings');
		$where = [
			'self_id' => $self_id,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_find_byMinUser($min_user) {
		$db = db('bot_settings');
		$where = [
			'min_user' => ['<', $min_user],
			'max_user' => ['>', $min_user],
			'recomm' => true,
		];
		$db->where($where);
		$db->order('min_user desc');
		return $db->find();
	}

}
