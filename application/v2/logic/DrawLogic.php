<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

/**
 * Description of DrawLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class DrawLogic {

	public static function StartDraw($json, $message, $groupfunc, $own) {
		$mess = $json['message'];
		$mess = preg_filter('/^奖励/', '', $mess);
		$arr = explode('=', $mess);
		$ret = \app\v2\model\GroupLuckydrawModel::api_find($json['group_id']);
		if ($ret) {
			\app\v1\logic\GroupLogic::send_msg($json, '已存在一个抽奖内容，请先开奖才可以设定下一个抽奖');
		} else {
			if (isset($arr[0]) && isset($arr[1]) && isset($arr[2])) {
				if (\app\v2\model\GroupLuckydrawModel::api_insert($json['group_id'], $arr[0], $arr[1], $arr[2])) {
					\app\v1\logic\GroupLogic::send_msg($json, '抽奖已设定，内容：' . "\n" . $arr[0] . "\n" . '数量：' . $arr[1] . "\n" . '时长：' . $arr[2]);
				} else {
					\app\v1\logic\GroupLogic::send_msg($json, '设定失败，设定格式为奖励+奖励内容=数量=时长（秒），例如“奖励玩具抱枕=1=3600”');
				}
			} else {
				\app\v1\logic\GroupLogic::send_msg($json, '设定失败，设定格式为奖励+奖励内容=数量=时长（秒），例如“奖励玩具抱枕=1=3600”');
			}
		}
	}

	public static function OnDraw($json) {
		$ret = \app\v2\model\GroupLuckydrawModel::api_find($json['group_id']);
		if ($ret) {
			$ret2 = \app\v2\model\GroupLuckydrawLogModel::api_find($json['group_id'], $json['user_id'], $ret['id']);
			if (!$ret2) {
				if (\app\v2\model\GroupLuckydrawLogModel::api_insert($json['group_id'], $json['user_id'], $ret['id'])) {
					\app\v1\logic\GroupLogic::send_msg($json, '抽奖成功请等待开奖~');
				} else {
					\app\v1\logic\GroupLogic::send_msg($json, '抽奖失败，插件故障~');
				}
			} else {
				\app\v1\logic\GroupLogic::send_msg($json, '您已经参与过抽奖了，请等待开奖~');
			}
		} else {
			\app\v1\logic\GroupLogic::send_msg($json, '没有抽奖内容哦~');
		}
	}

	public static function StopDraw($json, $message, $groupfunc, $own) {

	}

}
