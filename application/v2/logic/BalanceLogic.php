<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

use think\helper\Time;

/**
 * Description of BalanceLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class BalanceLogic {

	public static function inner_balance($json, $inc_balance) {
		$group_id = $json['group_id'];
		$user_id = $json['user_id'];

		if (\app\v2\model\GroupBalanceModel::api_find_byGroupId($group_id, $user_id)) {
			\app\v2\model\GroupBalanceModel::api_inc_balance($group_id, $user_id, $inc_balance);
		} else {
			\app\v2\model\GroupBalanceModel::api_insert($group_id, $user_id, $inc_balance);
		}
	}

	public static function innser_update_balance($json, $balance) {
		$group_id = $json['group_id'];
		$user_id = $json['user_id'];

		if (\app\v2\model\GroupBalanceModel::api_find_byGroupId($group_id, $user_id)) {
			\app\v2\model\GroupBalanceModel::api_update_balance($group_id, $user_id, $balance);
		} else {
			\app\v2\model\GroupBalanceModel::api_insert($group_id, $user_id, 0);
		}
	}

	public static function log_sign_reward($json) {
//		\app\v1\action\GroupAction::set_group_card($json['self_id'], $json['group_id'], $json['user_id'], $groupfunc['card_name']);
		self::inner_balance($json, \app\v1\service\SettingsService::serv_get('sign_reward'));
	}

	public static function log_lottery($json) {
		$group_id = $json['group_id'];
		$user_id = $json['user_id'];
		$sign_rank = cache('__lottoday__' . date('Y-m-d', time()));
		if (!$sign_rank) {
			$sign_rank = [];
		}
		if (!isset($sign_rank[$group_id])) {
			$sign_rank[$group_id] = [];
		}
		if (cache('_lott_' . $group_id . '-' . $user_id) == date('Y-m-d', time())) {
			\app\v1\logic\GroupLogic::send_msg($json, date('Y-m-d H:i:s', time()) . "\n" . '您今天已经抽奖过了，请明天再来');
		} else {
			cache('_lott_' . $group_id . '-' . $user_id, date('Y-m-d', time()));
			$num = rand(0, 1000);
			$bal = \app\v2\model\GroupBalanceModel::api_find_byGroupId($group_id, $user_id);
			$str = date('Y-m-d H:i:s', time()) . "\n";
			$reward = '';
			$rank = count($sign_rank[$group_id]);
			array_push($sign_rank[$group_id], $user_id);
			cache('__lottoday__' . date('Y-m-d', time()), $sign_rank, 86400);
			if ($bal < 0) {
				if ($rank < 10) {
					$str .= '您是第' . ($rank + 1) . '个抽奖的，额外奖励' . ((10 - $rank) * 10) . '分' . "\n";
					self::inner_balance($json, (10 - $rank) * 10);
					$str .= '您的总积分为：' . "\n";
					$str .= $bal['balance'];
					\app\v1\logic\GroupLogic::send_msg($json, $str);
				} else {
					$str .= '您的积分已经小于0，请先通过签到增加积分哦~';
					\app\v1\logic\GroupLogic::send_msg($json, $str);
				}
			} else {
				if ($num == 1000) {
					$reward .= '现有积分100倍奖励';
					self::innser_update_balance($json, $bal['balance'] * 100);
				} elseif (1 < $num && $num <= 30) {
					$reward .= '现有积分10倍奖励';
					self::innser_update_balance($json, $bal['balance'] * 10);
				} elseif (30 < $num && $num <= 100) {
					$reward .= '现有积分2倍奖励';
					self::innser_update_balance($json, $bal['balance'] * 2);
				} elseif (100 < $num && $num <= 500) {
					$reward .= '现有积分奖励20';
					self::inner_balance($json, 20);
				} elseif (500 < $num && $num <= 700) {
					$reward .= '现有积分奖励10';
					self::inner_balance($json, 10);
				} elseif (700 < $num && $num <= 900) {
					$reward .= '现有积分扣除20';
					self::inner_balance($json, -20);
				} elseif (900 < $num && $num <= 999) {
					$reward .= '现有积分双倍扣除';
					self::innser_update_balance($json, $bal['balance'] / 2);
				} else {
					$reward .= '积分清零';
					self::innser_update_balance($json, 0);
				}
				$bal = \app\v2\model\GroupBalanceModel::api_find_byGroupId($group_id, $user_id);

				$str .= '您的奖励为：' . "\n";
				$str .= $reward . "\n";
				$str .= '您的总积分为：' . "\n";
				$str .= $bal['balance'];

				if ($rank < 10) {
					$str .= '您是第' . ($rank + 1) . '个抽奖的，额外奖励' . ((10 - $rank) * 10) . '分' . "\n";
					self::inner_balance($json, (10 - $rank) * 10);
				}

				\app\v1\logic\GroupLogic::send_msg($json, $str);
			}
		}
	}

	public static function log_lottery_check($json) {
		$group_id = $json['group_id'];
		$user_id = $json['user_id'];
		$bal = \app\v2\model\GroupBalanceModel::api_find_byGroupId($group_id, $user_id);
		$str = date('Y-m-d H:i:s', time()) . "\n";
		$str .= '您的总积分为：' . "\n";
		$str .= $bal['balance'];
		\app\v1\logic\GroupLogic::send_msg($json, $str);
	}

	public static function log_lottery_list($json) {
		$group_id = $json['group_id'];
//		$user_id = $json['user_id'];
		$bal = \app\v2\model\GroupBalanceModel::api_select_byGroupId($group_id);
		$i = 1;
		$str = date('Y-m-d H:i:s', time()) . "\n";
		foreach ($bal as $value) {
			$user = \app\v1\model\GroupMemberModel::api_find($group_id, $value['user_id']);
			$str .= '第' . $i . '名：' . $value['balance'] . '分---[' . ($user['card'] ?: $user['nickname']) . "]\n";
			$i++;
		}
		if (!cache(__CLASS__ . __FUNCTION__ . $group_id)) {
			cache(__CLASS__ . __FUNCTION__ . $group_id, 1, 120);
			\app\v1\action\GroupAction::send_group_msg($json['self_id'], $json['group_id'], $str, false);
		}
	}

}
