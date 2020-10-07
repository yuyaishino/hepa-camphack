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
require_once ("../../application/common/db/sessioninit.php");
require_once ("../../application/common/comUtils/SessionCheck.php");
require_once("../common/message/MessageTool.php");
require_once("../common/Exception/SysException.php");
require_once("../common/comUtils/CommonTool.class.php");
require_once '../common/db/dbini.php';
require_once '../common/db/WorkLogTool.php';
require_once '../common/PHPMailer/mailerInit.php';

try{

    $responseData = array();

    $id = $_SESSION['usr_id'];
    $where = array();
    $where['id'] = $id;
    $result = $db->findRow('user',$where,'point');

    if( $result['point'] == null){
        $responseData['point'] = 0;
    } else {
        $responseData['point'] = $result['point'];
    }


    $response = array();
    $response['status'] = 1;
    $response['message'] = "";
    $response['data'] = $responseData;
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

