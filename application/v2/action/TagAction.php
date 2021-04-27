<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\action;

use app\v1\action\GroupAction;
use app\v2\model\GroupTagModel;

/**
 * Description of TagAction
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class TagAction {

	//put your code here
	public static function OnTag($json, $mess, $groupfunc, $own) {
		$group_id = $json['group_id'];
		$user_id = $json['user_id'];
		$self_id = $json['self_id'];
		$message = preg_filter('/^tag/', '', $mess);
		$arr = explode('=', $message);
		if (count($arr) > 2) {
			$temp0 = '';
			for ($index = 1; $index <= count($arr); $index++) {
//			dump($arr[$index]);
				$temp0 .= $arr[$index];
				unset($arr[$index]);
			}
			$arr[1] = $temp0;
		}

		if (isset($arr[0]) && isset($arr[1])) {
			if (substr($arr[1], 0, 1) == '-') {
				$right = $arr[1];
			} else {
				if (preg_match('/[0-9]+/', $arr[1], $arr1)) {
					$right = current($arr1);
				} else {
					$right = $arr[1];
				}
			}
			$exist = GroupTagModel::api_find($group_id, $user_id, $arr[0], $right);
			$count = GroupTagModel::api_count($group_id, $user_id);
			if ($right == '--') {
				if (GroupTagModel::api_delete($group_id, $user_id, $arr[0])) {
					\app\v1\logic\GroupLogic::send_msg($json, $arr[0] . '小组已删除');
				} else {
					\app\v1\logic\GroupLogic::send_msg($json, $arr[0] . '小组为空');
				}
			} elseif (substr($right, 0, 1) == '-') {
				$temp = substr($right, 1);
				if (GroupTagModel::api_delete_single($group_id, $user_id, $arr[0], $temp)) {
					\app\v1\logic\GroupLogic::send_msg($json, $temp . '已从' . $arr[0] . '中删除');
				} else {
					\app\v1\logic\GroupLogic::send_msg($json, $temp . '不在此分组中');
				}
			} else {
				$rr = substr($arr[0], 1);
//				dump(substr($arr[0], 0, 3));
				if (substr($arr[0], 0, 1) == '#') {
					if ($groupfunc['tag_open'] || $own['role'] == 'admin' || $own['role'] == 'owner') {
						$ret = GroupTagModel::api_select($group_id, $user_id, $rr);
						if ($ret) {
							$ui = \app\v1\model\GroupMemberModel::api_find($group_id, $user_id);
							if (strlen($ui['card']) > 3) {
								$name = $ui['card'];
							} else {
								$name = $ui['nickname'];
							}
							$str2 = $name . "对[$rr]组说：\n-----------------\n" . $right . "\n-----------------\n";
							foreach ($ret as $value) {
								$str2 .= "[CQ:at,qq=$value[qq]]";
							}
							GroupAction::send_group_msg($self_id, $group_id, $str2);
						} else {
							\app\v1\logic\GroupLogic::send_msg($json, '没找到这个分组');
						}
					} else {
						\app\v1\logic\GroupLogic::send_msg($json, '管理员并未开放本权限给普通群成员');
					}
				} elseif ((substr($arr[0], 0, 1) == ':' ) || (substr($arr[0], 0, 3) == '：')) {
					if (substr($arr[0], 0, 3) == '：') {
						$rr = substr($arr[0], 3);
					}
					if ($groupfunc['tag_private'] || $own['role'] == 'admin' || $own['role'] == 'owner') {
						$ret = GroupTagModel::api_select($group_id, $user_id, $rr);
						if ($ret) {
							$ui = \app\v1\model\GroupMemberModel::api_find($group_id, $user_id);
							$gp = \app\v1\model\GroupInfoModel::api_find($group_id);
							if (strlen($ui['card']) > 3) {
								$name = $ui['card'];
							} else {
								$name = $ui['nickname'];
							}
							foreach ($ret as $value) {
								\app\v1\action\PrivateAction::send_private_msg($self_id, $value['qq'], $name . "[$user_id]对您说：\n-----------------\n" . $right . "\n-----------------\n" . "消息来自\n[$gp[group_name]-$group_id]");
							}
							\app\v1\logic\GroupLogic::send_msg($json, "[$rr]列表已经私发");
						} else {
							\app\v1\logic\GroupLogic::send_msg($json, '没找到这个分组');
						}
					} else {
						\app\v1\logic\GroupLogic::send_msg($json, '管理员并未开放本权限给普通群成员');
					}
				} else {
					if ($count <= 100) {
						if (!$exist) {
							if (\app\v1\model\GroupMemberModel::api_find($group_id, $right)) {
								if (\app\v2\model\GroupTagModel::api_insert($group_id, $user_id, $arr[0], $right)) {
									\app\v1\logic\GroupLogic::send_msg($json, '已将' . $right . '添加进' . $arr[0]);
								} else {
									\app\v1\logic\GroupLogic::send_msg($json, '分组添加失败');
								}
							} else {
								\app\v1\logic\GroupLogic::send_msg($json, '群内没有这个成员');
							}
						} else {
							\app\v1\logic\GroupLogic::send_msg($json, '号码已存在，可以用：tag' . $arr[0] . '=-' . $right . '从本组删除');
						}
					} else {
						\app\v1\logic\GroupLogic::send_msg($json, '你的列表已经满了删掉部分数据来添加新的用户');
					}
				}
			}
		} elseif (!empty($arr[0])) {
			if ($arr[0] == 'help') {

			} else {
				$ret = GroupTagModel::api_select($group_id, $user_id, $arr[0]);
				if ($ret) {
					$arr2 = [];
					foreach ($ret as $value) {
						$arr2[] = $value['qq'];
					}
					GroupAction::send_group_msg($self_id, $group_id, "$arr[0]列表包含：\n-----------------------------\n" . implode(',', $arr2) . "\n-----------------------------\n" . "使用“tag#” + “分组名” + “=消息内容”来在群中呼唤你的朋友");
				} else {
					\app\v1\logic\GroupLogic::send_msg($json, '列表为空，可使用“tag”+“分组名=qq号码”，例如“tag聚会组=12345678”来将12345678这个qq号码添加进分组' . "\n" . "使用“tag#” + “分组名” + “=消息内容”来在群中呼唤你的朋友");
				}
			}
		} else {
			$ret = GroupTagModel::api_groupby($group_id, $user_id);
			if ($ret) {
				$arr2 = [];
				foreach ($ret as $value) {
					$arr2[] = $value['tag'];
				}
				GroupAction::send_group_msg($self_id, $group_id, "Tag列表包含：\n-----------------------------\n" . implode(',', $arr2) . "\n-----------------------------\n" . "使用“tag#” + “分组名” + “=消息内容”来在群中呼唤你的朋友");
			} else {
				\app\v1\logic\GroupLogic::send_msg($json, '列表为空，可使用“tag”+“分组名=qq号码”，例如“tag聚会组=12345678”来将12345678这个qq号码添加进分组' . "\n" . "使用“tag:” + “分组名” + “=消息内容”将消息私聊发送给你的好友");
			}
		}
	}

}
