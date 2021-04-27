<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\action;

use app\v1\model\GroupSendModel;
use app\v1\service\NetService;
use app\v1\model\GroupKickModel;
use app\v1\model\GroupBanModel;
use app\v1\model\GroupMemberModel;
use app\v1\model\GroupInfoModel;

class GroupAction {

	public static function send_group_msg($self_id, $group_id, $message, $auto_escape = false, $force_send = false, $speed_limit = true) {
		dump('send-by-' . $self_id);
		echo 'send:' . $message;
		if (!\app\v2\logic\PowerLogic::bot_avail($self_id)) {
			echo "机器人已经过期";
			exit(0);
		}
		if (!bots()[$self_id]['unlimit']) {
			if ($speed_limit) {
				if (!cache('send_repeat_protect_' . $group_id . md5($message))) {
					cache('send_repeat_protect_' . $group_id . md5($message), $message, 120);
				} else {
					echo 'rpt_proct';
					exit(0);
				}
			}
		}
		if (!$force_send) {
			if (!\app\v2\logic\PowerLogic::privilage_bot($self_id)) {
				$ams = \app\v1\model\BotAntiWordsModel::api_select();
				if ($ams) {
					foreach ($ams as $value) {
						if (strstr($message, $value)) {
							if (!cache(__CLASS__ . __FUNCTION__ . $group_id)) {
								cache(__CLASS__ . __FUNCTION__ . $group_id, 1, 86400);
							}
							if (cache(__CLASS__ . __FUNCTION__ . $group_id)) {
								cache(__CLASS__ . __FUNCTION__ . $group_id, cache(__CLASS__ . __FUNCTION__ . $group_id) + 1, 86400);
							}
							if (cache(__CLASS__ . __FUNCTION__ . $group_id) > 3) {
								\app\v1\model\BotBlackListModel::api_insert($group_id);
								echo '恶意值+1';
								GroupAction::send_group_msg($self_id, $group_id, '本群已进入黑名单，群内全体成员恶意值+1，如为误报请联系开发群：542749156', null, true);
								self::set_group_leave($self_id, $group_id);
								self::set_group_leave($self_id, $group_id, true);
							}
							exit(0);
						}
					}
				}
			}
			if (\app\v1\model\BotBlackListModel::api_find($group_id)) {
				echo 'blacklist_of_bots_sql';

				GroupAction::send_group_msg($self_id, $group_id, '本群在Acfur高危数据库中，无法使用AcfurBOT，如为误报请联系开发群：542749156', null, true);
				\app\v1\action\GroupAction::set_group_leave($self_id, $group_id);
				\app\v1\action\GroupAction::set_group_leave($self_id, $group_id, true);
			}
		}
		$arr = [
			'group_id' => $group_id,
			'message' => $message,
			'auto_escape' => $auto_escape,
		];
		$ret = NetService::serv_post($self_id, __FUNCTION__, $arr);
		echo 'ret';
		dump($ret);
		if ($ret['retcode'] == '0') {
			GroupSendModel::api_insert($self_id, $group_id, $message, $ret['data']['message_id'], $ret['retcode']);
		} elseif ($ret['retcode'] == '-34') {
			\app\v1\action\GroupAction::set_group_leave($self_id, $group_id);
			\app\v1\action\GroupAction::set_group_leave($self_id, $group_id, true);
		} else {
			\app\v1\model\LogsSendModel::api_insert(JObject($ret), __FUNCTION__);
		}
		return $ret['retcode'];
	}

	public static function set_group_kick($self_id, $group_id, $user_id, $reject_add_request = false) {
		$arr = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'reject_add_request' => $reject_add_request,
		];
		$ret = NetService::serv_get($self_id, __FUNCTION__, $arr);
		\app\v1\model\LogsActionModel::api_insert(json_encode($arr, 320), json_encode($ret, 320), 'set_group_kick');
		if ($ret['retcode'] == '0') {
			return GroupKickModel::api_insert($group_id, $user_id, $reject_add_request);
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function set_group_ban($self_id, $group_id, $user_id, $duration, $log = true) {
		if ($duration > 2592000) {
			$duration = 2592000;
		}
		$arr = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'duration' => $duration,
		];
		$ret = NetService::serv_get($self_id, __FUNCTION__, $arr);
//		dump($ret);
		\app\v1\model\LogsActionModel::api_insert(json_encode($arr, 320), json_encode($ret, 320), 'set_group_ban');
		if ($ret['retcode'] == '0') {
			$at = '[CQ:at,qq=' . $user_id . ']';
			if ($duration > 2505599) {
				if (\app\v1\model\GroupBanPermenentModel::api_insert($self_id, $group_id, $user_id, time() + 86400 * 30)) {
					echo $self_id, '-', $group_id, '-', $user_id, '-永久小黑屋';
					GroupAction::send_group_msg($self_id, $group_id, $at . '暂时加入永久小黑屋，验证后自动解封');
				}
			} elseif ($duration == 0) {
				if (\app\v1\model\GroupBanPermenentModel::api_delete($group_id, $user_id)) {
					GroupAction::send_group_msg($self_id, $group_id, '自动解封，欢迎活人' . $at);
				}
			} else {
				echo $duration;
			}

			if ($log) {
				return GroupBanModel::api_insert($self_id, $group_id, $user_id, $duration);
			} else {
				return true;
			}
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function set_group_whole_ban($self_id, $group_id, $enable = true) {
		$arr = [
			'group_id' => $group_id,
			'enable' => $enable,
		];
		$ret = NetService::serv_get($self_id, __FUNCTION__, $arr);
//		\app\v1\model\LogsActionModel::api_insert(json_encode($ret, 320), 'set_group_whole_ban');
		\app\v1\model\LogsActionModel::api_insert(json_encode($arr, 320), json_encode($ret, 320), 'set_group_whole_ban');
		if ($ret['retcode'] == '0') {
			return true;
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function set_group_card($self_id, $group_id, $user_id, $card) {
		$arr = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'card' => $card,
		];
		$ret = NetService::serv_get($self_id, __FUNCTION__, $arr);
//		\app\v1\model\LogsActionModel::api_insert(json_encode($ret, 320), 'set_group_card');
		\app\v1\model\LogsActionModel::api_insert(json_encode($arr, 320), json_encode($ret, 320), 'set_group_card');
		if ($ret['retcode'] == '0') {
			return true;
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function set_group_admin($self_id, $group_id, $user_id, $enable = false) {
		$arr = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'enable' => $enable,
		];
		$ret = NetService::serv_get($self_id, __FUNCTION__, $arr);
//		\app\v1\model\LogsActionModel::api_insert(json_encode($ret, 320), 'set_group_card');
		\app\v1\model\LogsActionModel::api_insert(json_encode($arr, 320), json_encode($ret, 320), 'set_group_admin');
		if ($ret['retcode'] == '0') {
			return true;
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	/*
	 * array(1) {
	  [0] => array(2) {
	  ["group_id"] => int(94537310)
	  ["group_name"] => string(24) "火线兔的宇宙飞船"
	  }
	  }
	 */

	public static function get_group_list($self_id) {
		$ret = NetService::serv_get($self_id, __FUNCTION__);
		if ($ret['retcode'] == '0') {
			return $ret['data'];
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function get_group_member_info($self_id, $group_id, $user_id, $no_cache = false, $role = null) {
		$arr = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'no_cache' => $no_cache,
		];
		$ret = NetService::serv_post($self_id, __FUNCTION__, $arr);
		if ($ret['retcode'] == '0') {
			$value = $ret['data'];
			if (!GroupMemberModel::api_find_bySelfid($self_id, $group_id, $user_id) || $no_cache) {
				if ($no_cache) {
					GroupMemberModel::api_delete_byUserId($group_id, $user_id);
				}
				GroupMemberModel::api_insert($self_id, $group_id, $user_id, $value['nickname'], $value['age'], $value['join_time'], $value['last_sent_time'], $value['sex'], $value['role'], $value['card'], $value['level']);
			} else {
				if ($user_id != $self_id || $role) {
					if ($role) {
						$value['role'] = 'admin';
					}
					GroupMemberModel::api_update($group_id, $user_id, $value['nickname'], $value['age'], $value['join_time'], $value['last_sent_time'], $value['sex'], $value['role'], $value['card'], $value['level']);
				}
			}
			return $value;
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function get_group_member_list($self_id, $group_id, $true = true) {
		$arr = [
			'group_id' => $group_id,
		];
		$ret = NetService::serv_get($self_id, __FUNCTION__, $arr);
		if ($ret['retcode'] == '0') {
			$data = $ret['data'];
//			dump($data);
			if ($true) {
				GroupMemberModel::api_delete($group_id);
				$arr = [];
				foreach ($data as $value) {
					$arr[] = GroupMemberModel::api_pre_insertAll($self_id, $group_id, $value['user_id'], $value['nickname'], $value['age'], $value['join_time'], $value['last_sent_time'], $value['sex'], $value['role'], $value['card']);
				}
				GroupMemberModel::api_insertAll($arr);
			}
			return $data;
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function _get_group_info($self_id, $group_id, $change = false) {
		$ret = GroupInfoModel::api_find($group_id);
		if (!$ret || $change) {
			$arr = [
				'group_id' => $group_id,
			];
			$ret1 = NetService::serv_get($self_id, "get_group_info", $arr);
			$ret = NetService::serv_get($self_id, "_get_group_info", $arr);
			if ($ret['retcode'] == '0' && $ret1["retcode"] == "0") {
				$groupinfo = $ret1["data"];
				$data = $ret['data'];
//				dump($groupinfo);
//				dump($data);

				GroupInfoModel::api_delete($group_id);
				if (!isset($data['owner_id'])) {
					$data['owner_id'] = $data['admins'][0]['user_id'];
				}
				if (!isset($data['max_admin_count'])) {
					$data['max_admin_count'] = 1;
				}
				if (!isset($data['max_member_count'])) {
					$data['max_member_count'] = 1;
				}
				if (GroupInfoModel::api_insert($self_id, $group_id, $groupinfo['group_name'], 0, $data['owner_id'], [], 0, 0, "", $data['max_admin_count'], $groupinfo['max_member_count'], $groupinfo['member_count'])) {
					return $data;
				} else {
					return false;
				}
			} else {
				\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
			}
		} else {
			return $ret;
		}
	}

	public static function set_group_add_request($self_id, $flag, $sub_type, $reason, $approve = true) {
		$arr = [
			'flag' => $flag,
			'sub_type' => $sub_type,
			'approve' => $approve,
			'reason' => $reason,
		];
		$ret = NetService::serv_post($self_id, __FUNCTION__, $arr);
		if ($ret['retcode'] == '0') {
			return $ret;
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function delete_msg($self_id, $message_id) {
		$arr = [
			'message_id' => $message_id,
		];
		$ret = NetService::serv_post($self_id, __FUNCTION__, $arr);
		if ($ret['retcode'] == '0') {
			return true;
		} else {
			\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
		}
	}

	public static function set_group_leave($self_id, $group_id, $is_dismiss = false) {
		$arr = [
			'group_id' => $group_id,
			'is_dismiss' => $is_dismiss
		];
		try {
			$ret = NetService::serv_get($self_id, __FUNCTION__, $arr);
//		dump('exit_info:');
			if (isset($ret['retcode'])) {
				if ($ret['retcode'] == '0') {
					return \app\v1\model\GroupInfoModel::api_delete($group_id);
					return true;
				} else {
					\app\v1\model\LogsErrorModel::api_insert(JObject($ret), __FUNCTION__);
				}
			} else {
				\app\v1\model\LogsSendModel::api_insert($ret, 'set_group_leave');
				$ex = \app\v1\model\GroupInfoModel::api_delete($group_id);
				\app\v1\model\LogsSendModel::api_insert($ex, 'exit_info');
			}
		} catch (Exception $exc) {
			\app\v1\model\LogsSendModel::api_insert($ret, 'set_group_leave_fail');
			$ex = \app\v1\model\GroupInfoModel::api_delete($group_id);
			\app\v1\model\LogsSendModel::api_insert($ex, 'exit_info_fail');
			\app\v1\model\LogsErrorModel::api_insert($exc->getTraceAsString(), __FUNCTION__);
		}
	}

}
