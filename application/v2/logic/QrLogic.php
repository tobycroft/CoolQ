<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\v2\logic;

/**
 * Description of QrLogic
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class QrLogic {

	public static function qr_detect($url) {
		$url_e = urlencode($url);
		$raw = file_get_contents('http://zxing.org/w/decode?u=' . $url_e);
	}

}
