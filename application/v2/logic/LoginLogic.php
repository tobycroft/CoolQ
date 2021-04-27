<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

use app\v1\action\PrivateAction;

/**
 * Description of LoginLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class LoginLogic {

	public static function loc_request_login($json) {
		RestLogic::rest('login' . $json['user_id'], 60);
		$token = crc32(sha1(rand(100000, 999999) . md5(time())));
		if (cache('_login_' . $json['user_id'], $token, 300)) {
			$str = '你的登录码为：' . $token . "\n";
			$str .= '----------------------' . "\n";
			$str .= '请在APP中输入本登录码，账号为你当前的Q号！' . "\n";
			$str .= '----------------------' . "\n";
			$str .= '你可以向我发送“acfurapp”来获取APP~';
			PrivateAction::send_private_msg($json['self_id'], $json['user_id'], $str);
		} else {
			PrivateAction::send_private_msg($json['self_id'], $json['user_id'], '登录程序错误');
		}
	}

}
