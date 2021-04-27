<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

/**
 * Description of GroupLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class TagLogic {

	//put your code here
	public static function tag_add($json) {
		$group_id = $json['group_id'];
		$user_id = $json['user_id'];
		$tag = $json['tag'];
		\app\v2\model\GroupTagModel::api_insert($group_id, $user_id, $tag, $qq);
	}

}
