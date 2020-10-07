<?php

//DB共通処理をロードする
require_once("common/message/MessageTool.php");
require_once("common/Exception/SysException.php");
require_once("common/comUtils/CommonTool.class.php");
require_once 'common/db/dbini.php';
require_once 'common/db/WorkLogTool.php';
require_once 'common/PHPMailer/mailerInit.php';
require_once("common/comUtils/unknown_access.php");

// DB共通処理をロードする
require_once("../application/common/db/sessioninit.php");

if (empty($_POST)) {
    $json_raw = file_get_contents("php://input");
    $json_data = json_decode($json_raw, true);
} else {
    $json_data = $_POST;
}


try {

    // 验证
    $email = $_POST['id'];
    if (!isset($email) || CommonTool::isNullOREmpty($email)) { // 非空
        throw new SysException("I0090", '', '');
    }

    $password = $_POST['password'];
    if (!isset($password) || CommonTool::isNullOREmpty($password)) { // 密码非空
        throw new SysException("I0090", '', '');
    }

    if(mb_strlen($password,'utf-8') > 20 || mb_strlen($password,'utf-8') < 6){
        throw new SysException("I0091", '', '');
    }

    $sql = "SELECT * FROM user WHERE email = '" . mysqli_escape_string($db->getLink(), $email) . "' AND delete_flag = 0";
    $result = $db->getAll($sql);

    $time = CommonTool::getDateTimeWithMillisecond();
    $sql = "SELECT * FROM user_temp WHERE email = '" .  mysqli_escape_string($db->getLink(), $email) . "' AND expire_time > '". $time ."'  ";
    $resultTemp = $db->getAll($sql);

    if (count($result) <= 0 && count($resultTemp) <= 0) {   //用户不存在
        throw new SysException("I0090", '', '');
    }

    if(count($result) <= 0){   // 用户表中不存在该用户
        throw new SysException("I0090", '', '');
    }

    if(count($resultTemp) > 0 && $resultTemp[0]['email'] == $email && $resultTemp[0]['password'] == $password){  // 用户名密码相同
        // 删除该临时密码
        $where = array('id'=>$resultTemp[0]['id']);
        $db->deleteOne('user_temp',$where);
        doLogin($result);
    } else if(count($resultTemp) <= 0 && $result[0]['email'] == $email && $result[0]['password'] == $password){
        doLogin($result);
    } else {
        throw new SysException("I0090", '', '');
    }

    $response = array();
    $response['status'] = 1;
    $response['message'] = "";
    echo json_encode($response);

} catch (SysException $e) {
    //カストマイズException
    $db->rollback();
    $errCode = $e->getErrCode();
    $messageStr = MessageTool::getMessageWithReplaceText($errCode, $e->getReplaceTexts());
    $response['status'] = 0;
    $response['message'] = $messageStr;
    echo json_encode($response);
    exit;
} catch (Exception $e) {
    //$db->endTransaction(false);
    $ExceptionMessage = $e->getMessage();
    if ($ExceptionMessage == "pg_error") {
        //PostgreSQLの問題
        $messageStr = MessageTool::getMessage("S0002");
    } else {
        //原因不明エラー
        $messageStr = MessageTool::getMessage("S0001");
    }
    $response = array();
    $response['status'] = 0;
    $response['message'] = $messageStr;
    echo json_encode($response);
    exit;
}

function doLogin($result){
    if (!isset($_SESSION['ssID'])) {
        $_SESSION['ssID'] = null;
    }
    $_SESSION['usr_id'] =  $result[0]['id'];
    $_SESSION['name_prifix'] = $result[0]['name_prifix'];
    $_SESSION['name_suffix'] = $result[0]['name_suffix'];
    $_SESSION['mail'] = $result[0]['email'];
    $_SESSION['ssID'] = get_ssID($result[0]['id']);
    //$_SESSION['common_base_cd'] = $mdm_usr[0]->base_cd;
    $_SESSION['SESSION_CSRF_MIDDLE_TOKEN'] = create_uuid();

    if(isset($_SESSION['no_error_count'])){
        unset($_SESSION['no_error_count']);
    }

    if(isset($_SESSION['code_error_count'])){
        unset($_SESSION['code_error_count']);
    }

    if (isset($_SESSION['loginURL'])) {
        $go_page = $_SESSION['loginURL'];
    } else {
        $go_page = "/webapps/user/mypage.php";        //以前
    }
    unset($_SESSION['loginURL']);
}


function register($db,$tempData){
    // 将临时表中的用户信息保存到真实用户表
    $db->startTransaction();

    $maxResult =  $db->findRow('user','',' max(id) maxId ');
    $id =$maxResult['maxId'] + 1;

    $currentTime = CommonTool::getDateTimeWithMillisecond();
    $data = array();
    $data['id'] = $id;
    $data['email'] = $tempData['email'];
    $data['password'] = $tempData['password'];
    $data['name_prifix'] = $tempData['name_prifix'];
    $data['name_suffix'] = $tempData['name_prifix'];
    $data['notation_prifix'] = $tempData['name_prifix'];
    $data['notation_sufix'] = $tempData['name_prifix'];
    $data['delete_flag'] = 0;
    $data['create_time'] = $currentTime;
    $data['create_by'] = $id;
    $data['update_time'] = $currentTime;
    $data['update_by'] = $id;

    $db->insert('user',$data);


    // 删除临时表中已经过期的信息。
    $where = array();
    $where['id'] = $tempData['id'];
    $db->deleteOne('user_temp',$where);
    $db->commit();

    return $data;
}



?>