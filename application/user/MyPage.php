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
require_once("../common/message/MessageTool.php");
require_once("../common/Exception/SysException.php");
require_once("../common/comUtils/CommonTool.class.php");
require_once '../common/db/dbini.php';
require_once '../common/db/WorkLogTool.php';
require_once '../common/PHPMailer/mailerInit.php';
require_once ("../../application/common/comUtils/SessionCheck.php");



try{


    $responseData = array();

    $id = $_SESSION['usr_id'];
    $where = array();
    $where['id'] = $id;
    $result = $db->findRow('user',$where,'point,name_prifix,name_suffix');


    $responseData['point'] = $result['point'];
    $responseData['name_prifix'] = $result['name_prifix'];
    $responseData['name_suffix'] = $result['name_suffix'];


//    $sql = "SELECT * FROM apply WHERE user_id = '" . $id . "' ORDER BY type,time";
    $sql = "SELECT user_id,type,sum(apply_count) as apply_count,max(time) as time, color FROM apply WHERE user_id = '" . $id . "' group by type,color  ORDER BY type,time,color ";
    $prizeList = $db->getAll($sql);


    for($i = 0 ; $i < sizeof($prizeList) ; $i ++ ){
        $prizeList[$i]['time'] = CommonTool::formatDate( $prizeList[$i]['time'],'Y/m/d');
    }


    $responseData['prizeList'] = $prizeList;



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

