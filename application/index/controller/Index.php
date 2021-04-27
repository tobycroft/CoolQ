<?php

namespace app\index\controller;

use app\index\action\RouterAction;

class Index {

	public function index() {
		$content = file_get_contents('php://input');
		if (!empty($content)) {
			RouterAction::Log_all($content);
			RouterAction::Do_action($content);
		}
	}

	public function status() {
		$bot = bots();
		if ($bot) {
			foreach ($bot as $key => $value) {
				$r1 = \app\v1\action\CombAction::get_login_info($key);
				$r2 = \app\v1\action\CombAction::get_cookies($key);
				if ($r2) {
					$ret['app_enabled'] = $ret['app_good'] = $ret['online'] = $ret['good'] = 1;
				}
//				dump($r2);
//				$ret = \app\v1\action\CombAction::get_status($key);
				if (!$ret['good']) {
//					\app\v1\action\CombAction::set_restart($key);
//					\app\v1\action\CombAction::set_restart_plugin($key);
				}
				echo '账号：', $r1['user_id'], "</br>";
				echo '昵称：', $r1['nickname'], "</br>";
				echo '--------------------------', "</br>";

				echo '启用状态：', $ret['app_enabled'] ? '开' : '关', "</br>";
				echo '运行状态：', $ret['app_good'] ? '开' : '关', "</br>";
				echo '在线：', $ret['online'] ? '是' : '否', "</br>";
				echo '全正常：', $ret['good'] ? '是' : '否', "</br>";
				echo '--------------------------', "</br>";
			}
		}
	}

}
