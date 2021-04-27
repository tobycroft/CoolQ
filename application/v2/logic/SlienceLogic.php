<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

use app\v1\logic\GroupLogic;

/**
 * Description of SlienceLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class SlienceLogic {

	public static function slience($data) {
		foreach ($data as $user_id => $value) {
			$self_id = $value['self_id'];
			$group_id = $value['group_id'];
			$value['user_id'] = $user_id;
			$groupinfo = \app\v1\model\GroupInfoModel::api_find($group_id);
			if ($groupinfo) {
				$groupfunc = \app\v1\model\GroupFunctionOpenModel::api_find($group_id);
				$slience = $groupfunc['slience'];
				$slience_time = $groupfunc['slience_time'];
				$slience_resign = $groupfunc['slience_resign'];
				$slience_kick = $groupfunc['slience_kick'];
				$join_time = $value['join_time'];
				$to_time = $value['to_time'];
				if (!isset($value['next_alert'])) {
					dump('unset' . $value['user_id']);
					unset($data[$user_id]);
					cache('__slience__', $data, 86400 * 30);
				}
				$next_alert = $value['next_alert'];
				$alert = $value['alert'];
				$joined_min = floor((time() - $join_time) / 60);
				$end_min_orign = floor(($to_time - time()) / 60);
				$end_min = $slience_time - $joined_min;
				$at = "[CQ:at,qq=$user_id]]";
				if ($slience_kick) {
					$str = '被T出';
				} else {
					$str = '重新验证';
				}
//				dump($user_id . '-' . $to_time . '-' . ($to_time - time()));
				if (time() > $to_time) {
					dump($user_id . 'totimereach');
					unset($data[$user_id]);
					cache('__slience__', $data, 86400 * 30);
					if ($slience_kick) {
						dump('kick-' . $user_id);
						\app\v1\action\GroupAction::set_group_kick($self_id, $group_id, $user_id);
					} elseif ($slience_resign) {
						dump('resign-' . $user_id);
						\app\v1\action\OnNoticeAction::verify_code($self_id, $group_id, $user_id);
					}
				} else {
					if ($alert) {
//						dump($user_id . '-' . $next_alert . '-' . ($next_alert - time()));
						if ($next_alert < time()) {
							dump($user_id . 'alert');
							$data[$user_id]['alert'] = false;
							dump('您已经加群' . $joined_min . '分钟了，' . $end_min . '分钟不发言将' . $str);
							GroupLogic::send_msg($value, $at . '您已经加群' . $joined_min . '分钟了，' . $end_min . '分钟内不发言将' . $str);
						}
					} else {
						dump($user_id . 'noalert');
					}
				}
			} else {
				dump($group_id . 'not_found');
				unset($data[$user_id]);
			}
		}
		cache('__slience__', $data, 86400 * 30);
	}

}
