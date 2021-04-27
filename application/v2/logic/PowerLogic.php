<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

use app\v1\action\GroupAction;

/**
 * Description of PowerLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class PowerLogic {

	public static function privilage_bot($self_id) {
		if (bots()[$self_id]['pay'] == true && self::bot_avail($self_id)) {
			return true;
		} else {
			return false;
		}
	}

	public static function bot_avail($self_id) {
		if (bots()[$self_id]['end_time'] > time()) {
			return true;
		} else {
			return false;
		}
	}

	//put your code here

	public static function clearout($group_list, $self_id) {
		$arr = [];
		foreach ($group_list as $value) {
			$arr[] = $value['group_id'];
		}
		$sus = implode(',', $arr);
		$arr2 = \app\v1\model\GroupInfoModel::api_release($sus);
		$arr3 = [];
		foreach ($arr2 as $value) {
			$arr3[] = $value['group_id'];
		}
		$group_ids = implode(',', $arr3);
		dump($arr2);
//		\think\Db::startTrans();
//		\app\v1\model\GroupMemberModel::api_deletes($group_ids);
//		\app\v1\model\GroupFunctionOpenModel::api_deletes($group_ids);
//		\app\v1\model\GroupKickModel::api_deletes($group_ids);
//		\app\v1\model\GroupRecieveModel::api_deletes($group_ids);
//		\app\v1\model\GroupRequestModel::api_deletes($group_ids);
//		\app\v1\model\GroupSendModel::api_deletes($group_ids);
//		\app\v1\model\GroupSignModel::api_deletes($group_ids);
//		\app\v2\model\GroupTagModel::api_deletes($group_ids);
//		\app\v1\model\GroupBlackListModel::api_deletes($group_ids);
//		\app\v1\model\GroupAutoreplyModel::api_deletes($group_ids);
//		\app\v1\model\GroupBanModel::api_deletes($group_ids);
//		\app\v1\model\GroupBanPermenentModel::api_deletes($group_ids);
//		\app\v1\model\GroupInfoModel::api_deletes($group_ids);
//		\think\Db::commit();
		echo 'groupclear';
	}

	public static function power($group_list, $self_id) {
		$bot = \app\v1\model\BotSettingsModel::api_find($self_id);
		foreach ($group_list as $val) {
//			dump($val['group_id']);
			$rett = (\app\v1\action\GroupAction::get_group_member_info($self_id, $val['group_id'], $self_id, true));
			if (\app\v1\model\BotBlackListModel::api_find($val['group_id'])) {
				\app\v1\action\GroupAction::send_group_msg($self_id, $val['group_id'], '本群已进入Acfur数据链黑名单，你可以创建新群后再邀请Acfur，本智能姬人数要求是：' . $bot['min_user']);
				\app\v1\action\GroupAction::set_group_leave($self_id, $val['group_id']);
				\app\v1\action\GroupAction::set_group_leave($self_id, $val['group_id'], true);
				echo 'groupblacklist_of_' . $val['group_id'];
				continue;
			}
			$gp = \app\v1\model\GroupInfoModel::api_find($val['group_id']);
			$gps = json_decode($gp['admins'], 1);
			$at = '';
			if (!empty($gps)) {
				foreach ($gps as $value) {
					if ($value['role'] == 'owner') {
						$num = $value['user_id'];
					}
				}

				if ($num) {
					$at = "[CQ:at,qq=$num]";
				}
			}
			$gp = \app\v1\model\GroupMemberModel::api_count_group($val['group_id']);
			if (isset($val['member_count'])) {
				if (($val['member_count'] != 0 && $val['member_count'] < $bot['min_user']) || ($val['member_count'] == 0 && $gp < $bot['min_user'])) {
					\app\v1\action\GroupAction::send_group_msg($self_id, $val['group_id'], $at . '因为我的人数要求是' . $bot['min_user'] . '因为本群人数与我的要求差了' . ($bot['min_user'] - $val['member_count']) . '人，所以您可以更换与您匹配的智能姬呢');
					GroupAction::set_group_leave($self_id, $val['group_id']);
					echo 'member_count_not_right_at_' . $val['group_id'];
					continue;
				} elseif (($val['member_count'] != 0 && $val['member_count'] > $bot['max_user']) || ($val['member_count'] == 0 && $gp > $bot['max_user'])) {
					\app\v1\action\GroupAction::send_group_msg($self_id, $val['group_id'], $at . '因为我的人数要求是' . $bot['max_user'] . '因为本群人数超过上限' . ($bot['min_user'] - $val['member_count']) . '人，所以您可以更换与您匹配的智能姬呢');
					GroupAction::set_group_leave($self_id, $val['group_id']);
					echo 'member_count_not_right_at_' . $val['group_id'];
					continue;
				}
			} else {
				echo $val['group_id'] . '=' . '0' . "\n";
			}
			if ($rett['role'] != 'admin') {
				echo $val['group_id'], $at, "\n";
				$count = cache('__quit2__' . $val['group_id']);
				if ($rett['role'] == 'owner') {
					$admins = \app\v1\model\GroupMemberModel::api_select_admins($val["group_id"]);
					foreach ($admins as $value) {
						GroupAction::set_group_admin($self_id, $val["group_id"], $value["user_id"], false);
					}
					GroupAction::set_group_whole_ban($self_id, $val['group_id'], true);
					\app\v1\action\GroupAction::set_group_leave($self_id, $val['group_id'], true);
					return;
				}
				if (!$count) {
					$count = 1;
				} else {
					$count++;
				}
				if ($count == 2) {
					echo "-01";
					$code = \app\v1\action\GroupAction::send_group_msg($self_id, $val['group_id'], $at . '有成员邀请Acfur加入，使用：acfurhelp，来显示我的简介，我只负责群管理不负责陪聊，有需要可以设定管理(๑•ᴗ•๑)');
					if ($code == -34) {
						\app\v1\action\GroupAction::set_group_leave($self_id, $val['group_id']);
						\app\v1\model\GroupInfoModel::api_delete($val['group_id']);
						cache('__quit2__' . $val['group_id'], 1, 1);
					} else {
						cache('__quit2__' . $val['group_id'], $count, 4000);
					}
				} elseif ($count == 3) {
					echo "-12";
					$num = \app\v1\model\GroupInfoModel::api_count();
					$code = \app\v1\action\GroupAction::send_group_msg($self_id, $val['group_id'], $at . '机器人没有超管权限没有总后台，所有操作均由管理员配置，如果在使用中有任何问题可以向我们反馈：542749156');
					if ($code == -34) {

						\app\v1\action\GroupAction::set_group_leave($self_id, $val['group_id']);
						\app\v1\model\GroupInfoModel::api_delete($val['group_id']);
						cache('__quit2__' . $val['group_id'], 1, 1);
					} else {
						cache('__quit2__' . $val['group_id'], $count, 4000);
					}
				} elseif ($count == 4) {
					echo "-13";
					$code = \app\v1\action\GroupAction::send_group_msg($self_id, $val['group_id'], $at . '群主如果以后有需要这样的机器人，可以直接拉我就是');
					if ($code == -34) {
						\app\v1\action\GroupAction::set_group_leave($self_id, $val['group_id']);
						\app\v1\model\GroupInfoModel::api_delete($val['group_id']);
						cache('__quit2__' . $val['group_id'], 1, 1);
					} else {
						cache('__quit2__' . $val['group_id'], $count, 4000);
					}
				} elseif ($count > 2) {
					echo "-17";
					$num = \app\v1\model\GroupInfoModel::api_count();
					$code = \app\v1\action\GroupAction::send_group_msg($self_id, $val['group_id'], 'Acfur云机器人是群管理机器人，以“不干扰正常聊天”为设计思路，已经有' . ($num * 2) . '个群在使用，有任何问题或者功能需求可以加开发群：542749156');
					\app\v1\model\GroupInfoModel::api_delete($val['group_id']);
					\app\v1\action\GroupAction::set_group_leave($self_id, $val['group_id']);

					cache('__quit2__' . $val['group_id'], 1, 1);
				}
			}
		}
	}

}
