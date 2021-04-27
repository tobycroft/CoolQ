<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

use app\v1\action\GroupAction;

/**
 * Description of KickLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class KickLogic {

	//put your code here
	public static function kick($json, $msg, $groupfunc) {
		if (preg_match('/\[(.*?)\]/i', $msg)) {
			$msg = preg_filter('/\[(.*?)\]/', '', $msg);
		}
		dump('kick-' . $msg);
		if (strstr($msg, 'jqqqcom')) {
			echo 'jq';
			GroupAction::delete_msg($json['self_id'], $json['message_id']);
			\app\v1\logic\GroupLogic::logic_ban_user($json, '群', 86400 * 30);
			return true;
		} elseif (strstr($msg, 'tcn')) {
			echo 'tcn';
			GroupAction::delete_msg($json['self_id'], $json['message_id']);
			\app\v1\logic\GroupLogic::logic_ban_user($json, '群', 86400 * 30);
			return true;
		} elseif (strstr($msg, 'urlcn')) {
			echo 'ucn';
			GroupAction::delete_msg($json['self_id'], $json['message_id']);
			\app\v1\logic\GroupLogic::logic_ban_user($json, '群', 86400 * 30);
			return true;
		} elseif (strstr($msg, 'http')) {
			echo 'http';
			GroupAction::delete_msg($json['self_id'], $json['message_id']);
			\app\v1\logic\GroupLogic::logic_ban_user($json, '群', 86400 * 30);
			return true;
		} elseif (preg_match_all('/[0-9]/i', $msg, $num)) {
			echo 'num';
			if (count($num[0]) > 8) {
				if ($groupfunc['guanggao']) {
					GroupAction::delete_msg($json['self_id'], $json['message_id']);
					\app\v1\logic\GroupLogic::logic_kick_user($json, '群', $groupfunc['ban_time']);
					return true;
				}
			}
		}
	}

	public static function kickout($json, $msg, $groupfunc) {
		GroupAction::delete_msg($json['self_id'], $json['message_id']);
		return \app\v1\logic\GroupLogic::logic_kick_user($json, '宣传群', $groupfunc['ban_time']);
	}

}
