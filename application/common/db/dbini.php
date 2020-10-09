<?php
/**
 * DB情報
 */

//$host = 'localhost';
//$user = 'h7e49001';
//$pass = 'i2R6NemY';
//$db = 'h7e49001';
//$port = 3306;
//$charset = 'utf8';
$host = 'localhost';
$user = 'root';
$pass = 'password';
$db = 'h7e49001';
$port = 3306;
$charset = 'utf8';

require_once 'Db.class.php';

$db = Db::getIntance($host,$port,$user,$pass,$db,$charset);


?>
