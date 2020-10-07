<?php
/**
 * 荷割入力
 *
 * PageID:   G3111
 * Date:     2018/04/19
 * Author:   haominglei
 *
 **/

// セッション共通処理
//require_once ("../../application/common/comUtils/SessionCheck.php");
require_once("../common/message/MessageTool.php");
require_once("../common/Exception/SysException.php");
require_once("../common/comUtils/CommonTool.class.php");
require_once '../common/db/dbini.php';
require_once '../common/db/WorkLogTool.php';
require_once '../common/PHPMailer/mailerInit.php';

require_once("../../application/common/db/sessioninit.php");

try{


    // 非空验证
    if(!isset($_POST['email']) || CommonTool::isNullOREmpty($_POST['email'])){
        throw new SysException("I0001", '', array('ID（メールアドレス）'));
    }

//    var_dump(!CommonTool::isEmail($_POST['email']));
//
//
//    var_dump(CommonTool::isFullAngle($_POST['email']));
//
//    var_dump(!CommonTool::isContainsLowerCase(substr($_POST['email'],0,1)));
//
//
//    var_dump(CommonTool::isContainsUpperCase($_POST['email']));
//
//
//    var_dump(CommonTool::isFullAngle($_POST['email']));



    if(!isset($_POST['email']) || CommonTool::isFullAngle($_POST['email'])){
        throw new SysException("I0103", '', array('ID（メールアドレス）'));
    }
    // 第一个字母是否小写
    if(!CommonTool::isContainsLowerCase(substr($_POST['email'],0,1))){
        throw new SysException("I0103", '', array('ID（メールアドレス）'));
    }
    // 包含大写，报错
    if(CommonTool::isContainsUpperCase($_POST['email'])){
        throw new SysException("I0103", '', array('ID（メールアドレス）'));
    }
    // 包含全角，报错
    if(!isset($_POST['email']) || CommonTool::isFullAngle($_POST['email'])){
        throw new SysException("I0103", '', array('ID（メールアドレス）'));
    }
    // 是否是 email
    if(!CommonTool::isEmail($_POST['email'])){
        throw new SysException("I0103", '', array('ID（メールアドレス）'));
    }


    // 查询用户表，检测是否已经存在
    $sql = "SELECT email FROM user WHERE email = '" . mysqli_real_escape_string($db->getLink(),$_POST['email']) . "' AND delete_flag = 0";
    $result =  $db->getAll($sql);
    if(count($result) > 0){
        throw new SysException("I0075",'',array('このID'));
    }

    // 查询用户临时表，检测是否已经存在
    $time = CommonTool::getDateTimeWithMillisecond();
    $sql = "SELECT email FROM user_temp WHERE email = '" . mysqli_real_escape_string($db->getLink(),$_POST['email']) . "' AND expire_time > '".$time."'";
    $result =  $db->getAll($sql);
    if(count($result) > 0){
        throw new SysException("I0075",'',array('このID'));
    }

    // 密码非空验证
    if(!isset($_POST['password'])  || CommonTool::isNullOREmpty($_POST['password']) ){
        throw new SysException("I0001", '', array('パスワード'));
    }

    if(mb_strlen($_POST['password'],'UTF8') > 20  || mb_strlen($_POST['password'],'UTF8') < 6 || CommonTool::isFullAngle($_POST['password'])){
        throw new SysException("I0099", '', array(''));
    }


    if(!isset($_POST['name_prifix'])  ||  CommonTool::isNullOREmpty($_POST['name_suffix']) ){
        throw new SysException("I0001", '', array('氏名'));
    }

    if( CommonTool::isHalfAngle($_POST['name_prifix'])){
        throw new SysException("I0100", '', array('氏名'));
    }

    if(!isset($_POST['name_suffix'])  || CommonTool::isNullOREmpty($_POST['name_suffix'])){
        throw new SysException("I0001", '', array('氏名'));
    }

    if( CommonTool::isHalfAngle($_POST['name_suffix'])){
        throw new SysException("I0100", '', array('氏名'));
    }


    if(!isset($_POST['notation_prifix'])  || CommonTool::isNullOREmpty($_POST['notation_prifix'])){
        throw new SysException("I0001", '', array('氏名（フリガナ）'));
    }

    if( !CommonTool::isZenKatakana($_POST['notation_prifix']) ){
        throw new SysException("I0101", '', array('氏名'));
    }

    if(!isset($_POST['notation_sufix'])  || CommonTool::isNullOREmpty($_POST['notation_sufix'])){
        throw new SysException("I0001", '', array('氏名（フリガナ）'));
    }

    if(!CommonTool::isZenKatakana($_POST['notation_sufix']) ){
        throw new SysException("I0101", '', array('氏名'));
    }

    $_SESSION['temp_email'] =  $_POST['email'];
    $_SESSION['temp_password'] = $_POST['password'];
    $_SESSION['temp_name_prifix'] = $_POST['name_prifix'];
    $_SESSION['temp_name_suffix'] = $_POST['name_suffix'];
    $_SESSION['temp_notation_prifix'] = $_POST['notation_prifix'];
    $_SESSION['temp_notation_sufix'] = $_POST['notation_sufix'];

   // header("Location: /webapps/sign/signupcheck.php");


    $response = array();
    $response['status'] = 1;
    $response['message'] = "";
    echo json_encode ($response);

}catch (SysException $e) {
    //カストマイズException
    $errCode = $e->getErrCode();
    $messageStr = MessageTool::getMessageWithReplaceText($errCode, $e->getReplaceTexts());
    $response['status'] = 0;
    $response['message'] = $messageStr;
    echo json_encode ($response);
    exit;
} catch (Exception $e) {
    //$db->endTransaction(false);
    $ExceptionMessage = $e->getMessage();
    if ($ExceptionMessage == "pg_error"){
        //PostgreSQLの問題
        $messageStr = MessageTool::getMessage("S0002");
    }else{
        //原因不明エラー
        $messageStr = MessageTool::getMessage("S0001");
    }
    $response = array();
    $response['status'] = 0;
    $response['message'] = $messageStr;
    echo json_encode ($response);
    exit;
}

