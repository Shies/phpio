# PHPIO

## Install aop extension
pecl install aop-beta

[aop]  
extension=aop.so 

if coredump ?  
1. change you aop extension version (the pecl version is stable than git)  
2. rebuild php

## prepend phpio.php
php.ini  
auto_prepend_file = /path/to/phpio/phpio.php  
php-fpm  
php_admin_value[auto_prepend_file] = /path/to/phpio/phpio.php

## start profile
run you php programs with param XDEBUG_PROFILE  
$_REQUEST['XDEBUG_PROFILE']  
$_COOKIE['XDEBUG_PROFILE']  
$_SERVER['XDEBUG_PROFILE']  

for example : 
http://www.example.com/index.php?XDEBUG_PROFILE=1 

recommand firefox user use [easy-xdebug], it can auto append $_COOKIE['XDEBUG_PROFILE'] in requst: 
https://addons.mozilla.org/firefox/addon/easy-xdebug-with-moveable-/

## view profile
http://www.yousite.com/phpio/www/index.php   
http://www.yousite.com/phpio/www/index.php?profile=[profile_id]
