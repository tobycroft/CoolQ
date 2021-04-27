<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class GroupRecieveModel {

	public static function api_insert($self_id, $sub_type, $message_id, $group_id, $user_id, $message, $raw_message, $font, $time) {
		$db = db('group_recieve');
		$data = [
			'self_id' => $self_id,
			'sub_type' => $sub_type,
			'message_id' => $message_id,
			'group_id' => $group_id,
			'user_id' => $user_id,
			'message' => $message,
			'raw_message' => $raw_message,
			'font' => $font,
			'date' => $time,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_find_last($group_id, $user_id) {
		$db = db('group_recieve');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		$db->order('id desc');
		return $db->find();
	}

	public static function api_select_last($group_id, $user_id, $num = 5) {
		$db = db('group_recieve');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		$db->order('id desc');
		$db->limit($num);
		return $db->select();
	}

	public static function api_delete($group_id) {
		$db = db('group_recieve');
		$where = [
			'group_id' => $group_id,
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_deletes($group_ids) {
		$db = db('group_recieve');
		$where = [
			'group_id' => ['in', $group_ids],
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_find($user_id, $last_of) {
		$db = db('group_recieve');
		$where = [
			'user_id' => $user_id,
		];
		$db->where($where);
		$db->order('id desc');
		$db->page($last_of);
		return $db->find();
	}

}
