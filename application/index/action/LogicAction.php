<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\index\action;

/**
 * Description of LogicAction
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
use app\v1\action\GroupAction;

class LogicAction {

	//put your code here
	public static function refresh_all_group_member($self_id) {
		$groups = GroupAction::get_group_list($self_id);
		if ($groups) {
			foreach ($groups as $value) {
//				GroupAction::_get_group_info($self_id, $value['group_id']);
				if (!\app\v1\model\GroupMemberModel::api_find_groupExists($value['group_id'])) {
					GroupAction::get_group_member_list($self_id, $value['group_id']);
				}
			}
		} else {
			dump($groups);
			dump($self_id);
		}
	}

}
