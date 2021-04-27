<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class PrivateSendModel {

	public static function api_insert($self_id, $user_id, $message, $message_id, $status = '1') {
		$db = db('private_send');
		$data = [
			'self_id' => $self_id,
			'user_id' => $user_id,
			'message' => $message,
			'message_id' => $message_id,
			'code' => $status,
			'date' => time(),
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_update_retract($message_id) {
		$db = db('private_send');
		$where = [
			'message_id' => $message_id,
		];
		$db->where($where);
		$data = [
			'retract' => true
		];
		$db->data($data);
		return $db->update();
	}

}
