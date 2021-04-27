<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\action;

use app\v1\service\NetService;
use app\v1\model\PrivateSendModel;
use app\v1\model\PrivateFriendModel;

class PrivateAction {

	public static function send_private_msg($self_id, $user_id, $message, $auto_escape = false) {
		if (!bots()[$self_id]['unlimit']) {
			if (!cache('send_repeat_protect_' . $user_id . md5($message))) {
				cache('send_repeat_protect_' . $user_id . md5($message), $message, 120);
			} else {
				echo 'private_send_reprat' . $self_id . $user_id . ($message);
				exit(0);
			}
		}
		if (!\app\v2\logic\PowerLogic::bot_avail($self_id)) {
			echo "机器人已经过期";
			exit(0);
		}
		dump('sendingprivate-' . $message . '--status:');
		$arr = [
			'user_id' => $user_id,
			'message' => $message,
			'auto_escape' => $auto_escape,
		];
		$ret = NetService::serv_post($self_id, __FUNCTION__, $arr);
		dump($ret);
		if ($ret['retcode'] == '0') {
			PrivateSendModel::api_insert($self_id, $user_id, $message, $ret['data']['message_id'], $ret['retcode']);
		} else {
			\app\v1\model\LogsSendModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

//	public static function send_like($user_id, $times) {
//		$arr = [
//			'user_id' => $user_id,
//			'times' => $times,
//		];
//		$ret = NetService::serv_post(__FUNCTION__, $arr);
//		if ($ret['retcode'] == '0') {
//			\app\v1\model\PrivateLikeModel::api_auto($user_id, $times);
//		} else {
//			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
//		}
//	}

	public static function _get_friend_list($self_id) {
		$arr = [
			'flat' => true,
		];
		$ret = NetService::serv_post($self_id, __FUNCTION__, $arr);
		if ($ret['retcode'] == '0') {
			$friends = $ret['data']['friends'];
			PrivateFriendModel::api_clear();
			$arr = [];
			foreach ($friends as $value) {
				$arr[] = PrivateFriendModel::api_pre_insertAll($self_id, $value['user_id'], $value['nickname'], $value['remark']);
			}
			PrivateFriendModel::api_insertAll($arr);
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function _get_vip_info($self_id, $user_id, $time = 0) {
		$arr = [
			'user_id' => $user_id,
		];
		$ret = NetService::serv_get($self_id, __FUNCTION__, $arr);
		if ($ret['retcode'] == '0') {
			$user_info = $ret['data'];
			if (isset($user_info['level'])) {
				return $user_info;
			} else {
				usleep(500 * 1000);
				return self::_get_vip_info($self_id, $user_id, $time++);
			}
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function set_friend_add_request($self_id, $flag, $approve = true, $remark = '') {
		$arr = [
			'flag' => $flag,
			'approve' => $approve,
			'remark' => $remark,
		];
		$ret = NetService::serv_post($self_id, __FUNCTION__, $arr);
		if ($ret['retcode'] == '0') {
			\app\v1\model\LogsModel::api_insert(JObject($ret), __FUNCTION__);
			return $ret;
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

}
