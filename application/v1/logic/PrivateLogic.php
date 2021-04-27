<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\logic;

/**
 * Description of PrivateLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class PrivateLogic {

	//put your code here

	public static function logic_sayHello($json, $priv_bot = false) {
		$time = cache('__time__hl__' . $json['user_id']);
		if (!$time) {
			$time = '1';
		}
		switch ($time) {
			case '1':
				\app\v1\action\PrivateAction::send_private_msg($json['self_id'], $json['user_id'], '你好呀~(๑•ᴗ•๑)，有任何疑问可以执行acfurhelp🐠' . $time);
				cache('__time__hl__' . $json['user_id'], $time++, 43200);
				break;
			case '2':
				\app\v1\action\PrivateAction::send_private_msg($json['self_id'], $json['user_id'], '有任何疑问可以执行acfurhelp🐠');
				cache('__time__hl__' . $json['user_id'], $time++, 43200);
				break;
			default:
				break;
		}
	}

	public static function logic_unlock($json, $data, $msg) {
		\app\v1\action\PrivateAction::send_private_msg($json['self_id'], $json['user_id'], $msg);
		\app\v1\action\GroupAction::set_group_ban($json['self_id'], $data['group_id'], $data['user_id'], 0, false);
	}

	public static function balance_check_single($json) {
		$self_id = $json['self_id'];
		$user_id = $json['user_id'];
		$group_id = $json['group_id'];
		$balance = \app\v1\model\GroupBalanceModel::api_find($group_id, $user_id);
		$str = '您在【';
		$group_info = \app\v1\model\GroupInfoModel::api_find($group_id);
		$str .= $group_info['group_name'] . '】的积分为：' . $balance['balance'] . "\n";
		$str .= "--------------------------------------------\n";
		$str .= '积分可以通过签到的方式获得哦~';
		\app\v1\action\PrivateAction::send_private_msg($self_id, $user_id, $str);
	}

	public static function balance_check_all($json) {
		$user_id = $json['user_id'];
		$groups = \app\v1\model\GroupMemberModel::api_select_group($user_id);
		$balance_groups = \app\v1\model\GroupBalanceModel::api_select_groups($user_id);
		$balance = [];
		foreach ($balance_groups as $value) {
			$balance[$value['group_id']] = $value['balance'];
		}
		$balance_info = [];
		foreach ($groups as $value) {
			$balance_info[$value['group_id']] = [];
		}
		$str = '您在各群的积分情况如下：';
		foreach ($balance_info as $key => $value) {
			$group_info = \app\v1\model\GroupInfoModel::api_find($key);
			$balance_info[$key] = [
				'group_name' => $group_info['group_name'],
				'balance' => (isset($balance[$key])) ? $balance[$key] : '0',
			];
			$str .= $balance_info[$key]['group_name'] . '：' . $balance_info[$key]['balance'] . "\n";
		}
		$str .= '积分可以通过签到的方式获得哦~';
		\app\v1\action\PrivateAction::send_private_msg($user_id, $str);
	}

}
