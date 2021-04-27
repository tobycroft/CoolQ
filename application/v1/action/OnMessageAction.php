<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\action;

class OnMessageAction {

	public static function private_chat_friend($json) {

		\app\v1\model\PrivateRecieveModel::api_insert($json['self_id'], 'friend', $json['message_id'], $json['user_id'], $json['message'], $json['raw_message'], $json['font'], $json['time']);
		$self_id = $json['self_id'];
		$message = $json['message'];
		$raw_message = $json['message'];
		$user_id = $json['user_id'];
		$sender = $json['sender'];
		$priv_bot = \app\v2\logic\PowerLogic::privilage_bot($json['self_id']);
		if (preg_match('/^acfur.*/i', $json['message'])) {
			$message = preg_filter('/^acfur/i', '', $json['message']);
			dump($message);
			dump('原生消息-' . $raw_message);
			$json['message'] = preg_filter('/^acfur/i', '', $message);
			switch ($message) {
				case 'help':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\PrivateLogic::logic_help($json);
						break;
					}

				case 'status':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\PrivateLogic::logic_status($json);
						break;
					}

				case 'info':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\PrivateLogic::logic_info($json);
						break;
					}

				case 'dev':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\PrivateLogic::logic_dev($json);
						break;
					}

				case 'cmd':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\PrivateLogic::logic_command($json);
						break;
					}

				case 'safe':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\PrivateLogic::logic_safe($json);
						break;
					}
				case 'gogogo':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\PrivateLogic::logic_detail($json);
						break;
					}

				case 'app':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\PrivateLogic::logic_app($json);
						break;
					}

				default:
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v1\logic\PrivateLogic::logic_sayHello($json, $priv_bot);
						break;
					}
			}
		} elseif (preg_match('/^code/i', $json['message'])) {
			$json['message'] = preg_filter('/^code/', '', $json['message']);
			$code = cache('__code__' . $json['user_id']);
			if ($code) {
				if ($code['code'] == trim($json['message'])) {
					cache('__code__' . $json['user_id'], false, 1);
					$groupinfo = \app\v1\model\GroupInfoModel::api_find($code['group_id']);
					\app\v1\logic\PrivateLogic::logic_unlock($json, $code, "您在   [$groupinfo[group_name]]   已经解除小黑屋");
				} else {
					PrivateAction::send_private_msg($json['self_id'], $json['user_id'], '不对呢');
				}
			} elseif ($json['message'] == '94537310') {
				$rand = rand(1000, 9999);
				$json['code'] = $rand;
				$json['group_id'] = '94537310';
				cache('__code__' . $json['user_id'], $json, 7200);
				echo $json['code'];
				PrivateAction::send_private_msg($json['self_id'], $json['user_id'], '验证码：' . $json['code']);
			} else {
				PrivateAction::send_private_msg($json['self_id'], $json['user_id'], '验证码都不存在验证个P');
			}
		} elseif ($json['message'] == '登录') {
			\app\v2\logic\LoginLogic::loc_request_login($json);
		} elseif ($json['message'] == '登陆' || $json['message'] == '登入') {
			PrivateAction::send_private_msg($json['self_id'], $json['user_id'], $json['message'] . '？你要去攻占钓鱼岛吗？如需获取登录码，请输入“登录”');
		} elseif ($json['message'] == 'app' || $json['message'] == '下载地址') {
			\app\v2\logic\PrivateLogic::logic_app($json);
		} elseif ($json['message'] == '帮助') {
			\app\v2\logic\PrivateLogic::logic_help($json);
		} elseif ($json['message'] == '积分查询') {
			\app\v2\logic\PrivateLogic::log_lottery_check($json);
		}
	}

	public static function group_chat_normal($json) {
		\app\v1\model\GroupRecieveModel::api_insert($json['self_id'], 'normal', $json['message_id'], $json['group_id'], $json['user_id'], $json['message'], $json['raw_message'], $json['font'], $json['time']);
		$str = '';
		$self_id = $json['self_id'];
		$message = $json['message'];
		$raw_message = $json['message'];
		$group_id = $json['group_id'];
		$user_id = $json['user_id'];
		$sender = $json['sender'];
		$nickname = $sender['nickname'];
		$card = $sender['card'];
		$role = $sender['role'];

		$priv_bot = \app\v2\logic\PowerLogic::privilage_bot($self_id);
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($group_id);
		$self_info = \app\v1\model\GroupMemberModel::api_find_bySelfid($self_id, $group_id, $self_id);
		dump('机器人权限-' . $self_info['role']);
		$is_admin = false;
		if ($self_info['role'] == 'admin') {
			$is_admin = true;
		}
		if ($self_info['role'] == 'owner') {
			$is_admin = true;
			GroupAction::set_group_whole_ban($json['self_id'], $json['group_id'], true);
			if ($role == 'admin') {
				GroupAction::set_group_ban($self_id, $group_id, $user_id, 2592000, false);
			}
		}
		if (\app\v1\model\BotBlackListModel::api_find($group_id)) {
			echo 'blacklist_of_bots_sql';
			GroupAction::send_group_msg($self_id, $group_id, '本群在Acfur高危数据库中，无法使用AcfurBOT，如为误报请联系开发群：542749156', null, true);
			\app\v1\action\GroupAction::set_group_leave($self_id, $group_id, true);
			\app\v1\action\GroupAction::set_group_leave($self_id, $group_id);
		}
		dump($is_admin);
		unset($groupfunc['group_id'], $groupfunc['id']);
		dump('原生消息-' . $raw_message);
		if ($groupfunc['card_unblank']) {
			if (strlen($json['sender']['nickname']) < 2) {
				if (strlen($json['sender']['card']) < 2) {
					\app\v2\logic\CardLogic::change_card($json, $groupfunc);
				}
			}
		}
		if (preg_match('/^acfur.*/i', $message)) {
			$send = cache('__send__' . $json['user_id']);
			if ($send > 1) {
				\app\v1\logic\GroupLogic::logic_ban_user($json, '干扰判定', 86400);
				cache('_norep1_' . $json['user_id'], true, 86400);
			}
			if (!$send) {
				$send = 1;
			} else {
				$send++;
			}
			cache('__send__' . $json['user_id'], $send, 1);

			$message = preg_filter('/^acfur/i', '', $message);
			switch ($message) {
				case 'help':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\GroupLogic::logic_help($json);
					}
					break;
				case 'status':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\GroupLogic::logic_status($json);
					}
					break;
				case 'info':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\GroupLogic::logic_info($json);
					}
					break;
				case 'dev':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\GroupLogic::logic_dev($json);
						break;
					}
				case 'cmd':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\GroupLogic::logic_command($json);
					}
					break;
				case 'safe':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\GroupLogic::logic_safe($json);
					}
					break;
				case 'gogogo':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\GroupLogic::logic_detail($json);
					}
					break;
				case 'app':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v2\logic\GroupLogic::logic_app($json);
						break;
					}

				case '积分':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v1\logic\PrivateLogic::balance_check_single($json);
					}
					break;

				case '人数':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v1\logic\GroupLogic::logic_group_members_num($json);
					}
					break;

				case '当前人数':
					if (!cache('_norep1_' . $json['user_id'])) {
						\app\v1\logic\GroupLogic::logic_group_members_num($json);
						break;
					}

				case '权限':
					if (!cache('_norep1_' . $json['user_id'])) {
						GroupAction::send_group_msg($self_id, $group_id, 'Acfur现在是' . $self_info['role']);
						break;
					}

				case '权限':
					if (!cache('_norep1_' . $json['user_id'])) {
						GroupAction::send_group_msg($self_id, $group_id, 'Acfur现在是' . $self_info['role']);
						break;
					}

				case '刷新权限':
					if (!cache('_norep1_' . $json['user_id'])) {
						$self_info = GroupAction::get_group_member_info($self_id, $group_id, $user_id, true);
						if (isset($self_info['role'])) {
							GroupAction::send_group_msg($self_id, $group_id, '自我权限刷新完毕，Acfur-' . $self_info['role']);
						} else {
							GroupAction::send_group_msg($self_id, $group_id, '自我权限刷新失败');
						}
						break;
					}

				case '清除部分用户':
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'owner') {
						\app\v2\logic\CleanoutLogic::loc_clear($json);
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}

				case '刷新人数':
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'admin' || $own['role'] == 'owner') {
						\app\v1\logic\GroupLogic::logic_refresh_members_num($json);
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}
					break;

				case '强制刷新人数':
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'owner') {
						\app\v1\logic\GroupLogic::logic_refresh_members_num($json, true);
					} else {
						self::send_msg($json, '只有群主能执行本操作呢~' . rand(0, 9999));
					}
					break;

				case '强制设定为管理员':
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'owner') {
						$self_info = GroupAction::get_group_member_info($self_id, $group_id, $user_id, true, 'admin');
						if ($self_info['role'] == 'admin') {
							GroupAction::send_group_msg($self_id, $group_id, '自我权限刷新完毕，Acfur-' . 'admin');
						} else {
							GroupAction::send_group_msg($self_id, $group_id, '自我权限刷新失败');
						}
						break;
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}
					break;

				case '全开！':
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'admin' || $own['role'] == 'owner') {
						if ($is_admin) {
							\app\v1\logic\GroupLogic::l_turn_all_on($json);
						} else {
							GroupAction::send_group_msg($self_id, $group_id, '请先设定我为管理员');
						}
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}
					break;

				case '全关！':
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'admin' || $own['role'] == 'owner') {
						\app\v1\logic\GroupLogic::l_turn_all_off($json);
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}
					break;

				case '更新群信息':
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'admin' || $own['role'] == 'owner') {
						if (GroupAction::_get_group_info($json['self_id'], $json['group_id'])) {
							GroupAction::send_group_msg($self_id, $group_id, '群信息更新完毕');
						} else {
							GroupAction::send_group_msg($self_id, $group_id, '群信息更新失败');
						}
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}
					break;



				case '清除小黑屋':
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'admin' || $own['role'] == 'owner') {
						\app\v1\logic\GroupLogic::l_clear($json);
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}
					break;

				case '清除黑名单':
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'admin' || $own['role'] == 'owner') {
						\app\v1\logic\GroupLogic::l_black_clear($json);
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}
					break;

				case '小黑屋':
					\app\v1\logic\GroupLogic::logic_show_black($json);
					break;

				case '设定':
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'admin' || $own['role'] == 'owner') {
						if (!$is_admin) {
							GroupAction::send_group_msg($self_id, $group_id, '请先设定我为管理员');
							break;
						}
						foreach ($groupfunc as $key => $value) {
							if ($value != '1' || $value != '0') {
								if (!$priv_bot) {
									$value = '请使用acfurapp查看';
								}
							}
							if ($value == '1') {
								$value = '开';
							} elseif ($value == '0') {
								$value = '关';
							}
							$str .= \app\v1\model\FunctionDetailModel::api_find_v($key) . '：' . $value . "\n";
						}
						$str .= '-----------------------------------------------' . "\n";
						$str .= '使用“acfur!欢迎开关=开”来开启某项功能哦~' . "\n" . '例如，使用“acfur!欢迎语=欢迎加入本群”来设定文字或数字内容';
						$str .= "\n" . '-----------------------------------------------';
//						dump($group_id);
						GroupAction::send_group_msg($self_id, $group_id, $str, false, true);
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}
					break;

				case (preg_match('/^!|^！/i', $message) ? $message : !$message):
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					$message = preg_filter('/^!|^！/', '', $message);
					$arr = explode('=', $message);
					if (isset($arr[0])) {
						if ($own['role'] == 'admin' || $own['role'] == 'owner') {
							if (!$is_admin) {
								GroupAction::send_group_msg($self_id, $group_id, '请先设定我为管理员');
								break;
							}

							$key = \app\v1\model\FunctionDetailModel::api_find_k_all($arr[0]);
							if (isset($key['k']) && isset($arr[1])) {
								$value = $arr[1];
								switch ($key['t']) {
									case 'bool':
										if ($value == '开') {
											$value = 1;
										} elseif ($value == '关') {
											$value = 0;
										} else {
											$value = 1;
											GroupAction::send_group_msg($self_id, $group_id, '本设定只能=开或关');
										}
										if (\app\v1\model\GroupFunctionOpenModel::api_update_manual($group_id, $key['k'], (int) $value)) {
											GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '已更改为：' . $arr[1], false, true);
										} else {
											GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '设定未更改', false, true);
										}
										break;

									case 'string':
										if (\app\v1\model\GroupFunctionOpenModel::api_update_manual($group_id, $key['k'], (string) $value)) {
											GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '已设定为：' . $arr[1], false, true);
										} else {
											GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '设定未更改');
										}
										break;

									case 'int':
										if (strlen((int) $value) >= 1) {
											if (\app\v1\model\GroupFunctionOpenModel::api_update_manual($group_id, $key['k'], (int) $value)) {
												GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '已设定为：' . $arr[1], false, true);
											} else {
												GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '设定未更改', false, true);
											}
										} else {
											GroupAction::send_group_msg($self_id, $group_id, '本设定只能=数字');
										}
										break;

									case 'array':
										if ($value) {
											$bw = json_decode($groupfunc[$key['k']]);
											if (!isset($bw[0])) {
												$bw = [];
											}
											if ($value == '--') {
												$bw = [];
												if (\app\v1\model\GroupFunctionOpenModel::api_update_manual($group_id, $key['k'], json_encode($bw, 320))) {
													GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '已清空', false, true);
												}
											} elseif (preg_match('/^\-/i', $arr[1])) {
												$word = preg_filter('/^\-/', '', $arr[1]);
												$bw = array_diff($bw, [$word]);
												$bw = array_merge($bw, []);
												$imp = implode(',', $bw);
												if (\app\v1\model\GroupFunctionOpenModel::api_update_manual($group_id, $key['k'], json_encode($bw, 320))) {
													if ($priv_bot) {
														GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '删除词语：' . $word . "\n" . '删除后' . $arr[0] . '列表为：' . $imp, false, true);
													} else {
														GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '删除成功，请使用acfurapp查看列表', false, true);
													}
												} else {
													if ($priv_bot) {
														GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '列表为：' . $imp, false, true);
													} else {
														GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '请使用acfurapp查看列表', false, true);
													}
												}
											} else {
												$word = $arr[1];
												if (!in_array($word, $bw)) {
													array_push($bw, $word);
												}
												$imp = implode(',', $bw);
												if (\app\v1\model\GroupFunctionOpenModel::api_update_manual($group_id, $key['k'], json_encode($bw, 320))) {
													if ($priv_bot) {
														GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '添加词语：' . $word . "\n" . '添加后' . $arr[0] . '列表为：' . $imp, false, true);
													} else {
														GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '添加成功，请使用acfurapp查看列表', false, true);
													}
												} else {
													if ($priv_bot) {
														GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '列表为：' . $imp, false, true);
													} else {
														GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '请使用acfurapp查看列表', false, true);
													}
												}
											}
										} else {
											GroupAction::send_group_msg($self_id, $group_id, '如果需要清除屏蔽词/T出词，请使用“acfur!屏蔽词=--”/“acfur!T出词=--”，清除' . "\n" . '如需新增请直接在等号后输入屏蔽词即可，如需删除请使用例如“acfur!屏蔽词=-滚蛋”/“acfur!T出词=-滚蛋”操作删除' . "\n" . '如果需要禁止发图，可以使用acfur!屏蔽词=image来对发图片的人进行惩罚');
										}
										break;

									default:
										break;
								}
							} else {
								switch ($arr[0]) {
									case '屏蔽词':
										$bw = json_decode($groupfunc['ban_words']);
										if (isset($bw[0])) {
											$imp = implode(',', $bw);
											if ($priv_bot) {
												GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '列表为：' . $imp . "\n" . '如需新增请直接在输入例如“acfur!屏蔽词=滚蛋”即可' . "\n" . '如需删除请使用例如“acfur!屏蔽词=-滚蛋”操作删除' . '如果需要禁止发图，可以使用acfur!屏蔽词=image来对发图片的人进行惩罚', false, true);
											} else {
												GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '如需新增请直接在输入例如“acfur!屏蔽词=滚蛋”即可' . "\n" . '如需删除请使用例如“acfur!屏蔽词=-滚蛋”操作删除' . '如果需要禁止发图，可以使用acfur!屏蔽词=image来对发图片的人进行惩罚，可以直接使用acfurapp添加删除哦~', false, true);
											}
										} else {
											if ($priv_bot) {
												GroupAction::send_group_msg($self_id, $group_id, '没有设定屏蔽词' . "\n" . '如需新增请直接在输入例如“acfur!屏蔽词=滚蛋”即可' . "\n" . '如需删除请使用例如“acfur!屏蔽词=-滚蛋”操作删除' . '如果需要禁止发图，可以使用acfur!屏蔽词=image来对发图片的人进行惩罚', false, true);
											} else {
												GroupAction::send_group_msg($self_id, $group_id, '没有设定屏蔽词' . "\n" . '如需新增请直接在输入例如“acfur!屏蔽词=滚蛋”即可' . "\n" . '如需删除请使用例如“acfur!屏蔽词=-滚蛋”操作删除' . '如果需要禁止发图，可以使用acfur!屏蔽词=image来对发图片的人进行惩罚', false, true);
											}
										}
										break;

									case 'T出词' || 't出词':
										$bw = json_decode($groupfunc['kick_words']);
										if (isset($bw[0])) {
											$imp = implode(',', $bw);
											if ($priv_bot) {
												GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '列表为：' . $imp . "\n" . '如需新增请直接在输入例如“acfur!T出词=滚蛋”即可' . "\n" . '如需删除请使用例如“acfur!T出词=-滚蛋”操作删除' . '如果需要T出发图的人，可以使用acfur!T出词=image来对发图片的人进行移除本群的处罚', false, true);
											} else {
												GroupAction::send_group_msg($self_id, $group_id, $arr[0] . '如需新增请直接在输入例如“acfur!T出词=滚蛋”即可' . "\n" . '如需删除请使用例如“acfur!T出词=-滚蛋”操作删除' . '如果需要T出发图的人，可以使用acfur!T出词=image来对发图片的人进行移除本群的处罚，可以直接使用acfurapp添加删除哦', false, true);
											}
										} else {
											if ($priv_bot) {
												GroupAction::send_group_msg($self_id, $group_id, '没有设定T出词' . "\n" . '如需新增请直接在输入例如“acfur!T出词=滚蛋”即可' . "\n" . '如需删除请使用例如“acfur!T出词=-滚蛋”操作删除' . '如果需要禁止发图，可以使用acfur!T出词=image来对发图片的人进行移除本群的处罚', false, true);
											} else {
												GroupAction::send_group_msg($self_id, $group_id, '如需新增请直接在输入例如“acfur!T出词=滚蛋”即可' . "\n" . '如需删除请使用例如“acfur!T出词=-滚蛋”操作删除' . '如果需要禁止发图，可以使用acfur!T出词=image来对发图片的人进行移除本群的处罚，可以直接使用acfurapp添加删除哦', false, true);
											}
										}
										break;

									default:
										$str .= '使用“!命令名称=开或关”来开启某项带有开关性质的功能~' . "\n" . '例如欢迎，刷屏警告等功能 ' . "\n" . '使用“acfur + ! (感叹号) + 命令名称(acfur设定中看到的名称) = (等于) 命令内容(对应开或关，数字或文本)”来设定带有文字性质的内容，例如欢迎语，禁言时长等';
										GroupAction::send_group_msg($self_id, $group_id, $str, false, true);
										break;
								}
							}
						} else {
							\app\v1\logic\GroupLogic::logic_access_denide($json);
						}
					} else {
						GroupAction::send_group_msg($self_id, $group_id, '没有这个功能哦~');
					}
					break;

				case (preg_match('/刚刚发了啥/i', $message) ? $message : !$message):
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'admin' || $own['role'] == 'owner') {
						$message = preg_filter('/刚刚发了啥.*/', '', $message);
						$json['message'] = $message;
						if ($priv_bot) {
							\app\v1\logic\GroupLogic::logic_retract_message($json);
						} else {
							self::send_msg($json, '只有私有机器人支持本功能呢~' . rand(0, 9999));
						}
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}
					break;

				case (preg_match('/刚刚说了啥/i', $message) ? $message : !$message):
					if ($priv_bot) {
						$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
						if ($own['role'] == 'admin' || $own['role'] == 'owner') {
							$message = preg_filter('/刚刚说了啥.*/', '', $message);
							$json['message'] = $message;
							if ($priv_bot) {
								\app\v1\logic\GroupLogic::logic_retract_message($json);
							} else {
								self::send_msg($json, '只有私有机器人支持本功能呢~' . rand(0, 9999));
							}
						} else {
							\app\v1\logic\GroupLogic::logic_access_denide($json);
						}
					}
					break;

				case (preg_match('/^主播/i', $message) ? $message : !$message):
					if ($groupfunc['api_func']) {
						$message = preg_filter('/主播/', '', $message);
						$json['message'] = $message;
						\app\v1\logic\GroupLogic::logic_wegame_search($json);
					}
					break;


				case (preg_match('/^直播/i', $message) ? $message : !$message):
					if ($groupfunc['api_func']) {
						$message = preg_filter('/直播/', '', $message);
						$json['message'] = $message;
						\app\v1\logic\GroupLogic::logic_wegame_search_online($json);
					}
					break;

				case (preg_match('/^#/i', $message) ? $message : !$message):
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'admin' || $own['role'] == 'owner') {
						if (!$is_admin) {
							GroupAction::send_group_msg($self_id, $group_id, '请先设定我为管理员');
							break;
						}

						$message = preg_filter('/^#/', '', $message);
						$arr = explode('==', $message);
						if (isset($arr[0]) && isset($arr[1])) {
							$reply = \app\v1\model\GroupAutoreplyModel::api_find($group_id, $arr[0]);
							$count = \app\v1\model\GroupAutoreplyModel::api_count($group_id);
							if ($arr[1] == '--') {
								if (\app\v1\model\GroupAutoreplyModel::api_clear($group_id)) {
									GroupAction::send_group_msg($self_id, $group_id, '关键词全部清除');
								} else {
									GroupAction::send_group_msg($self_id, $group_id, '没有可供清除的关键词');
								}
							} elseif ($arr[1] == '-') {
								if (\app\v1\model\GroupAutoreplyModel::api_delete($group_id, $arr[0])) {
									if ($priv_bot) {
										GroupAction::send_group_msg($self_id, $group_id, '关键词清除：' . $arr[0]);
									} else {
										GroupAction::send_group_msg($self_id, $group_id, '关键词清除成功');
									}
								} else {
									GroupAction::send_group_msg($self_id, $group_id, '没有可供清除的关键词');
								}
							} else {
								if ($count <= 100) {
									if (!$reply) {
										if (\app\v1\model\GroupAutoreplyModel::api_insert($group_id, $arr[0], $arr[1])) {
											GroupAction::send_group_msg($self_id, $group_id, '关键词回复设定成功');
										} else {
											GroupAction::send_group_msg($self_id, $group_id, '关键词回复设定失败');
										}
									} else {
										GroupAction::send_group_msg($self_id, $group_id, '已存在相同的关键词回复信息，使用“acfur#关键词”查看哦~');
									}
								} else {
									GroupAction::send_group_msg($self_id, $group_id, '关键词太多啦~使用acfur#关键词==-xxxxx来删除某个关键词或者使用acfur#任意文字==--来清除所有关键词');
								}
							}
						} elseif (isset($arr[0])) {
							$ret = \app\v1\model\GroupAutoreplyModel::api_select($group_id);
							if ($ret) {
								$arr = [];
								foreach ($ret as $value) {
									$arr[] = $value['key'];
								}
								GroupAction::send_group_msg($self_id, $group_id, "关键词列表包含：\n-----------------------------\n" . implode(',', $arr) . "\n-------------------------------\n可以使用“acfur#关键词==回复内容”进行添加，\n例如“acfur#钢琴==可以弹奏的一个乐器”\n\n如果要删除所有关键词，\n使用“acfur#任意文字==--”来清除所有关键词，\n\n如果只需要删除一个关键词，\n使用“acfur#钢琴==-”来删除“钢琴”这个关键词回复");
							} else {
								GroupAction::send_group_msg($self_id, $group_id, '暂未设定关键词，可以使用“acfur#关键词==回复内容”进行添加，例如“acfur#钢琴==可以弹奏的一个乐器”');
							}
						} else {

						}
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}
					break;

				case (preg_match('/^:|^：/i', $message) ? $message : !$message):
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($own['role'] == 'admin' || $own['role'] == 'owner') {
						if (!$is_admin) {
							GroupAction::send_group_msg($self_id, $group_id, '请先设定我为管理员');
							break;
						}

						$message = preg_filter('/^:|^：/', '', $message);
						$arr = explode('==', $message);
						if (isset($arr[0]) && isset($arr[1])) {
							$reply = \app\v1\model\GroupAutomsgModel::api_find($group_id, $arr[0]);
							$count = \app\v1\model\GroupAutomsgModel::api_count($group_id);
							if ($arr[1] == '--') {
								if (\app\v1\model\GroupAutomsgModel::api_clear($group_id)) {
									GroupAction::send_group_msg($self_id, $group_id, '自动回复全部清除');
								} else {
									GroupAction::send_group_msg($self_id, $group_id, '没有可供清除的自动回复');
								}
							} elseif ($arr[1] == '-') {
								if (\app\v1\model\GroupAutomsgModel::api_delete($group_id, $arr[0])) {
									if ($priv_bot) {
										GroupAction::send_group_msg($self_id, $group_id, '自动回复清除：' . $arr[0]);
									} else {
										GroupAction::send_group_msg($self_id, $group_id, '自动回复清除成功');
									}
								} else {
									GroupAction::send_group_msg($self_id, $group_id, '没有可供清除的自动回复');
								}
							} else {
								if ($count <= 100) {
									if (!$reply) {
										if (\app\v1\model\GroupAutomsgModel::api_insert($group_id, $arr[0], $arr[1])) {
											GroupAction::send_group_msg($self_id, $group_id, '自动回复回复设定成功');
										} else {
											GroupAction::send_group_msg($self_id, $group_id, '自动回复回复设定失败');
										}
									} else {
										GroupAction::send_group_msg($self_id, $group_id, '已存在相同的自动回复回复信息，使用“acfur:自动回复”查看哦~');
									}
								} else {
									GroupAction::send_group_msg($self_id, $group_id, '自动回复太多啦~使用acfur:自动回复==-xxxxx来删除某个自动回复或者使用acfur:任意文字==--来清除所有自动回复');
								}
							}
						} elseif (isset($arr[0])) {
							$ret = \app\v1\model\GroupAutomsgModel::api_select($group_id);
							if ($ret) {
								$arr = [];
								foreach ($ret as $value) {
									$arr[] = $value['key'];
								}
								GroupAction::send_group_msg($self_id, $group_id, "自动回复列表包含：\n-----------------------------\n" . implode(',', $arr) . "\n-------------------------------\n可以使用“acfur:自动回复==回复内容”进行添加，\n例如“acfur:钢琴==可以弹奏的一个乐器”\n\n如果要删除所有自动回复，\n使用“acfur:任意文字==--”来清除所有自动回复，\n\n如果只需要删除一个自动回复，\n使用“acfur:钢琴==-”来删除“钢琴”这个自动回复回复");
							} else {

								GroupAction::send_group_msg($self_id, $group_id, '暂未设定自动回复，可以使用“acfur:自动回复==回复内容”进行添加，例如“acfur:钢琴==可以弹奏的一个乐器”');
							}
						} else {

						}
					} else {
						\app\v1\logic\GroupLogic::logic_access_denide($json);
					}
					break;


				default:
					echo $message;
					if ($groupfunc['simulate']) {
						\app\v1\logic\GroupLogic::logic_reply($json);
					} else {
						\app\v1\logic\GroupLogic::logic_sayHello($json, $priv_bot);
					}
					break;
			}
		} elseif (preg_match('/^tag/i', $message)) {
			if ($priv_bot) {
				if (!$is_admin) {
					GroupAction::send_group_msg($self_id, $group_id, '请先设定我为管理员');
				} else {
					$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
					if ($groupfunc['tag_open'] || $groupfunc['tag_private'] || $own['role'] == 'admin' || $own['role'] == 'owner') {
						\app\v2\action\TagAction::OnTag($json, $message, $groupfunc, $own);
					} else {
						GroupAction::send_group_msg($self_id, $group_id, '管理员未开放任何公开非公开分组权限');
					}
				}
			} else {
				GroupAction::send_group_msg($self_id, $group_id, '只有非官方机器人才能支持本功能呢');
			}
		} elseif (preg_match('/^奖励/i', $message)) {
			$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
			if ($own['role'] == 'admin' || $own['role'] == 'owner') {
				if (!$is_admin) {
					GroupAction::send_group_msg($self_id, $group_id, '请先设定我为管理员');
				} else {
					\app\v2\logic\DrawLogic::StartDraw($json, $message, $groupfunc, $own);
				}
			} else {
				\app\v1\logic\GroupLogic::send_msg($json, '只有管理员可以设定奖励');
			}
		} elseif (preg_match('/^抽奖/i', $message)) {
			\app\v2\logic\DrawLogic::OnDraw($json);
		} elseif (preg_match('/^开奖/i', $message)) {
			if ($own['role'] == 'admin' || $own['role'] == 'owner') {
				$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
				\app\v2\logic\DrawLogic::StopDraw($json, $message, $groupfunc, $own);
			} else {
				\app\v1\logic\GroupLogic::send_msg($json, '只有管理员可以开奖');
			}
		} else {
			if ($groupfunc['shuapin'] && $is_admin) {
				$send = cache('__send__' . $json['user_id']);
				if ($send > 1) {
					\app\v1\logic\GroupLogic::logic_ban_user($json, '刷屏警告', $groupfunc['ban_time']);
					exit();
				}
				if (!$send) {
					$send = 1;
				} else {
					$send++;
				}
				cache('__send__' . $json['user_id'], $send, 1);
			}

			if ($groupfunc['shuamany'] && $is_admin) {
				$send2 = cache('_msg_' . md5($json['user_id'] . '_' . $message));
				if ($send2 > 1) {
					\app\v1\logic\GroupLogic::logic_ban_user($json, '重复刷屏警告', $groupfunc['ban_time']);
					exit();
				} elseif ($send2 > 0) {
					\app\v1\logic\GroupLogic::send_msg($json, date('Y-m-d H:i:s', time()) . "\n" . '请不要短时间内重复发送相同内容哦');
				}
				if (!$send2) {
					$send2 = 1;
				} else {
					$send2++;
				}
				cache('_msg_' . md5($json['user_id'] . '_' . $message), $send2, 30);
			}

			$umessage = str_replace(array("\r\n", "\r", "\n"), "", $message);
			dump('消息平行-' . $umessage);
			$message = str_replace(' ', '', $umessage);
			$message = str_replace("\n", '', $message);
			$message = str_replace('.', '', $message);
			$message = str_replace('。', '', $message);
			$message = str_replace('，', '', $message);
			$message = str_replace(',', '', $message);
			$message = str_replace('\\', '', $message);
			$message = str_replace('/', '', $message);
			$message = str_replace('-', '', $message);
			$message = str_replace('*', '', $message);
			$message = str_replace('$', '', $message);
			$message = str_replace('#', '', $message);
			$message = str_replace('@', '', $message);
			$message = str_replace('%', '', $message);
			$message = str_replace('^', '', $message);
			$message = str_replace('&', '', $message);
			$message = str_replace('(', '', $message);
			$message = str_replace(')', '', $message);
			$message = str_replace('‘', '', $message);
			$message = str_replace('’', '', $message);
			$message = str_replace('；', '', $message);
			$message = str_replace('：', '', $message);
			$message = str_replace('“', '', $message);
			$message = str_replace('”', '', $message);
			$message = str_replace('|', '', $message);
			$message = str_replace('、', '', $message);
			$message = str_replace('—', '', $message);
			$message = str_replace('+', '', $message);

			dump('处理后消息-' . $message);
			switch ($message) {

				case '签到':
					if ($groupfunc['sign']) {
						\app\v1\logic\GroupLogic::logic_sign($json, $groupfunc);
					}
					break;

				case '轮盘':
					if ($groupfunc['lottery']) {
						\app\v2\logic\BalanceLogic::log_lottery($json, $groupfunc);
					}
					break;

				case '积分查询':
					if ($groupfunc['lottery']) {
						\app\v2\logic\BalanceLogic::log_lottery_check($json, $groupfunc);
					}
					break;

				case '积分排行':
					if ($groupfunc['lottery']) {
						\app\v2\logic\BalanceLogic::log_lottery_list($json, $groupfunc);
					}
					break;

				case(preg_match('/群/i', $message) ? $message : !$message):
					if ($groupfunc['ban_qun'] && $is_admin) {
						$msg = preg_filter('/群/', '', $message);
						if (\app\v2\logic\KickLogic::kick($json, $msg, $groupfunc)) {
							echo 'kicked';
							break;
						}
					}



				case(preg_match('/裙/i', $message) ? $message : !$message):
					if ($groupfunc['ban_qun'] && $is_admin) {
						$msg = preg_filter('/裙/', '', $message);
						if (\app\v2\logic\KickLogic::kick($json, $msg, $groupfunc)) {
							echo 'kicked';
							break;
						}
					}


				case(preg_match('/qun/i', $message) ? $message : !$message):
					if ($groupfunc['ban_qun'] && $is_admin) {
						$msg = preg_filter('/qun/', '', $message);
						if (\app\v2\logic\KickLogic::kick($json, $message, $groupfunc)) {
							echo 'kicked';
							break;
						}
					}

				case(strstr($message, 'jqqqcom') ? $message : !$message):
					if (strstr($message, 'jqqqcom')) {
						if ($groupfunc['ban_qun'] && $is_admin) {
//						$msg = preg_filter('/jq.qq.com/', '', $umessage);
							if (\app\v2\logic\KickLogic::kickout($json, $message, $groupfunc)) {
								echo 'kicked';
								break;
							}
						}
					}


				case (strstr($message, 'contact') ? $message : !$message):
//					dump('contact-found');
					if (strstr($message, 'contact')) {
						if ($groupfunc['ban_qun'] && $is_admin) {
//						dump('contant-ban-qun-on');
							if (strstr($message, 'type=group')) {
//							dump('type-group-exec-kick');
								if (\app\v2\logic\KickLogic::kickout($json, $message, $groupfunc)) {
									echo 'kicked';
									break;
								}
							}
						}
					}


				case(preg_match('/vx/i', $message) ? $message : !$message):
					$msg = preg_filter('/vx/i', '', $message);
					if (preg_match('/([0-9,a-z].*.[a-z]+)/i', $msg, $num)) {
//						dump($num);
						if (strlen(current($num)) > 8) {
							if ($groupfunc['ban_qun'] && $is_admin) {
								GroupAction::delete_msg($json['self_id'], $json['message_id']);
								\app\v1\logic\GroupLogic::logic_ban_user($json, 'vx', $groupfunc['ban_time']);
								break;
							}
						}
					}


				case(preg_match('/微信/i', $message) ? $message : !$message):
					if (strstr($message, '微信')) {
						dump('capture-微信');
						if (preg_match_all('/[0-9,a-z]+/i', $message, $num)) {
//							dump($num);
							foreach (current($num) as $value) {
								if (strlen($value) > 8) {
									dump('preee-微信');
									if ($groupfunc['ban_qun'] && $is_admin) {
										dump('exec-微信');
										GroupAction::delete_msg($json['self_id'], $json['message_id']);
										\app\v1\logic\GroupLogic::logic_ban_user($json, '微信', $groupfunc['ban_time']);
										break;
									}
								}
							}
						}
					}



				case(strstr($message, 'http') ? $message : !$message):
					if (strstr($message, 'http')) {
//						dump('网址屏蔽pre');
						if ($groupfunc['ban_url'] && $is_admin) {
							dump('网址屏蔽start');
							if (strstr($umessage, '[CQ')) {
								$msg = preg_filter('/\[cq[^]]+\]/i', '', $umessage);
							} else {
								$msg = $umessage;
							}
							dump('网址屏蔽before_exec-' . $msg);
							if ($msg) {
								$url_last = explode(',', C('urls'));
								$ver = false;
								foreach ($url_last as $uls) {
									if (strstr($msg, $uls)) {
										$ver = true;
										break;
									}
								}
//							if (\app\v2\logic\KickLogic::kick($json, $msg, $groupfunc)) {
//								echo 'kicked';
//								break;
//							}
//							if (\app\v1\logic\GroupLogic::logic_ban_user($json, '网址屏蔽', $groupfunc['ban_time'])) {
//								echo 'kicked';
//								break;
//							}
								if ($ver) {
									dump('网址屏蔽exec');
									GroupAction::delete_msg($json['self_id'], $json['message_id']);
									\app\v1\logic\GroupLogic::logic_ban_user($json, '网址屏蔽', $groupfunc['ban_time']);
									break;
								}
							}
						}
					}


				case (preg_match('/\.[A-z]+(\/||\?).([A-z]+[0-9])/i', $umessage) ? $message : !$message):
					if (preg_match('/\.[A-z]+(\/||\?).([A-z]+[0-9])/i', $umessage)) {
//						dump('网址屏蔽pre2');
						if ($groupfunc['ban_url'] && $is_admin) {
							dump('网址屏蔽start2');
							if (strstr($umessage, '[CQ')) {
								$msg = preg_filter('/\[cq[^]]+\]/i', '', $umessage);
							} else {
								$msg = $umessage;
							}
							dump('网址屏蔽before_exec2-' . $msg);
							if ($msg) {
								$url_last = explode(',', C('urls'));
								$ver = false;
								foreach ($url_last as $uls) {
									if (strstr($msg, $uls)) {
										$ver = true;
										break;
									}
								}
								if (\app\v2\logic\KickLogic::kick($json, $msg, $groupfunc)) {
									echo 'kicked2';
									break;
								}
								if ($ver) {
									dump('网址屏蔽exec2');
									GroupAction::delete_msg($json['self_id'], $json['message_id']);
									\app\v1\logic\GroupLogic::logic_ban_user($json, '网址屏蔽2', $groupfunc['ban_time']);
									break;
								}
							}
						}
					}



				case(strstr($message, '[CQ:share') ? $message : !$message):
					if (strstr($message, '[CQ:share')) {
						echo '[CQ:share';
						if ($groupfunc['ban_share'] && $is_admin) {
							GroupAction::delete_msg($json['self_id'], $json['message_id']);
							\app\v1\logic\GroupLogic::logic_ban_user($json, '禁止分享', $groupfunc['ban_time']);
							break;
						}
					}
				case(strstr($message, '[CQ:rich') ? $message : !$message):
					if (strstr($message, '[CQ:rich')) {
						echo '[CQ:rich2-' . $groupfunc['ban_share'];
						if ($groupfunc['ban_share'] && $is_admin) {
							echo '[CQ:rich2-ban';
							if (strstr($message, '分享')) {
								dump('rich exec1');
								GroupAction::delete_msg($json['self_id'], $json['message_id']);
								\app\v1\logic\GroupLogic::logic_ban_user($json, '禁止分享', $groupfunc['ban_time']);
								break;
							}
						}
					}

				case(strstr($message, '[CQ:show') ? $message : !$message):
					if (strstr($message, '[CQ:show')) {
						echo '[CQ:show';
						if ($groupfunc['ban_game'] && $is_admin) {
							GroupAction::delete_msg($json['self_id'], $json['message_id']);
							\app\v1\logic\GroupLogic::logic_ban_user($json, '禁止分享小游戏', $groupfunc['ban_time']);
							break;
						}
					}

				case(strstr($message, '[CQ:music') ? $message : !$message):
					if (strstr($message, '[CQ:music')) {
						echo '[CQ:music';
						if ($groupfunc['ban_music'] && $is_admin) {
							GroupAction::delete_msg($json['self_id'], $json['message_id']);
							\app\v1\logic\GroupLogic::logic_ban_user($json, '禁止分享音乐', $groupfunc['ban_time']);
							break;
						}
					}

				case(preg_match('/^重新验证.*/i', $message) ? $message : !$message):
					if (preg_match('/^重新验证.*/i', $message)) {
						echo '重新验证';
						$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
						if ($own['role'] == 'admin' || $own['role'] == 'owner') {
							if (preg_match('/\[CQ\:atqq.[0-9]+.\]/i', $message, $resign_temp)) {
								if (isset($resign_temp[0])) {
									if (preg_match('/[0-9]+/', $resign_temp[0], $atnum)) {
										unset($resign_temp);
										if (current($atnum)) {
											OnNoticeAction::verify_code($self_id, $group_id, current($atnum));
											unset($atnum);
											break;
										}
									}
								}
							}
						} else {
							\app\v1\logic\GroupLogic::logic_access_denide($json);
						}
					}

				case(preg_match('/^说话.*/i', $message) ? $message : !$message):
					if (preg_match('/^说话.*/i', $message)) {
						echo '说话';
						$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
						if ($own['role'] == 'admin' || $own['role'] == 'owner') {
							if (preg_match('/\[CQ\:atqq.[0-9]+.\]/i', $message, $resign_temp)) {
								if (isset($resign_temp[0])) {
									if (preg_match('/[0-9]+/', $resign_temp[0], $atnum)) {
										unset($resign_temp);
										if (current($atnum)) {
											OnNoticeAction::slience_add($self_id, $group_id, current($atnum), $groupfunc);
											unset($atnum);
											break;
										}
									}
								}
							}
						} else {
							\app\v1\logic\GroupLogic::logic_access_denide($json);
						}
					}

				case(preg_match('/^ktest.*/i', $message) ? $message : !$message):
					if (preg_match('/^ktest.*/i', $message)) {
						echo 'ktest';
						$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
						if ($own['role'] == 'admin' || $own['role'] == 'owner') {
							if (preg_match('/\[CQ\:atqq.[0-9]+.\]/i', $message, $resign_temp)) {
								if (isset($resign_temp[0])) {
									if (preg_match('/[0-9]+/', $resign_temp[0], $atnum)) {
										unset($resign_temp);
										if (current($atnum)) {
											GroupAction::set_group_kick($self_id, $group_id, current($atnum));
											unset($atnum);
											break;
										}
									}
								}
							}
						} else {
							\app\v1\logic\GroupLogic::logic_access_denide($json);
						}
					}

				case(preg_match('/^等级.*/i', $message) ? $message : !$message):
					if (preg_match('/^等级.*/i', $message)) {
						echo '等级';
						$own = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
						if ($own['role'] == 'admin' || $own['role'] == 'owner') {
							if (preg_match('/\[CQ\:atqq.[0-9]+.\]/i', $message, $resign_temp)) {
								if (isset($resign_temp[0])) {
									if (preg_match('/[0-9]+/', $resign_temp[0], $atnum)) {
										unset($resign_temp);
										if (current($atnum)) {
											$user_info = \app\v1\action\PrivateAction::_get_vip_info($json['self_id'], $json['user_id']);
											\app\v1\model\LogsActionModel::api_insert($json['user_id'], json_encode($user_info, 320), '等级');
											$str = OnNoticeAction::transform_to_emoji($user_info['level']);
											GroupAction::send_group_msg($self_id, $group_id, $user_info['level'] . '用户' . current($atnum) . $str);
											unset($atnum);
											break;
										}
									}
								}
							}
						} else {
							\app\v1\logic\GroupLogic::logic_access_denide($json);
						}
					}


				default:

					if ($groupfunc['word_limit']) {
						if (strlen($raw_message) > $groupfunc['word_limit_num']) {
							GroupAction::delete_msg($json['self_id'], $json['message_id']);
							\app\v1\logic\GroupLogic::logic_ban_user($json, '消息字数超过消息最大长度', $groupfunc['ban_time']);
						}
					}

					if ($groupfunc['ban_word']) {
						$bw = json_decode($groupfunc['ban_words'], 1);
						if (isset($bw[0])) {
							$ban = false;
							foreach ($bw as $value) {
								if ($value) {
									if (strstr($message, $value)) {
										echo $value;
										$ban = $value;
										break;
									}
								}
							}
							if ($ban) {
//								if ($is_admin) {
								GroupAction::delete_msg($json['self_id'], $json['message_id']);
								\app\v1\logic\GroupLogic::logic_ban_user($json, '自定义屏蔽词：' . substr($ban, 0, 1) . '**');
//								} else {
//									GroupAction::send_group_msg($self_id, $group_id, '请先设定我为管理员');
//								}
							}
						}
					}


					if ($groupfunc['kick_word']) {
						$bw = json_decode($groupfunc['kick_words'], 1);
						if (isset($bw[0])) {
							$ban = false;
							foreach ($bw as $value) {
								if (strstr($message, $value)) {
									$ban = $value;
									break;
								}
							}
							if ($ban) {
//								if ($is_admin) {
								GroupAction::delete_msg($json['self_id'], $json['message_id']);
								\app\v1\logic\GroupLogic::logic_kick_user($json, '自定义T出词：' . substr($ban, 0, 1) . '**');
//								} else {
//									GroupAction::send_group_msg($self_id, $group_id, '请先设定我为管理员');
//								}
							}
						}
					}

					if (preg_match('/\[CQ\:at\,qq\=' . $self_id . '\]/i', $raw_message)) {
						if (!cache('_norep1_' . $json['user_id'])) {
							GroupAction::send_group_msg($self_id, $group_id, "Hi~(๑•ᴗ•๑)我是Acfur\n如需帮助请输入acfurhelp");
						}
						$send = cache('__send__' . $json['user_id']);
						if ($send > 2) {
							\app\v1\logic\GroupLogic::logic_ban_user($json, '干扰判定', 86400);
							cache('_norep1_' . $json['user_id'], true, 86400);
						}
						if (!$send) {
							$send = 1;
						} else {
							$send++;
						}
						cache('__send__' . $json['user_id'], $send, 1);
					}

//					GroupAction::send_group_msg($self_id,$json['group_id'], $message);




					if ($groupfunc['auto_msg']) {
						$ams = \app\v1\model\GroupAutomsgModel::api_select($group_id);
						if ($ams) {
							foreach ($ams as $value) {
								if (strstr($message, $value['key'])) {
									if ($priv_bot) {
										GroupAction::send_group_msg($self_id, $group_id, $value['val']);
									} elseif (!$is_admin) {
										GroupAction::send_group_msg($self_id, $group_id, "我得有管理员权限才有资格说话喔~");
									} else {
										GroupAction::send_group_msg($self_id, $group_id, "一个月5.5有没有？考虑下私有机器人吧？");
									}
									break;
								}
							}
						}
					}

					if ($groupfunc['auto_reply']) {
						$reply = \app\v1\model\GroupAutoreplyModel::api_find($group_id, $message);
						if (isset($reply['val'])) {
							if ($priv_bot) {
								GroupAction::send_group_msg($self_id, $group_id, $reply['val']);
							} elseif (!$is_admin) {
								GroupAction::send_group_msg($self_id, $group_id, "我得有管理员权限才有资格说话喔~");
							} else {
								GroupAction::send_group_msg($self_id, $group_id, "我没有权限在您的群里自动回复呢，请使用acfurapp开通呢");
							}
							break;
						}
					}

					if ($groupfunc['qr_detect']) {
						if ($priv_bot) {

						}
					}

					break;
			}
		}
		if (array_key_exists($user_id, bots())) {
			if (bots()[$user_id]['rank'] > bots()[$self_id]['rank']) {
				if (GroupAction::set_group_leave($user_id, $group_id)) {

				}
			}
		}

		if ($groupfunc['guanggao'] && $is_admin) {
			dump('广告start');
			if (\app\v3\logic\AdLogic::api_adlogic($message)) {
				dump('guanggao exec1');
				GroupAction::delete_msg($json['self_id'], $json['message_id']);
				\app\v1\logic\GroupLogic::logic_ban_user($json, '广告屏蔽', $groupfunc['ban_time']);
			}
		}


		if (!cache('_last_msg_' . $user_id)) {
			cache('_last_msg_' . $user_id, true, 86400);
			GroupAction::get_group_member_info($self_id, $group_id, $user_id, true);
		}

		$slience = cache('__slience__');
		if (isset($slience[$user_id])) {
//			if ($slience[$user_id]['msg_count'] >= 1) {
//				unset($slience[$user_id]);
//			} else {
//				$slience[$user_id]['msg_count'] ++;
//			}
			unset($slience[$user_id]);
			cache('__slience__', $slience, 86400 * 30);
		}
	}

}
