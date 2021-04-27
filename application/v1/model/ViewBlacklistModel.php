<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

/**
 * Description of ViewBlacklistModel
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class ViewBlacklistModel {

	//put your code here

	public static function api_select($group_id) {
		$db = db('view_blacklist');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->select();
	}

}
