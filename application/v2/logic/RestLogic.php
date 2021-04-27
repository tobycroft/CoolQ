<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

class RestLogic {

	public static function rest($type, $time = 60) {
		if (cache('_rest_' . $type)) {
			echo 'colling_down';
			exit();
		} else {
			cache('_rest_' . $type, 1, $time);
		}
	}

}
