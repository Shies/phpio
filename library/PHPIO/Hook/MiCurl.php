<?php
/*
This module for inject xiaomi X5 protocol
*/
class PHPIO_Hook_MiCurl extends PHPIO_Hook_Curl {
	function CURLOPT_POSTFIELDS_rewrite($value) {
		do {
			if ( !is_string($value) ) {
				break;
			}

			parse_str($value, $params);
			if ( !isset($params['data']) || !is_string($params['data']) ) {
				break;
			}

			$params['data'] = $this->x5_decode($params['data']);
			if ( !is_array($params['data']) || 
				 !isset($params['data']['header']['method']) ) {
				break;
			}
			// because the [gw] will combind header.url+header.method as new request URL
			$params['data']['header']['method'] .= '?XDEBUG_PROFILE='.PHPIO::$run_id;
			$params['data'] = $this->x5_encode($params['data']);
			$value = http_build_query($params);
		} while (0);

		return $value;
	}

	function x5_encode($value) {
		return base64_encode(json_encode($value));
	}

	function x5_decode($value) {
		return json_decode(base64_decode($value), true);
	}
}