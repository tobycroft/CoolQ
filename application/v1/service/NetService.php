<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v1\service;

class NetService {

	private static function array_to_param($array) {
		$str = '';
		foreach ($array as $key => $value) {
			$str .= '&' . $key . '=' . $value;
		}
		return $str;
	}

	public static function serv_get($self_id, $path, $array = []) {
		$curl = curl_init();
		//设置抓取的url
		if (isset(bots()[$self_id]['port'])) {
			curl_setopt($curl, CURLOPT_URL, bots()[$self_id]['api_url'] . ':' . bots()[$self_id]['port'] . DS . $path . '?' . self::array_to_param($array));
			//设置头文件的信息作为数据流输出
//		curl_setopt($curl, CURLOPT_HEADER, 1);
			//设置获取的信息以文件流的形式返回，而不是直接输出。
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_TIMEOUT, 2);
			//执行命令
			$data = curl_exec($curl);
			//关闭URL请求
			curl_close($curl);
			//显示获得的数据
			return json_decode($data, 1);
		}
	}

	public static function serv_post($self_id, $path, $array = []) {
		$curl = curl_init();
		//设置抓取的url
		if (isset(bots()[$self_id]['port'])) {
			curl_setopt($curl, CURLOPT_URL, bots()[$self_id]['api_url'] . ':' . bots()[$self_id]['port'] . DS . $path);
			$data_string = json_encode($array);
			//设置头文件的信息作为数据流输出
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string)
			));
			//设置获取的信息以文件流的形式返回，而不是直接输出。
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_TIMEOUT, 2);
			//设置post方式提交
			curl_setopt($curl, CURLOPT_POST, 1);
			//设置post数据
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
			//执行命令
			$data = curl_exec($curl);
			//关闭URL请求
			curl_close($curl);
			//显示获得的数据
			return json_decode($data, 1);
		}
	}

}
