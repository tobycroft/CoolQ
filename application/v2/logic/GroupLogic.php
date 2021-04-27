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
class GroupLogic {

	public static function logic_status($json) {
		$str = StrLogic::logic_status($json);
		\app\v1\logic\GroupLogic::send_msg($json, $str);
	}

	public static function logic_help($json) {
		$str = StrLogic::logic_help($json);
		\app\v1\logic\GroupLogic::send_msg($json, $str);
	}

	public static function logic_info($json) {
		$str = StrLogic::logic_info($json);
		\app\v1\logic\GroupLogic::send_msg($json, $str);
	}

	public static function logic_dev($json) {
		$str = StrLogic::logic_dev($json);
		\app\v1\logic\GroupLogic::send_msg($json, $str);
	}

	public static function logic_command($json) {
		$str = StrLogic::logic_command($json);
		\app\v1\logic\GroupLogic::send_msg($json, $str);
	}

	public static function logic_safe($json) {
		$str = StrLogic::logic_safe($json);
		\app\v1\logic\GroupLogic::send_msg($json, $str);
	}

	public static function logic_detail($json) {
		$str = StrLogic::logic_detail($json);
		\app\v1\logic\GroupLogic::send_msg($json, $str);
	}

	public static function logic_app($json) {
		$str = StrLogic::logic_app($json);
		\app\v1\logic\GroupLogic::send_msg($json, $str);
	}

}
