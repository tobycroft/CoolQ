<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\action;

use app\v1\model\GroupSendModel;
use \app\v1\model\PrivateSendModel;
use app\v1\service\NetService;

class CombAction {

	public static function delete_msg($self_id, $message_id) {
		$arr = [
			'message_id' => (int) $message_id,
		];
		$ret = NetService::serv_get($self_id, __FUNCTION__, $arr);
//		dump($ret);
		if ($ret['retcode'] == '0') {
			GroupSendModel::api_update_retract($message_id);
			PrivateSendModel::api_update_retract($message_id);
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function set_restart($self_id) {
		return NetService::serv_get($self_id, __FUNCTION__);
	}

	public static function set_restart_plugin($self_id) {
		return NetService::serv_get($self_id, __FUNCTION__);
	}

	public static function set_discuss_leave($self_id, $discuss_id) {
		$arr = [
			'discuss_id' => $discuss_id,
		];
		$ret = NetService::serv_get($self_id, __FUNCTION__, $arr);
		if ($ret['retcode'] == '0') {
			return true;
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function get_login_info($self_id) {
		$ret = NetService::serv_get($self_id, __FUNCTION__);
		if ($ret['retcode'] == '0') {
			return $ret['data'];
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function get_cookies($self_id) {
		$ret = NetService::serv_get($self_id, __FUNCTION__);
		if ($ret['retcode'] == '0') {
			return $ret['data'];
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function get_status($self_id) {
		$ret = NetService::serv_get($self_id, __FUNCTION__);
		if ($ret['retcode'] == '0') {
			return $ret['data'];
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function clean_data_dir($self_id) {
		$ret = NetService::serv_get($self_id, __FUNCTION__);
		if ($ret['retcode'] == '0') {
			return $ret['data'];
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

}
