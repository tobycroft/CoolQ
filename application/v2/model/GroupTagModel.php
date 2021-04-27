<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\model;

/**
 * Description of GroupTagModel
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class GroupTagModel {

	//put your code here
	public static function api_find($group_id, $user_id, $tag, $qq) {
		$db = db('group_tag');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'tag' => $tag,
			'qq' => $qq,
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_count($group_id, $user_id) {
		$db = db('group_tag');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		return $db->count(0);
	}

	public static function api_select($group_id, $user_id, $tag) {
		$db = db('group_tag');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'tag' => $tag,
		];
		$db->where($where);
		return $db->select();
	}

	public static function api_groupby($group_id, $user_id) {
		$db = db('group_tag');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		$db->group('tag');
		return $db->select();
	}

	public static function api_insert($group_id, $user_id, $tag, $qq) {
		$db = db('group_tag');
		$data = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'tag' => $tag,
			'qq' => $qq,
		];
		$db->data($data);
		return $db->insert();
	}

	public static function api_delete($group_id, $user_id, $tag) {
		$db = db('group_tag');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'tag' => $tag,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_deletes($group_ids) {
		$db = db('group_tag');
		$where = [
			'group_id' => ['in', $group_ids],
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_delete_single($group_id, $user_id, $tag, $qq) {
		$db = db('group_tag');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
			'tag' => $tag,
			'qq' => $qq,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_delete_user($group_id, $user_id) {
		$db = db('group_tag');
		$where = [
			'group_id' => $group_id,
			'user_id' => $user_id,
		];
		$db->where($where);
		return $db->delete();
	}

	public static function api_delete_qq($group_id, $qq) {
		$db = db('group_tag');
		$where = [
			'group_id' => $group_id,
			'qq' => $qq,
		];
		$db->where($where);
		return $db->delete();
	}

}
