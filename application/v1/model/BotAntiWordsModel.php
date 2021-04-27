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
class BotAntiWordsModel {

	static $table = 'bot_anti_words';

	public static function api_select() {
		$cache = cache(__CLASS__);
		if ($cache) {
			return $cache;
		}
		$db = \think\Db::table(self::$table);
		return $db->cache(__CLASS__, 30)->column('word');
	}

}
