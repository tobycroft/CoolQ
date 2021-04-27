<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class PrivateRecieveModel {

	public static function api_insert($self_id, $sub_type, $message_id, $user_id, $message, $raw_message, $font, $time) {
		$db = db('private_recieve');
		$data = [
			'self_id' => $self_id,
			'sub_type' => $sub_type,
			'message_id' => $message_id,
			'user_id' => $user_id,
			'message' => $message,
			'raw_message' => $raw_message,
			'font' => $font,
			'date' => $time,
		];
		$db->data($data);
		return $db->insert();
	}

}
