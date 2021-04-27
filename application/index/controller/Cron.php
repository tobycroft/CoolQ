<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\index\controller;

use app\v1\action\GroupAction;

class Cron {

	public function index() {
//		\app\v1\action\PrivateAction::send_private_msg('710209520', '123');
//		\app\v1\action\GroupAction::send_group_msg('94537310', '123');
//		dump(bots());
//		dump(bots());
//		GroupAction::set_group_ban(640190738, 542749156, 2982145726, 2505600);
		dump(strstr('端午节啊code电击文库24', '.co'));
		echo request()->ip();
	}

	public function gtt() {
		$ret = \app\v1\action\PrivateAction::_get_vip_info('710209521');
//		$ret = GroupAction::_get_group_info(1206420783, 542749156, 1);
//		$arr = [
//			'group_id' => 542749156,
//		];
//		$ret = \app\v1\service\NetService::serv_get(1206420783, '_get_group_info', $arr);
		dump($ret);
	}

	public function delete() {
		$message_id = input('post.id');
		dump($message_id);
		\app\v1\action\CombAction::delete_msg($message_id);
	}

	public function slience_check() {
		$slience = cache('__slience__');
		\app\v2\logic\SlienceLogic::slience($slience);
	}

	public function ipl() {
		dump(cache('__IPSLIST__'));
	}

	public function ipl_check() {
		$ipl = cache('__IPSLIST__');
		\app\v3\logic\IplLogic::ipl_check($ipl);
	}

	public function slience() {
		$slience = cache('__slience__');
		dump($slience);
	}

	public function refresh_group_all() {
		set_time_limit(0);
		$bot = bots();
		db()->query('TRUNCATE `group_info`');
//		dump($bot);
		foreach ($bot as $key => $value) {
			$ret = GroupAction::get_group_list($key);
			if ($ret) {
				foreach ($ret as $v) {
					GroupAction::_get_group_info($key, $v['group_id'], false);
				}
			} else {
				dump($key);
			}
		}
	}

	public function refresh_group() {
		set_time_limit(0);
		$bot = bots();
//		db()->query('TRUNCATE `group_info`');
//		dump($bot);
		foreach ($bot as $key => $value) {
			$ret = GroupAction::get_group_list($key);
//			dump($key);

			if ($ret) {
				foreach ($ret as $value) {
					GroupAction::_get_group_info($key, $value['group_id'], false);
				}
			} else {
				dump($key);
				dump($ret);
			}
		}
	}

	public function blackexec() {
		set_time_limit(0);
		$banper = \app\v1\model\GroupBanPermenentModel::api_select(time() + 86400);
		if ($banper) {
			foreach ($banper as $value) {
				if (!isset(bots()[$value['self_id']]['port'])) {
					\app\v1\model\GroupBanPermenentModel::api_delete($value['group_id'], $value['user_id']);
					continue;
				}
//				\app\v1\action\GroupAction::set_group_ban($value['group_id'], $value['user_id'], 0, FALSE);
				if (\app\v1\model\GroupMemberModel::api_find_bySelfid($value['self_id'], $value['group_id'], $value['user_id'])) {
					if (\app\v1\action\GroupAction::set_group_ban($value['self_id'], $value['group_id'], $value['user_id'], 86400 * 28, FALSE)) {
						$tm = time() + 86400 * 28;
						echo $tm;
						if (\app\v1\model\GroupBanPermenentModel::api_update($value['group_id'], $value['user_id'], $tm)) {
							echo 'yes';
						} else {
							echo 'no';
						}

						echo $value['user_id'];
					}
				} else {
					\app\v1\model\GroupBanPermenentModel::api_delete($value['group_id'], $value['user_id']);
					echo $value['user_id'], 'exit';
				}
			}
		} else {
			echo 'noone';
		}
	}

	public function te() {
		$list = \app\v1\action\GroupAction::get_group_list(640190738);
		dump($list);
	}

	public function check_power() {
		set_time_limit(0);
		$bot = bots();
		foreach ($bot as $key => $value) {
			$ret = \app\v1\action\GroupAction::get_group_list($key);
			if ($ret) {
				\app\v2\logic\PowerLogic::power($ret, $key);
			} else {
				echo 'nogroup' . dump($key);
			}
		}
	}

	public function clear_out() {
		set_time_limit(0);
		$bot = bots();
		foreach ($bot as $key => $value) {
			$ret = \app\v1\action\GroupAction::get_group_list($key);
			if ($ret) {
				\app\v2\logic\PowerLogic::clearout($ret, $key);
			} else {
				echo 'noclear';
			}
		}
	}

	public function mem() {
		set_time_limit(0);
		$bot = bots();
		foreach ($bot as $key => $value) {
			\app\index\action\LogicAction::refresh_all_group_member($key);
		}
	}

	public function fl() {
		$t = time() - 1537805915;
		$d = floor($t / (3600 * 24));
		echo $d;
	}

	public function del() {
		$str = input('str');
		\app\index\action\RouterAction::Do_action($str);
//		$ar = cache('__punish__');
//		dump($ar);
//		dump(count(\app\v1\action\GroupAction::get_group_member_list(424503491)));
//		\app\v1\action\GroupAction::send_group_msg('94537310', 'test1[CQ:rich,file=95DC53F94879D4CA8C3942F96E4CCDD8.jpg,url=https://gchat.qpic.cn/gchatpic_new/347238190/615352952-2636636552-95DC53F94879D4CA8C3942F96E4CCDD8/0?vuin=1206420783&amp;term=2]');
//		dump(preg_match('/\.(com|cn|vip|io|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum)/', $str));
//		\app\v1\action\GroupAction::set_group_ban(94537310, 316050941, 86400 * 7, FALSE);
	}

	public function dd() {
//		$user_info = \app\v1\action\PrivateAction::_get_vip_info('137465877');
//		dump($user_info);
//		$admins = \app\v1\model\GroupMemberModel::api_select_admins($json['group_id']);
//		$adm = '';
//		foreach ($admins as $value) {
//			$adm .= "[CQ:at,qq=$value[user_id]]";
//		}
//		if (isset($user_info['level'])) {
//			if (($user_info['level'] < 16) && ($user_info['level_speed'] <= 1)) {
//		\app\v1\action\GroupAction::set_group_ban(1206420783, 94537310, 2053667486, 86400 * 7);
//				\app\v1\action\GroupAction::send_group_msg(94537310, '用户低于群设定等级，请手动解禁！' . "\n帐号137465877" . "\n" . $adm);
//			}
//		} else {
//			\app\v1\action\GroupAction::set_group_ban(94537310, 137465877, 86400 * 7);
//			\app\v1\action\GroupAction::send_group_msg(94537310, '用户无法判断等级，请手动解禁！！' . "\n帐号137465877" . "\n" . $adm);
//		}
//		$bot = bots();
//		foreach ($bot as $key => $value) {
//			$groups = GroupAction::get_group_list($key);
//			dump($groups);
//		}
	}

	public function test() {
		$umessage = '[CQ:rich,content={"detail":{"appid":"1109696408"&amp;#44;"icon":"miniapp.gtimg.cn/public/appicon/15582d98fba7029b82d0f05445dfbf23_200.jpg"&amp;#44;"title":"球球向下冲"&amp;#44;"desc":"这个游戏为啥这么好玩？咱也不知道，咱也不敢问"&amp;#44;"url":"m.q.qq.com/a/s/cad86ca1192f00cc27a91ce8fe68cc8e"&amp;#44;"preview":"qqadapt.qpic.cn/adapt/0/92ca2e5c-8ba3-5992-8930-7716982de55b/0?pt=0&amp;amp;ek=1&amp;amp;kp=1&amp;amp;sce=70-0-0"}},title=&amp;#91;QQ小程序&amp;#93;球球向下冲]';
//		echo strstr($umessage, '[CQ:rich');
		switch ($umessage) {
			case(strstr($umessage, '[CQ:rich') ? $umessage : !$umessage):
				echo '[CQ:rich';
				if (strstr($message, '[CQ:rich')) {
					echo '[CQ:rich2-' . $groupfunc['ban_share'];
					if ($groupfunc['ban_share'] && $is_admin) {
						echo '[CQ:rich2-ban';
						GroupAction::delete_msg($json['self_id'], $json['message_id']);
						\app\v1\logic\GroupLogic::logic_ban_user($json, '禁止分享', $groupfunc['ban_time']);
						break;
					}
				}
		}
	}

}
