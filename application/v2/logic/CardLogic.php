<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

/**
 * Description of CardLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class CardLogic {

	//put your code here
	public static function change_card($json, $groupfunc) {
		\app\v1\action\GroupAction::set_group_card($json['self_id'], $json['group_id'], $json['user_id'], $groupfunc['card_name']);
	}

}
