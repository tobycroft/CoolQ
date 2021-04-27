<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\model;

class LogsModel {

	public static function api_insert($logs, $discript = '') {
		$db = db('logs');
		$data = [
			'logs' => $logs,
			'discript' => $discript,
		];
		$db->data($data);
		return $db->insert();
	}

}
