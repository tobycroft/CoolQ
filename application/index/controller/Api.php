<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\index\controller;

/**
 * Description of Api
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class Api {

	//put your code here
	public function clear_all() {
		set_time_limit(0);
		\app\v2\action\ClearAction::check_exists();
	}

}
