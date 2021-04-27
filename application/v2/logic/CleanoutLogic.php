<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

/**
 * Description of CleanoutLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class CleanoutLogic {

	//put your code here
	public static function loc_clear($json) {
		$group_id = $json['group_id'];
		$ret = \app\v1\model\GroupBanPermenentModel::api_select_byGroupId($group_id);
		if ($ret) {
			foreach ($ret as $value) {
				if (\app\v1\action\GroupAction::set_group_kick($value['self_id'], $value['group_id'], $value['user_id'])) {
					\app\v1\model\GroupBanPermenentModel::api_delete($value['group_id'], $value['user_id']);
				}
			}
			\app\v1\action\GroupAction::send_group_msg($value['self_id'], $value['group_id'], '已完成清除');
		}
	}

}
