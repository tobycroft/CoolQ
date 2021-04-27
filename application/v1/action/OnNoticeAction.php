<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\action;

class OnNoticeAction {

	public static function group_member_dec($json) {
		$sub_type = $json['sub_type'];
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($json['group_id']);
		$member = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
		$str = '';
		$number = \app\v1\model\GroupMemberModel::api_count_group($json['group_id']) - 1;
		$str .= $member['nickname'] . '[' . $json['user_id'] . ']';
		if (!array_key_exists($json['user_id'], bots())) {
			if ($sub_type == 'kick') {
				if ($groupfunc['kick_note']) {
					if (!$groupfunc['simulate']) {
						\app\v1\logic\GroupLogic::logic_goodbyeword($json);
					}
				}
				if ($groupfunc['kick_ban']) {
					\app\v1\model\GroupBlackListModel::api_insert($json['group_id'], $json['user_id'], '被T出本群', 1);
					$str .= "\n" . '被T出本群，帐号自动拉黑';
				} else {
					$str .= "\n" . '被T出本群';
				}
			} else {
				if ($groupfunc['ban_quit']) {
					\app\v1\model\GroupBlackListModel::api_insert($json['group_id'], $json['user_id'], '退出本群帐号自动拉黑', 2);
					$str .= "\n" . '退出本群帐号自动拉黑';
				} else {
					$str .= "\n" . '退出本群';
				}
			}
			if ($groupfunc['member_dec']) {
				if (!$groupfunc['simulate']) {
					GroupAction::send_group_msg($json['self_id'], $json['group_id'], $str . "\n" . '当前群人数：' . $number . '人');
				} else {
					GroupAction::send_group_msg($json['self_id'], $json['group_id'], '慢走不送');
				}
			}
			\app\v1\logic\GroupLogic::group_user_limit_check($json['self_id'], $json['group_id'], $number);
		}
		if ($sub_type == 'kick_me') {
			\app\v1\model\GroupInfoModel::api_delete($json['group_id']);
			\app\v1\model\GroupMemberModel::api_delete($json['group_id']);
		}
		\app\v1\model\GroupMemberModel::api_delete_byUserId($json['group_id'], $json['user_id']);
	}

	public static function group_member_inc($json) {
		$user_id = $json['user_id'];
		$self_id = $json['self_id'];
		$group_id = $json['group_id'];
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($json['group_id']);
		$number = \app\v1\model\GroupMemberModel::api_count_group($json['group_id']);
		$ret = GroupAction::get_group_member_info($json['self_id'], $json['group_id'], $json['user_id'], true);
		$at = '[CQ:at,qq=' . $json['user_id'] . ']';
		$json['groupinfo'] = $group_info = GroupAction::_get_group_info($json['self_id'], $json['group_id']);
		if (array_key_exists($json['user_id'], bots())) {
			if (bots()[$json['user_id']]['rank'] > bots()[$json['self_id']]['rank']) {
				if (GroupAction::set_group_leave($json['user_id'], $json['group_id'])) {
					return true;
				}
			}
		}
		if ($number > 1 && !array_key_exists($json['user_id'], bots())) {
			$str = '';
			$welcom = $groupfunc['welcome'];
			if ($groupfunc['member_inc']) {
				if (!$groupfunc['simulate']) {
					$str .= $ret['nickname'] . '加入本群，' . "\n" . '当前人数：' . ($number + 1) . '人';
					echo $str;
					GroupAction::send_group_msg($json['self_id'], $json['group_id'], $str);
				}
			}
			$slience = true;
			if ($groupfunc['protect_level']) {
				$welcom = false;
				$user_info = \app\v1\action\PrivateAction::_get_vip_info($json['self_id'], $json['user_id']);
				$user_info['level'] = 1;
				$adm = \app\v1\logic\GroupLogic::get_admins($json);
				$pvb = $groupfunc['onekeypass'] && \app\v2\logic\PowerLogic::privilage_bot($json['self_id']);
				if ($pvb) {
					$rand = rand(100000, 999999);
				} else {
					$rand = rand(1000, 9999);
				}
				$json['code'] = $rand;
				cache('__code__' . $json['user_id'], $json, 86400 * 28);
				cache('__code_int__' . $json['code'], $json, 86400 * 28);
				if (isset($user_info['level'])) {
					if (($user_info['level'] < $groupfunc['level_limit']) && ($user_info['level_speed'] <= 1)) {
						dump('protectlvl' . $json['user_id']);
						$slience = false;
						GroupAction::set_group_ban($json['self_id'], $json['group_id'], $json['user_id'], 86400 * 30, FALSE);
						if ($pvb) {
							PrivateAction::send_private_msg($json['self_id'], $json['user_id'], date('Y-m-d H:i:s', time()) . "\n" . '-' . '如果需要在群中发言，请使用一键验证：' . "\n" . 'http://verify.tuuz.cc:81/verify/' . $json['code']);
						} else {
							if (($user_info['level'] >= 25) || ($groupfunc['level_limit'] < 10)) {
								if ($groupfunc['simulate']) {
									GroupAction::send_group_msg($json['self_id'], $json['group_id'], '新人你把这个code' . $json['code'] . '发给我');
//								PrivateAction::send_private_msg($json['self_id'], $json['user_id'], '只是例行公事，发code+数字就好，发给我一会给你解禁' . '-' . date('Y-m-d H:i:s', time()));
								} else {
									GroupAction::send_group_msg($json['self_id'], $json['group_id'], $at . ' 如果您不是广告号，请私聊我并向我发送:' . "\n" . '--------------' . "\n" . 'code' . $json['code'] . "\n" . '--------------' . "\n" . '用户等级:' . $user_info['level'] . '如有必要可手动解禁' . "\n帐号:$json[user_id]" . "\n" . $adm);
//								PrivateAction::send_private_msg($json['self_id'], $json['user_id'], "请向我发送群中以code开头的验证码来完成解禁 \n解禁码有效期仅为1小时 \n解禁码过期后仅能通过群管理手动解禁，请尽快验证" . '-' . date('Y-m-d H:i:s', time()));
								}
							} else {
								if ($groupfunc['simulate']) {
									GroupAction::send_group_msg($json['self_id'], $json['group_id'], '新加群的这位朋友你把code' . $json['code'] . '发给我');
//								PrivateAction::send_private_msg($json['self_id'], $json['user_id'], '我看你等级比较低，不知道是不是发广告的，发code+数字就好，发给我一会给你解禁' . '-' . date('Y-m-d H:i:s', time()));
								} else {
									GroupAction::send_group_msg($json['self_id'], $json['group_id'], $at . ' 您的等级过低，请私聊我并向我发送:' . "\n" . '--------------' . "\n" . 'code' . $json['code'] . "\n" . '--------------');
//								PrivateAction::send_private_msg($json['self_id'], $json['user_id'], "请向我发送群中以code开头的验证码来完成解禁 \n解禁码有效期仅为1小时 \n解禁码过期后仅能通过群管理手动解禁，请尽快验证" . '-' . date('Y-m-d H:i:s', time()));
								}
							}
						}
					} elseif ($groupfunc['welcome']) {
						$welcom = true;
					}
				} else {
					$slience = false;
					self::group_verify($json, $groupfunc, $at);
				}
			}
			if ($slience) {
				self::slience_add($self_id, $group_id, $user_id, $groupfunc);
			}
			if ($welcom) {
				if ($groupfunc['join_ban']) {
					GroupAction::set_group_ban($json['self_id'], $json['group_id'], $json['user_id'], $groupfunc['join_ban_time'], FALSE);
					if (!empty($groupfunc['join_ban_word'])) {
						if ($groupfunc['simulate']) {
							\app\v1\logic\GroupLogic::logic_send_rand_welcome($json);
						} else {
							if ($groupfunc['private_welcome']) {
								PrivateAction::send_private_msg($json['self_id'], $json['user_id'], $groupfunc['join_ban_word'] . '-' . date('Y-m-d H:i:s', time()));
							} else {
								GroupAction::send_group_msg($json['self_id'], $json['group_id'], $at . $groupfunc['join_ban_word'] . '-' . date('Y-m-d H:i:s', time()));
							}
						}
					} else {
						if ($groupfunc['simulate']) {
							\app\v1\logic\GroupLogic::logic_send_rand_welcome($json);
						} else {
							if ($groupfunc['private_welcome']) {
								PrivateAction::send_private_msg($json['self_id'], $json['user_id'], $groupfunc['join_ban_word'] . '-' . date('Y-m-d H:i:s', time()));
							} else {
								GroupAction::send_group_msg($json['self_id'], $json['group_id'], $at . $groupfunc['join_ban_word'] . '-' . date('Y-m-d H:i:s', time()));
							}
						}
					}
				} elseif ($groupfunc['welcome']) {
					if ($groupfunc['simulate']) {
						\app\v1\logic\GroupLogic::logic_send_rand_welcome($json);
					} else {
						if ($groupfunc['private_welcome']) {
							PrivateAction::send_private_msg($json['self_id'], $json['user_id'], $groupfunc['word'] . '-' . date('Y-m-d H:i:s', time()));
						} else {
							GroupAction::send_group_msg($json['self_id'], $json['group_id'], $at . $groupfunc['word'] . '-' . date('Y-m-d H:i:s', time()));
						}
					}
				}
			}
			if ($groupfunc['card_change']) {
				\app\v2\logic\CardLogic::change_card($json, $groupfunc);
			}
		} else {
			if (GroupAction::get_group_member_list($json['self_id'], $json['group_id'], true)) {
				//
				sleep(3);
				self::group_member_inc($json);
			}
		}
//		dump($group_info);
		\app\v1\logic\GroupLogic::group_user_limit_check($json['self_id'], $json['group_id'], $group_info['member_count']);
	}

	public static function slience_add($self_id, $group_id, $user_id, $groupfunc = null, $notice = false) {
		if (!$groupfunc) {
			$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($group_id);
		}
		$slience = $groupfunc['slience'];
		if ($slience) {
			dump('slience' . $user_id);
			$slience_arr = cache('__slience__');
			if (!$slience_arr) {
				$slience_arr = [];
				cache('__slience__', $slience_arr, 3600);
			}
			$slience_arr[$user_id] = [];
			$slience_arr[$user_id]['self_id'] = $self_id;
			$slience_arr[$user_id]['group_id'] = $group_id;
			$slience_arr[$user_id]['join_time'] = time();
			$slience_arr[$user_id]['next_alert'] = time() + abs($groupfunc['slience_time'] * 60) - 600;
			$slience_arr[$user_id]['alert'] = true;
			$slience_arr[$user_id]['msg_count'] = 0;
			$slience_arr[$user_id]['to_time'] = time() + abs($groupfunc['slience_time'] * 60);
			dump('sli_add' . $user_id);
			cache('__slience__', $slience_arr, 86400 * 30);
			if ($notice) {
				\app\v1\logic\GroupLogic::send_msg2($self_id, $group_id, $user_id, \app\v1\logic\GroupLogic::get_at($user_id) . '加群后勤尽快发言');
			}
		}
	}

	public static function transform_to_emoji($user_level) {
		$str = '';
		$s1 = floor($user_level / 16);
		for ($index = 1; $index < $s1; $index++) {
			$str .= '1';
		}
		$s2 = floor($user_level % 16 / 4);
		for ($index1 = 1; $index1 < $s2; $index1++) {
			$str .= '2';
		}
		$s3 = floor($user_level % 16 % 4);
		for ($index2 = 1; $index2 < $s3; $index2++) {
			$str .= '3';
		}
		return $str;
	}

	public static function group_verify($json, $groupfunc, $at) {
		return self::verify_code($json['self_id'], $json['group_id'], $json['self_id'], $json['code'], $groupfunc, $at);
	}

	public static function verify_code($self_id, $group_id, $user_id, $code = null, $groupfunc = null, $at = null) {
		if (!$at) {
			$at = '[CQ:at,qq=' . $user_id . ']';
		}
		if (!$groupfunc) {
			$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($group_id);
		}
		$groupinfo = \app\v1\model\GroupInfoModel::api_find($group_id);
		if (!$code) {
			$code = rand(1000, 9999);
			$json = [
				'user_id' => $user_id,
				'code' => $code,
				'self_id' => $self_id,
				'group_id' => $group_id,
				'groupinfo' => $groupinfo,
			];
			cache('__code__' . $user_id, $json, 86400 * 28);
			cache('__code_int__' . $code, $json, 86400 * 28);
		}
		GroupAction::set_group_ban($self_id, $group_id, $user_id, 86400 * 30, FALSE);
		if ($groupfunc['onekeypass'] && \app\v2\logic\PowerLogic::privilage_bot($self_id)) {
			PrivateAction::send_private_msg($json['self_id'], $json['user_id'], date('Y-m-d H:i:s', time()) . "\n" . '-' . '如果需要在群中发言，请使用一键验证：' . "\n" . 'http://verify.tuuz.cc:81/verify/' . $code);
		} else {
			if ($groupfunc['simulate']) {
				GroupAction::send_group_msg($self_id, $group_id, $at . '新加群的这位朋友你把"code' . $json['code'] . '"发给我');
//				PrivateAction::send_private_msg($self_id, $user_id, date('Y-m-d H:i:s', time()) . '-' . "code那个发给我，我一会给你解");
			} else {
				GroupAction::send_group_msg($self_id, $group_id, $at . '请私聊我并向我发送:' . "\n" . '--------------' . "\n" . 'code' . $json['code'] . "\n" . '--------------');
//				PrivateAction::send_private_msg($self_id, $user_id, date('Y-m-d H:i:s', time()) . '-' . "请向我发送群中以code开头的验证码来完成解禁 \n解禁码有效期仅为1小时 \n解禁码过期后仅能通过群管理手动解禁，请尽快验证");
			}
		}
	}

	public static function group_change_admin($json) {
		if (isset($json['user_id']) && isset($json['group_id'])) {
			$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($json['group_id']);
			$at = "[CQ:at,qq=$json[user_id]]";
			switch ($json['sub_type']) {
				case 'set':
					echo 'set';
					if (GroupAction::get_group_member_info($json['self_id'], $json['group_id'], $json['user_id'], true)) {
						if (!$groupfunc['simulate']) {
							if (!array_key_exists($json['user_id'], bots())) {
								GroupAction::send_group_msg($json['self_id'], $json['group_id'], $at . '恭喜上位~');
							} else {
								GroupAction::get_group_member_info($json['self_id'], $json['group_id'], $json['self_id'], true, 'admin');
								GroupAction::send_group_msg($json['self_id'], $json['group_id'], '权限已授予');
							}
						}
					}
					break;

				case 'unset':
					echo 'unset';
					if (GroupAction::get_group_member_info($json['self_id'], $json['group_id'], $json['user_id'], true)) {
						if (!$groupfunc['simulate']) {
							if (!array_key_exists($json['user_id'], bots())) {
								GroupAction::send_group_msg($json['self_id'], $json['group_id'], '管理列表刷新');
							} else {
								GroupAction::get_group_member_info($json['self_id'], $json['group_id'], $json['self_id'], true, 'member');
								GroupAction::send_group_msg($json['self_id'], $json['group_id'], '权限回收');
							}
						}
					}
					break;

				default:
					echo $json['sub_type'];
					break;
			}
		}
	}

	public static function group_other_change($json) {
		\app\v1\logic\GroupLogic::logic_refresh_members_num($json, true);
	}

	public static function group_ban_change($json) {
		$message = $json['message'];
		if (preg_match_all('/\([0-9]+\)/', $message, $qq_arr)) {
			$arr = preg_split('/\([0-9]{6,12}\)\s/', $message);
			$end = end($qq_arr);
			$end = end($end);
			$user_id = str_replace(')', '', str_replace('(', '', $end));
			$right = end($arr);
			$name = implode('', array_diff($arr, [$right]));
		} else {
			$arr = explode(' ', $message);
			$left = $arr[0];
			$arr2 = explode('(', $left);
			$name = $arr2[0];
			$user_id = str_replace(')', '', $arr2[1]);
			$right = $arr[1];
		}
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($json['group_id']);
		if (strstr($right, '解除禁言')) {
			if (\app\v1\model\GroupBanPermenentModel::api_delete($json['group_id'], $user_id)) {
				if (!$groupfunc['simulate'] || !array_key_exists($json['user_id'], bots())) {
					GroupAction::send_group_msg($json['self_id'], $json['group_id'], $name . '已经脱离永久小黑屋');
				}
			}
		} else {
			if (strstr($right, '29天')) {
				if (\app\v1\model\GroupBanPermenentModel::api_insert($json['self_id'], $json['group_id'], $user_id, time() + 86400 * 29)) {
					if (!$groupfunc['simulate'] || !array_key_exists($json['user_id'], bots())) {
						GroupAction::send_group_msg($json['self_id'], $json['group_id'], $name . '被送入永久小黑屋');
					}
				}
			} elseif (strstr($right, '1月')) {
				if (\app\v1\model\GroupBanPermenentModel::api_insert($json['self_id'], $json['group_id'], $user_id, time() + 86400 * 30)) {
					if (!$groupfunc['simulate'] || !array_key_exists($json['user_id'], bots())) {
						GroupAction::send_group_msg($json['self_id'], $json['group_id'], $name . '被送入永久小黑屋');
					}
				}
			}
		}
	}

	public static function group_ban_change_notice($json) {
		$user_id = $json['user_id'];
		$group_id = $json['group_id'];
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($json['group_id']);
		$user = \app\v1\model\GroupMemberModel::api_find($group_id, $user_id);
		if ($user) {
			$name = $user['nickname'];
		} else {
			$user = $user_id;
		}
		if ($json['sub_type'] == 'ban') {
			if ($json['duration'] > 2505000) {
				if (\app\v1\model\GroupBanPermenentModel::api_insert($json['self_id'], $json['group_id'], $user_id, time() + 86400 * 29)) {
					if (!$groupfunc['simulate'] || !array_key_exists($json['user_id'], bots())) {
						GroupAction::send_group_msg($json['self_id'], $json['group_id'], $name . '被送入永久小黑屋', 0, 0, 1);
					}
				}
			}
		} elseif ($json['sub_type'] == 'lift_ban') {
			if (\app\v1\model\GroupBanPermenentModel::api_delete($json['group_id'], $user_id)) {
				if (!$groupfunc['simulate'] || !array_key_exists($json['user_id'], bots())) {
					GroupAction::send_group_msg($json['self_id'], $json['group_id'], $name . '已经脱离永久小黑屋', 0, 0, 1);
				}
			}
		}
	}

}
