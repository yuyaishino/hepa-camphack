<?php
/**
 * Created by PhpStorm.
 * User: 84770
 * Date: 2020/9/16
 * Time: 13:47
 */

$http_base = 'https://cmp-serial.nrir1test.jp';

$request_path = '/serialchk/CAGC001.do';

$timeout = 10;

$campid = '66';

$user='hepa2020';

$password='hepacmptest';

//--------------------
//$GLOBALS['error_log_path'] = '/data/vhosts/1/logs/php_error.log';
$GLOBALS['error_log_path'] = 'C:\Apache24\htdocs\hepa-camphack\logs\php_error.log';
$GLOBALS['send_log_path'] = 'C:\Apache24\htdocs\hepa-camphack\logs\php_send.log';
$api_url = $http_base . $request_path;



