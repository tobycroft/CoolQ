<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

/**
 * Description of OcrLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class OcrLogic {

	public static function ocr($img_url) {
		$arr = [
			'img_url' => $img_url,
			'engine' => 'tesseract',
		];
		\Net::post_json('http://10.0.0.164/ocr', $arr);
	}

}
