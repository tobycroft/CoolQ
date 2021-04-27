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
class GroupRequestModel {

	//put your code here

	public static function api_insert($self_id, $group_id, $user_id, $sub_type, $comment, $flag) {
		$db = db('group_request');
		$data = [
			'self_id' => $self_id,
			'group_id' => $group_id,
			'user_id' => $user_id,
			'sub_type' => $sub_type,
			'comment' => $comment,
			'flag' => $flag,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_find($group_id) {
		$db = db('group_request');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->find();
	}

	public static function api_delete($group_id) {
		$db = db('group_request');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_deletes($group_ids) {
		$db = db('group_request');
		$where = [
			'group_id' => ['in', $group_ids],
		];
		$db->where($where);
		return $db->delete();
	}

}
