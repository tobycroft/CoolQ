<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\index\action;

use app\v1\action\OnMessageAction;
use app\v1\action\OnRequestAction;
use app\v1\action\OnNoticeAction;

class RouterAction {

	public static function Do_action($json) {
		if (gettype($json) == 'string') {
			$json = json_decode($json, 1);
		}
		if (!\app\v2\logic\PowerLogic::bot_avail($json['self_id'])) {
			echo "机器人已经过期";
			exit(0);
		}
		if (isset($json['post_type'])) {
			switch ($json['post_type']) {
				case 'message':
					echo 'message';
					self::OnMessageAction($json);
					break;

				case 'notice':
					echo 'notice';
					self::OnNoticeAction($json);
					break;

				case 'request':
					echo 'request';
					self::OnRequestAction($json);
					break;

				default:
					echo 'error';
					self::Log_error(json_encode($json));
					break;
			}
		} else {
			self::Log_error(json_encode($json));
		}
	}

	public static function Log_all($logs, $discript = null) {
		\app\v1\model\LogsModel::api_insert($logs, $discript);
	}

	public static function Log_error($logs, $discript = null) {
		\app\v1\model\LogsErrorModel::api_insert($logs, $discript);
	}

	public static function OnMessageAction($json) {
		switch ($json['message_type']) {
			case 'private':
				echo 'private';
				OnMessageAction::private_chat_friend($json);
				break;

			case 'group':
				echo 'group';
				switch ($json['sub_type']) {
					case 'normal':
						echo 'normal';
						OnMessageAction::group_chat_normal($json);
						break;

					case 'notice':
						echo 'notice';
						OnNoticeAction::group_ban_change($json);
						break;

					case 'discuss':
						echo 'discuss';
						\app\v1\action\CombAction::set_discuss_leave($json['self_id'], $json['discuss_id']);
						break;
					default:
						break;
				}

				break;

			default:
				self::Log_error(JObject($json), __FUNCTION__);
				break;
		}
	}

	public static function OnNoticeAction($json) {
		switch ($json['notice_type']) {
			case 'group_decrease':
				echo 'group_decrease';
				OnNoticeAction::group_member_dec($json);
				break;
			case 'group_increase':
				echo 'group_increase';
				OnNoticeAction::group_member_inc($json);
				break;
			case 'group_admin':
				echo 'group_admin';
				OnNoticeAction::group_change_admin($json);
				break;

			case 'unknown':
				echo 'unknown-notice';
				if ($json['sub_type']) {
					OnNoticeAction::group_ban_change_notice($json);
					break;
				}


			default:
				echo $json['notice_type'];
				self::Log_error(JObject($json), __FUNCTION__);
				break;
		}
	}

	public static function OnRequestAction($json) {
		switch ($json['request_type']) {
			case 'friend':
				echo 'friend';
				OnRequestAction::private_friend_request($json);
				break;

			case 'group':
				echo 'group';
				OnRequestAction::group($json);
				break;

			default:
				self::Log_error(JObject($json), __FUNCTION__);
				break;
		}
	}

}
