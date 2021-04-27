<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\logic;

use app\v1\action\GroupAction;
use app\v1\model\GroupBanModel;

/**
 * Description of GroupCallLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class GroupLogic {

	public static function group_user_limit_check($self_id, $group_id, $member_count) {
		$ret = \app\v1\model\BotSettingsModel::api_find($self_id);
		dump(__FUNCTION__ . 'start');
		dump($member_count);
		dump($ret);

		if ($member_count < $ret['min_user']) {
			if (!cache('1' . __CLASS__ . $self_id . __FUNCTION__ . $group_id)) {
				$bot_recommend = \app\v1\model\BotSettingsModel::api_find_byMinUser($member_count);
				GroupAction::send_group_msg($self_id, $group_id, '本群人数为：' . $member_count . '，本机器最低人数要求为' . $ret['min_user'] . $ret['min_word'] . "\n" . '推荐使用：' . $bot_recommend['self_id'] . "\n" . '如有问题请联系开发群：542749156', null, 1);
			}
			GroupAction::set_group_leave($self_id, $group_id);
			echo 'reach_min_num_of' . $self_id . '-' . $group_id;
			cache('1' . __CLASS__ . $self_id . __FUNCTION__ . $group_id, 1, 300);
			exit(0);
		} elseif ($member_count > $ret['max_user']) {
			if (!cache('2' . __CLASS__ . $self_id . __FUNCTION__ . $group_id)) {
				$bot_recommend = \app\v1\model\BotSettingsModel::api_find_byMinUser($member_count);
				GroupAction::send_group_msg($self_id, $group_id, '本群人数为：' . $member_count . '，本机器人最高人数限制为' . $ret['max_user'] . $ret['max_word'] . "\n" . '推荐使用：' . $bot_recommend['self_id'] . "\n" . '如有问题请联系开发群：542749156', null, 1);
			}
			GroupAction::set_group_leave($self_id, $group_id);
			echo 'reach_max_num_of' . $self_id . '-' . $group_id;
			cache('2' . __CLASS__ . $self_id . __FUNCTION__ . $group_id, 1, 300);
			exit(0);
		}
		dump(__FUNCTION__ . 'end');
	}

	public static function logic_sayHello($json, $priv_bot = false) {
		$str = "[CQ:at,qq=$json[user_id]]";
//		$user = \app\v1\model\GroupMemberModel::api_find_bySelfid($json['self_id'], $json['group_id'], $json['self_id']);
		$user = GroupAction::get_group_member_info($json['self_id'], $json['group_id'], $json['self_id'], true);
		if ($priv_bot) {
			if (!empty($user['card'])) {
				$vv = $user['card'];
			} else {
				$vv = $user['nickname'];
			}
		} else {
			$vv = 'Acfur云助手';
		}
		\app\v1\action\GroupAction::send_group_msg($json['self_id'], $json['group_id'], $str . "\n" . 'Hi~(๑•ᴗ•๑)我是' . $vv . "\n" . '如需帮助请输入acfurhelp' . "\n" . '如果需要反馈请联系开发组：542749156');
	}

	public static function logic_reply($json) {
		$time = cache(__CLASS__ . __FUNCTION__ . $json['group_id']);
		if (!$time) {
			$time = 1;
			cache(__CLASS__ . __FUNCTION__ . $json['group_id'], $time, 600);
			$str = "[CQ:at,qq=$json[user_id]]";
			\app\v1\action\GroupAction::send_group_msg($json['self_id'], $json['group_id'], $str . 'acfurhelp：查看说明，acfur设定：查看设定' . rand(0, 9999));
		} else {
			if ($time < 3) {
				cache(__CLASS__ . __FUNCTION__ . $json['group_id'], $time++, 600);
				$str = "[CQ:at,qq=$json[user_id]]";
				\app\v1\action\GroupAction::send_group_msg($json['self_id'], $json['group_id'], $str . '以acfur开头的消息都是用来执行命令的！用“acfurhelp”来查看说明啊，写了很仔细了，实在不行就输入“acfur详细”来查看更详细的说明' . rand(0, 9999));
			} else {
				if ($time == 3) {
					$str = "[CQ:at,qq=$json[user_id]]";
					\app\v1\action\GroupAction::send_group_msg($json['self_id'], $json['group_id'], $str . '好了，如果不会用拟态就先关闭拟态模式吧' . rand(0, 9999));
				}
				cache(__CLASS__ . __FUNCTION__ . $json['group_id'], $time++, 600);
			}
		}
	}

	public static function logic_access_denide($json) {
		self::send_msg($json, '只有管理员能执行本操作呢~' . rand(0, 9999));
	}

	public static function logic_group_members_num($json) {
		$number = \app\v1\model\GroupMemberModel::api_count_group($json['group_id']);
		self::send_msg($json, '当前群人数：' . $number . '人');
	}

	public static function logic_sign($json, $groupfunc) {
		$sign = \app\v1\model\GroupSignModel::api_find($json['group_id'], $json['user_id']);
		$count = \app\v1\model\GroupSignModel::api_count($json['group_id'], $json['user_id']) + 1;
		$counts = 30 - $count % 30;
		$besu = ceil(($count + 0.1) / 30);
		$banm = \app\v1\model\GroupBanModel::api_count($json['group_id'], $json['user_id']);
		if (!$sign) {
			\app\v2\logic\BalanceLogic::log_sign_reward($json);
			$ret = \app\v1\model\GroupBanModel::api_find($json['group_id'], $json['user_id']);
			if ($ret) {
				if (\app\v1\model\GroupBanModel::api_delete($ret['id'])) {
					if ($banm > 1) {
						$adm = '您需要继续签到' . ($banm - 1) . '次才可累计签到获取免死金牌';
					} else {
						$adm = '您的生命值已经补满，明日签到就能累计签到天数拉~' . rand(0, 9999);
						\app\v1\model\GroupSignModel::api_delete($json['group_id'], $json['user_id']);
					}
					self::send_msg($json, '签到成功，生命值+1！' . "\n" . $adm, !$groupfunc['sign_noti']);
				} else {
					self::send_msg($json, '签到成功，生命值恢复失败，请通过Acfurgogogo查看反馈邮箱，向我们反馈！', !$groupfunc['sign_noti']);
				}
			} else {
				self::send_msg($json, date('Y-m-d H:i:s', time()) . "\n" . '签到成功！' . '您已签到' . $count . '天' . "\n" . '再签到' . $counts . '天就能获得第' . $besu . '块免死金牌', !$groupfunc['sign_noti']);
			}
			\app\v1\model\GroupSignModel::api_insert($json['group_id'], $json['user_id']);
		} else {
			self::send_msg($json, '今日已经签到请明日再来！' . rand(0, 9999), !$groupfunc['sign_noti']);
		}
	}

	public static function send_msg($json, $message, $force_private = false, $auto_escape = false) {
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($json['group_id']);
		if ($groupfunc['all_private'] || $force_private) {
			$groupinfo = \app\v1\model\GroupInfoModel::api_find($json['group_id']);
			dump('send_private');
			dump($json['group_id']);
//			dump($groupinfo);
			\app\v1\action\PrivateAction::send_private_msg($json['self_id'], $json['user_id'], $message . "\n-----------------\n消息来自：" . $groupinfo['group_name'], $auto_escape);
		} else {
			dump('send_group');
			GroupAction::send_group_msg($json['self_id'], $json['group_id'], $message, $auto_escape, true);
		}
	}

	public static function send_msg2($self_id, $group_id, $user_id, $message, $force_private = false, $auto_escape = false) {
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($group_id);
		if ($groupfunc['all_private'] || $force_private) {
			$groupinfo = \app\v1\model\GroupInfoModel::api_find($group_id);
			dump($group_id);
			dump($groupinfo);
			\app\v1\action\PrivateAction::send_private_msg($self_id, $user_id, $message . "\n-----------------\n消息来自：" . $groupinfo['group_name'], $auto_escape);
		} else {
			GroupAction::send_group_msg($self_id, $group_id, $message, $auto_escape, true);
		}
	}

	public static function logic_show_black($json) {
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($json['group_id']);
		$bl = \app\v1\model\ViewBlacklistModel::api_select($json['group_id']);
		$arr = [];
		foreach ($bl as $value) {
			if (!isset($arr[$value['user_id']])) {
				$arr[$value['user_id']] = [];
			}
			array_push($arr[$value['user_id']], $value);
		}
		$str = '';
		foreach ($arr as $key => $value) {
			$count = count($arr[$key]);
			if (strlen($value[0]['card']) > 1) {
				$name = $value[0]['card'];
			} else {
				$name = $value[0]['nickname'];
			}
			$name = preg_replace("/\t/", "", $name);
			$name = preg_replace("/　/", "", $name);
			if (strlen(trim($name)) < 3) {
				$name = $key;
			}
			$str .= $name . ':' . $count . '次--血量剩余:' . ($groupfunc['kick_time'] - $count) . "\n";
		}
		if (count($bl) > 0) {
			self::send_msg($json, '小黑屋记录：' . "\n" . $str);
		} else {
			self::send_msg($json, '小黑屋目前没人哦~期待您的加入呢！' . rand(0, 9999));
		}
	}

	public static function logic_refresh_members_num($json, $force = false) {
		$group_id = $json['group_id'];
		$self_id = $json['self_id'];
		$number = \app\v1\model\GroupMemberModel::api_count_group($json['group_id']);
		$data = GroupAction::get_group_member_list($json['self_id'], $json['group_id'], false);
		dump($data);
		$num = count($data);
		if ($num != $number || $force) {
			\app\v1\model\GroupMemberModel::api_delete($group_id);
			$arr = [];
			foreach ($data as $value) {
				$arr[] = \app\v1\model\GroupMemberModel::api_pre_insertAll($self_id, $group_id, $value['user_id'], $value['nickname'], $value['age'], $value['join_time'], $value['last_sent_time'], $value['sex'], $value['role'], $value['card']);
			}
			\app\v1\model\GroupMemberModel::api_insertAll($arr);
			self::send_msg($json, '刷新后群人数：' . $num . '人');
		} else {
			self::send_msg($json, '群人数：' . $number . '人');
		}
	}

	public static function logic_retract_message($json) {
		$member = \app\v1\model\GroupMemberModel::api_like_nickname($json['group_id'], $json['message']);
		if (!$member) {
			$member = \app\v1\model\GroupMemberModel::api_like_card($json['group_id'], $json['message']);
		}
		if ($member) {
			$message = \app\v1\model\GroupRecieveModel::api_select_last($json['group_id'], $member['user_id']);
			$str = '';
			$i = 0;
			foreach ($message as $value) {
				$i++;
				$str .= $i . '.' . $value['message'] . "\n";
			}
			self::send_msg($json, "他说:\n" . $str);
		}
	}

	public static function logic_kick_user($json, $word = '违规词') {
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($json['group_id']);
		$sign = \app\v1\model\GroupSignModel::api_count($json['group_id'], $json['user_id']);
		$user = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
		\app\v1\model\GroupSignModel::api_delete($json['group_id'], $json['user_id']);
		$balance = \app\v2\model\GroupBalanceModel::api_find_byGroupId($json['group_id'], $json['user_id']);
		$at = "[CQ:at,qq=$json[user_id]]";
		$bal = 100000;
		$bal_lft = $balance['balance'] - $bal;
		if ($bal_lft > 0) {
			$bans = '你已被扣除分数:' . $bal;
			\app\v2\model\GroupBalanceModel::api_inc_balance($json['group_id'], $json['user_id'], -abs($bal));
			if (!$groupfunc['simulate']) {
				if (strlen(trim($user['card'])) > 1) {
					self::send_msg($json, $user['card'] . '触发屏蔽词' . "\n" . $bans . '当前积分剩余：' . $bal_lft);
				} else {
					self::send_msg($json, $user['nickname'] . '触发屏蔽词' . "\n" . "\n" . $bans . '当前积分剩余：' . $bal_lft);
				}
			} else {
				self::send_msg($json, '本群不允许:' . $word . '等词语，请遵守群规，谢谢！');
			}
		} elseif ($sign > 60) {
			if (!$groupfunc['simulate']) {
				GroupAction::send_group_msg($json['self_id'], $json['group_id'], $at . '触发制裁：' . "\n" . '致命免死金牌被使用，T出操作被否决，金牌剩余：0');
			} else {
				GroupAction::send_group_msg($json['self_id'], $json['group_id'], '最后给你一次机会！在犯错你就死定了！' . rand(0, 9999));
			}
		} elseif ($sign > 1) {
			if (GroupAction::set_group_ban($json['self_id'], $json['group_id'], $json['user_id'], 86400 * 30)) {
				if (!$groupfunc['simulate']) {
					$adm = self::get_admins($json);
					GroupAction::send_group_msg($json['self_id'], $json['group_id'], $at . '触发制裁：' . "\n" . '非黑户，需要特殊处理' . "\n" . $adm);
				} else {
					GroupAction::send_group_msg($json['self_id'], $json['group_id'], '等其他管理高抬贵手吧' . rand(0, 9999));
				}
			}
		} else {
			if (GroupAction::set_group_kick($json['self_id'], $json['group_id'], $json['user_id'])) {
				if ($groupfunc['kick_note']) {
					self::logic_goodbyeword($json);
				}
				if ($user) {
					if (cache('__kickalert__' . $json['user_id'])) {
						cache('__kickalert__' . $json['user_id'], true, 3600);
						if (!$groupfunc['simulate']) {
							if (strlen(trim($user['card'])) > 1) {
								GroupAction::send_group_msg($json['self_id'], $json['group_id'], $at . rand(0, 9999) . '触发制裁：');
							} else {
								GroupAction::send_group_msg($json['self_id'], $json['group_id'], $at . rand(0, 9999) . '触发制裁：');
							}
						} else {
							GroupAction::send_group_msg($json['self_id'], $json['group_id'], $word . '会导致T出，不要作死' . rand(0, 9999));
						}
					}
				}
			}
		}
	}

	public static function logic_goodbyeword($json) {
		$group_id = $json['group_id'];
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($json['group_id']);
		$count = \app\v1\model\GroupBanModel::api_count($json['group_id'], $json['user_id']);
		$user = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
		$str = '';
		if (strlen(trim($user['card'])) > 1) {
			$name = $user['card'];
		} else {
			$name = $user['nickname'];
		}

		$str .= $name . '老爷生前也是个体面人，' . "\n";
		$join_to_now = time() - $user['join_time'];
		$d = floor($join_to_now / (3600 * 24));
		$m = floor((($join_to_now % (3600 * 24)) % 3600) / 60);
		if ($d > 10) {
			$str .= '在我们群里面蹦达了可能有那么' . $d . '天，技术不错，可惜还是没躲过制裁……' . "\n";
		} elseif ($d > 5) {
			$str .= '好像活了一个多星期吧，一个多星期怕是试用期都过不了╮(╯▽╰)╭' . "\n";
		} elseif ($d > 3) {
			$str .= '好像活了' . $d . '天多吧，这么短的时间就给干了，看来EQ极低=￣ω￣=' . "\n";
		} elseif ($d > 1) {
			$str .= '在我们群里面呆了可能有那么' . $d . '天，怕不是来度假的吧？(≧∇≦)ﾉ' . "\n";
		} else {
			$str .= '在我们群里面还没活过1天，也就活了' . $m . '分钟吧！走得很痛苦，火化的时候还诈了尸，一直喊没有死，火很旺，烧得嘎吱嘎吱响，烧了三天三夜。追悼会播放着《爱的供养》，家属很坚强，一个哭的都没有，还有一个是他奶奶忍不住摇了起来，那天风很大，运骨灰的路上还翻了车，骨灰盒摔碎了，刚要捧点儿骨灰，来了一辆洒水车……(ﾉ*･ω･)ﾉ' . "\n";
		}
		$i = 0;
		for ($index = 0; $index <= $count; $index++) {
			$i = $i + ($i + 1);
		}
		$str .= '记得他还在喘气的时候，在这里经历过了' . $count . '个磨难' . "\n";
		$ban_time = $groupfunc['ban_time'] * $i / 60;
		$str .= '一共在小黑屋度过了' . $ban_time . '分钟的美好时光，这段在小黑屋里的记忆将会成为他这一生最宝贵的财富' . "\n";
		$str .= '一路走好我们人类的好朋友' . $name;
		if (!$groupfunc['simulate']) {
			GroupAction::send_group_msg($json['self_id'], $group_id, $str);
		}
	}

	public static function logic_ban_user($json, $word = '违规词', $time = null) {
		$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($json['group_id']);
		if (!$time) {
			$time = $groupfunc['ban_time'];
		}
		$count = \app\v1\model\GroupBanModel::api_count($json['group_id'], $json['user_id']) + 1;
		$sign = \app\v1\model\GroupSignModel::api_count($json['group_id'], $json['user_id']);
		$user = \app\v1\model\GroupMemberModel::api_find($json['group_id'], $json['user_id']);
		$balance = \app\v2\model\GroupBalanceModel::api_find_byGroupId($json['group_id'], $json['user_id']);
		$bal = 300 * $count;
		$bal_lft = $balance['balance'] - $bal;
		if ($bal_lft > 0) {
			$bans = '这是你第' . $count . '次扣分，扣除分数:' . $bal;
			GroupBanModel::api_insert($json['self_id'], $json['group_id'], $json['user_id'], 1);
			\app\v2\model\GroupBalanceModel::api_inc_balance($json['group_id'], $json['user_id'], -abs($bal));
			if (!$groupfunc['simulate']) {
				if (strlen(trim($user['card'])) > 1) {
					self::send_msg($json, $user['card'] . '触发屏蔽词' . "\n" . '原因-' . $word . "\n" . $bans . '当前积分剩余：' . $bal_lft);
				} else {
					self::send_msg($json, $user['nickname'] . '触发屏蔽词' . "\n" . '原因-' . $word . "\n" . $bans . '当前积分剩余：' . $bal_lft);
				}
			} else {
				self::send_msg($json, '请不要发' . $word . '等词语，请遵守群规，谢谢！');
			}
		} elseif ($sign > 30) {
			$bans = '禁言操作被否决，免死金牌全部清空' . "\n";
			\app\v1\model\GroupSignModel::api_delete($json['group_id'], $json['user_id'], 30);
			$sign = \app\v1\model\GroupSignModel::api_count($json['group_id'], $json['user_id']);
			if (!$groupfunc['simulate']) {
				if (strlen(trim($user['card'])) > 1) {
					self::send_msg($json, $user['card'] . '触发屏蔽词' . "\n" . '原因-' . $word . "\n" . $bans . '当前免死金牌剩余：' . $sign);
				} else {
					self::send_msg($json, $user['nickname'] . '触发屏蔽词' . "\n" . '原因-' . $word . "\n" . $bans . '当前免死金牌剩余：' . $sign);
				}
			} else {
				self::send_msg($json, '这次就算了，下次再犯就直接禁言了');
			}
		} else {
			$bans = '这是你第' . $count . '次惩罚';
			if ($groupfunc['fixed_punish']) {
				$punish_next = $punish_ratio = 1;
			} else {
				$punish_ratio = pow(10, $count - 1);
				$punish_next = pow(10, $count);
			}
			if (GroupAction::set_group_ban($json['self_id'], $json['group_id'], $json['user_id'], $time * $punish_ratio)) {
				if ($user) {
					if ($count <= $groupfunc['kick_time']) {

						if (!$groupfunc['simulate']) {
							if (strlen(trim($user['card'])) > 1) {
								self::send_msg($json, $user['card'] . '触发' . ($time * $punish_ratio) . '秒小黑屋' . "\n" . '原因-' . $word . "\n" . $bans . "\n" . '预计下次禁言：' . ($punish_next * $time) . '秒' . "\n" . '还剩：' . ($groupfunc['kick_time'] - $count) . '条命');
							} else {
								self::send_msg($json, $user['nickname'] . '触发' . ($time * $punish_ratio) . '秒小黑屋' . "\n" . '原因-' . $word . "\n" . $bans . "\n" . '预计下次禁言：' . ($punish_next * $time) . '秒' . "\n" . '还剩：' . ($groupfunc['kick_time'] - $count) . '条命');
							}
						}
					} else {
						self::logic_kick_user($json, '生命值低于极限');
					}
				}
			}
		}
	}

	public static function l_turn_all_on($json) {
		if (\app\v1\model\GroupFunctionOpenModel::api_update_all_on($json['group_id'])) {
			GroupAction::send_group_msg($json['self_id'], $json['group_id'], 'AcField~全开，接管权限！');
		} else {
			GroupAction::send_group_msg($json['self_id'], $json['group_id'], 'AcField全速力中……');
		}
	}

	public static function l_turn_all_off($json) {
		if (\app\v1\model\GroupFunctionOpenModel::api_update_all_off($json['group_id'])) {
			GroupAction::send_group_msg($json['self_id'], $json['group_id'], '功能全关，管理权限交还');
		} else {
			GroupAction::send_group_msg($json['self_id'], $json['group_id'], 'AcField微速力中……');
		}
	}

	public static function l_clear($json) {
		if (\app\v1\model\GroupBanModel::api_clear($json['group_id'])) {
			GroupAction::send_group_msg($json['self_id'], $json['group_id'], '小黑屋记录已经清空');
		} else {
			GroupAction::send_group_msg($json['self_id'], $json['group_id'], '本群暂时没有小黑屋记录');
		}
	}

	public static function l_black_clear($json) {
		if (\app\v1\model\GroupBlackListModel::api_deletes($json['group_id'])) {
			GroupAction::send_group_msg($json['self_id'], $json['group_id'], '群黑名单已经全部清空');
		} else {
			GroupAction::send_group_msg($json['self_id'], $json['group_id'], '本群黑名单为空，欢迎大家踊跃进入');
		}
	}

	public static function logic_wegame_search($json) {
		if (!cache('group_id_' . $json['group_id'])) {
			cache('group_id_' . $json['group_id'], true, 10);
			$ret = \Wegame\Wegame::api_search($json['message']);
			$data = $ret['data'];
			$str = '';
			if (isset($data['count'])) {
				if ($data['count'] > 0) {
					$list = $data['lists'];
					$str .= "一共找到了" . $data['count'] . "个相关主播";
					foreach ($list as $value) {
//						$value['owner_name'];
//						$value['room_name'];
//						$value['live_id'];
//$value['is_opened'];
						$str .= "\n\n直播间:" . $value['room_name'] . "\n";
						$str .= "主播:" . $value['owner_name'] . "-ID:" . $value['live_id'] . "\n";
						$str .= "开播状态:" . (($value['is_opened'] === 1) ? '开播中' : '未开播');
					}
					$str .= "\n\n你还可以使用“acfur直播XXX来查找在线主播，例如\nacfur主播虎牙斗鱼";
					self::send_msg($json, $str);
				} else {
					self::send_msg($json, '没有找到相关主播，查询功能&开播提醒目前仅支持虎牙斗鱼熊猫企鹅电竞四个平台');
				}
			}
		}
	}

	public static function logic_wegame_search_online($json) {
		if (!cache('group_id_' . $json['group_id'])) {
			cache('group_id_' . $json['group_id'], true, 10);
			$ret = \Wegame\Wegame::api_search($json['message']);
			$data = $ret['data'];
			$str = '';
			if (isset($data['count'])) {
				if ($data['count'] > 0) {
					$list = $data['lists'];
					$i = 0;
					foreach ($list as $value) {
//						$value['owner_name'];
//						$value['room_name'];
//						$value['live_id'];
//$value['is_opened'];
						if ($value['is_opened'] === 1) {
							$i++;
							$str .= "\n\n直播间:" . $value['room_name'] . "\n";
							$str .= "主播:" . $value['owner_name'] . "-ID:" . $value['live_id'] . "\n";
							$str .= "开播状态" . ($value['is_opened'] == '1') ? '开播中' : '未开播';
						}
					}
					$str .= "\n\n如果需要绑定主播可以使用“acfur！绑定=主播ID”来进行绑定，例如\nacfur！绑定=123456";
					self::send_msg($json, '一共找到' . $i . '个已开播主播' . $str);
				} else {
					self::send_msg($json, '没有找到相关主播，查询功能&开播提醒目前仅支持虎牙斗鱼熊猫企鹅电竞四个平台');
				}
			}
		}
	}

	public static function get_admins($json) {
		$admins = \app\v1\model\GroupMemberModel::api_select_admins($json['group_id']);
		$adm = '';
		foreach ($admins as $value) {
			$adm .= "[CQ:at,qq=$value[user_id]]";
		}
		return $adm;
	}

	public static function get_at($user_id) {
		return "[CQ:at,qq=$user_id]";
	}

	public static function get_admins_arr($json) {
		$admins = \app\v1\model\GroupMemberModel::api_select_admins($json['group_id']);
		$adm = [];
		foreach ($admins as $value) {
			$adm[] = $value['user_id'];
		}
		return $adm;
	}

	public static function logic_send_rand_welcome($json) {
		$rand = rand(1, 9);
		switch ($rand) {
			case 1:
				GroupAction::send_group_msg($json['self_id'], $json['group_id'], '欢迎新人');
				break;

			case 2:
				GroupAction::send_group_msg($json['self_id'], $json['group_id'], '欢迎');
				break;

			case 3:
				GroupAction::send_group_msg($json['self_id'], $json['group_id'], '欢迎加入本群');
				break;

			case 4:
				GroupAction::send_group_msg($json['self_id'], $json['group_id'], '欢迎加入');
				break;

			case 5:
				GroupAction::send_group_msg($json['self_id'], $json['group_id'], '新人发红包');
				break;

			case 6:
				GroupAction::send_group_msg($json['self_id'], $json['group_id'], '欢迎欢迎');
				break;

			case 7:
				GroupAction::send_group_msg($json['self_id'], $json['group_id'], 'Welcome');
				break;

			case 8:
				GroupAction::send_group_msg($json['self_id'], $json['group_id'], '有新人');
				break;

			default:
				GroupAction::send_group_msg($json['self_id'], $json['group_id'], '欢迎');
				break;
		}
	}

}
