<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\action;

class OnRequestAction {

	public static function private_friend_request($json) {
		$ret = \app\v1\model\BotSettingsModel::api_find($json['self_id']);
		if ($ret['friend_request']) {
			PrivateAction::set_friend_add_request($json['self_id'], $json['flag']);
			PrivateAction::_get_friend_list($json['self_id']);
		} else {
			PrivateAction::set_friend_add_request($json['self_id'], $json['flag'], false, $ret['remark']);
//			\app\v1\model\PrivateRequestModel::api_insert($json['self_id'], $json['user_id'], $json['comment'], $json['flag']);
		}
	}

	public static function group($json) {
		switch ($json['sub_type']) {
			case 'invite':
				echo 'invite';
				self::group_invite_request($json);
				break;

			case 'add':
				echo 'add';
				self::group_join_request($json);
				break;

			default:
				echo 'gpadd';
				break;
		}
	}

	public static function group_invite_request($json) {
		$ret = \app\v1\model\BotSettingsModel::api_find($json['self_id']);
		dump($ret);

		if (!\app\v1\model\GroupInfoModel::api_find($json['group_id']) || \app\v1\model\BotBlackListModel::api_find($json['group_id'])) {
			if ($ret['group_invite']) {
//				GroupAction::set_group_add_request($json['self_id'], $json['flag'], $json['sub_type'], $ret['remark'], false);
				GroupAction::set_group_add_request($json['self_id'], $json['flag'], $json['sub_type'], 'ok');
				sleep(2);
				$group_info = GroupAction::_get_group_info($json['self_id'], $json['group_id'], 1);
				\app\v1\logic\GroupLogic::group_user_limit_check($json['self_id'], $json['group_id'], $group_info['member_count']);
			} else {
				GroupAction::set_group_add_request($json['self_id'], $json['flag'], $json['sub_type'], $ret['remark'], false);
				echo 'refuse';
			}
		} else {
			echo 'refuse';
			GroupAction::set_group_add_request($json['self_id'], $json['flag'], $json['sub_type'], $ret['remark'], false);
		}
	}

	public static function group_join_request($json) {
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($json['group_id']);
		if ($groupfunc['auto_in']) {
			if ($groupfunc['refugee']) {
				$black = \app\v1\model\GroupBlackListModel::api_find_user($json['user_id']);
			} else {
				$black = \app\v1\model\GroupBlackListModel::api_find($json['group_id'], $json['user_id']);
			}
			if ($black) {
				GroupAction::set_group_add_request($json['self_id'], $json['flag'], $json['sub_type'], '您在群黑名单中,请联系群管', false);
			} else {
				GroupAction::set_group_add_request($json['self_id'], $json['flag'], $json['sub_type'], '您在群黑名单中,请联系群管');
				GroupAction::get_group_member_info($json['self_id'], $json['group_id'], $json['user_id']);
			}
		} else {
			\app\v1\model\GroupRequestModel::api_insert($json['self_id'], $json['group_id'], $json['user_id'], $json['sub_type'], $json['comment'], $json['flag']);
		}
	}

}
