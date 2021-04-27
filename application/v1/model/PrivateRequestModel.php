<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

/**
 * Description of PrivateRequestModel
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class PrivateRequestModel {

	//put your code here

	public static function api_insert($self_id, $user_id, $comment, $flag) {
		$db = db('private_request');
		$data = [
			'self_id' => $self_id,
			'user_id' => $user_id,
			'comment' => $comment,
			'flag' => $flag,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_find() {
		$db = db('private_request');
		return $db->find();
	}

}
