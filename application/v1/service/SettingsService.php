<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\service;

/**
 * Description of ParamService
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class SettingsService {

	//put your code here

	public static function serv_get($param) {
		return \app\v1\model\SystemParamModel::api_find($param);
	}

	public static function serv_bool($param) {
		return (boolean) \app\v1\model\SystemParamModel::api_find($param);
	}

}
