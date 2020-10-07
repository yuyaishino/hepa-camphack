<?php

//DB共通処理をロードする
require_once("common/message/MessageTool.php");
require_once("common/Exception/SysException.php");
require_once("common/comUtils/CommonTool.class.php");
require_once 'common/db/dbini.php';
require_once 'common/db/WorkLogTool.php';
require_once 'common/PHPMailer/mailerInit.php';
require_once("common/comUtils/unknown_access.php");
require_once ("common/db/sessioninit.php");

if(isset($_SESSION['ssID'])){
    unset($_SESSION['ssID']);
    if(isset($_SESSION['isRecruit'])){
    	unset($_SESSION['isRecruit']);
    }
    if(isset($_SESSION['expireTime'])){
    	unset($_SESSION['expireTime']);
    	unset($_SESSION['verificationCode']);
    	unset($_SESSION['campaign']);
    }
}

if(isset($_SESSION['no_error_count'])){
    unset($_SESSION['no_error_count']);
}

header("Location:/");



?>