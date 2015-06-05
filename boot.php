<?php
set_time_limit(0);
error_reporting(E_ALL ^ E_NOTICE);

if ( !extension_loaded('aop') ) dl('aop.so');
ini_set("aop.enable", "1");
ini_set('memory_limit', -1);
ini_set('unserialize_callback_func', 'phpio_unserialize_fail');

define('PHPIO_ROOT' , __DIR__);
define('PHPIO_LIB'  , PHPIO_ROOT.'/library');
define('PHPIO_FUNC' , PHPIO_ROOT.'/functions');
define('PHPIO_TMP' , '/tmp/phpio');

if ( !file_exists(PHPIO_TMP) ) {
	mkdir(PHPIO_TMP, 0777, true);
}

require PHPIO_ROOT.'/config.php';
if ( file_exists(PHPIO_ROOT.'/config.my.php') ) {
	require PHPIO_ROOT.'/config.my.php';
}

require PHPIO_FUNC.'/common.func.php';
phpio_load();

foreach ( $_PHPIO as $_name => $_value ) {
	if ( isset($_value['class']) ) {
		PHPIO::$$_name = new $_value['class'];
		phpio_class_set_properties(PHPIO::$$_name, $_value);
	} else {
		if ( property_exists('PHPIO', $_name) ) {
			PHPIO::$$_name = $_value;
		}
	}
}

