<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v3\logic;

/**
 * Description of AdLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class AdLogic {

	public static function api_adlogic($message) {
		$adlist = \app\v3\model\PluginAdListModel::api_select();
		dump($adlist);
		foreach ($adlist as $value) {
			$death = 0;
			unset($value['id']);
			$cor = $value['cor'];
			unset($value['cor']);
			foreach ($value as &$v) {
				if (!empty($v)) {
					echo $v;
					if (strstr($message, $v)) {
						$death++;
					}
				}
			}
			if ($death >= $cor) {
				return true;
			}
			unset($cor);
		}
	}

}
