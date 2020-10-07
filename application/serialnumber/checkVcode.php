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
require_once("../../application/common/comUtils/SessionCheck.php");
require_once("../common/message/MessageTool.php");
require_once("../common/Exception/SysException.php");
require_once("../common/comUtils/CommonTool.class.php");
require_once '../common/db/dbini.php';
require_once '../common/db/WorkLogTool.php';
require_once '../common/PHPMailer/mailerInit.php';

try {

    $response = array();
    $response['status'] = 1;

    if (verifyData()) {
        // 验证过期时间
        if ($_SESSION['v_code_expire_time'] < CommonTool::getDateTimeWithMillisecond()) {
            logout();
            $response['status'] = 2;   // 返回退出登录状态
            $response['message'] =   MessageTool::getMessage('I0014');
            $response['expire_time'] = $_SESSION['v_code_expire_time'];
            $response['time'] = CommonTool::getDateTimeWithMillisecond();
        } else if ($_SESSION['v_code'] == $_POST['code']) {// 验证是否一致
            $response['status'] = 1;  //返回成功状态
            $_SESSION['code_error_count'] = 0;
            $response['expire_time'] = $_SESSION['v_code_expire_time'];
            $response['time'] = CommonTool::getDateTimeWithMillisecond();
            unsetData();
        } else {
            if(!isset($_SESSION['code_error_count'])){
                $_SESSION['code_error_count'] = 1;
            } else {
                $_SESSION['code_error_count'] ++;
            }
            if ($_SESSION['code_error_count'] >= 3) {
                // 退出登录
                logout();
                $response['status'] = 2;   // 返回退出登录状态
                $response['message'] = MessageTool::getMessage("I0098");
                $response['expire_time'] = $_SESSION['v_code_expire_time'];
                $response['time'] = CommonTool::getDateTimeWithMillisecond();
            } else {
                $response['status'] = 0;   // 不一致
                $response['message'] = MessageTool::getMessage('I0096');
            }
        }
    }

    echo json_encode($response);


} catch (SysException $e) {
    //カストマイズException
    $errCode = $e->getErrCode();
    $messageStr = MessageTool::getMessageWithReplaceText($errCode, $e->getReplaceTexts());
    $response['status'] = 0;
    $response['message'] =  MessageTool::getMessage("I0096");
    // 错误次数加一
    addErrorCount();
    if ($_SESSION['code_error_count'] >= 3) {
        $response['status'] = 2;   // 返回退出登录状态
        $response['message'] = MessageTool::getMessage("I0098");
    }
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

function verifyData()
{

    if (!isset($_POST['code'])) {
        throw new SysException("I0096", '', '');
    }

    if (mb_strlen($_POST['code'], 'utf-8') > 4 || mb_strlen($_POST['code'], 'utf-8') < 4) {
        throw new SysException("I0096", '', '');
    }

    if (!CommonTool::isNumeric($_POST['code'])) {
        throw new SysException("I0096", '', '');
    }
    return true;
}

function logout()
{
    if (isset($_SESSION['ssID'])) {
        unset($_SESSION['ssID']);

    }

    if(isset($_SESSION['no_error_count'])){
        unset($_SESSION['no_error_count']);
    }

    if(isset($_SESSION['code_error_count'])){
        unset($_SESSION['code_error_count']);
    }

}

function unsetData(){

    if(isset($_SESSION['v_code_expire_time'])){
        unset($_SESSION['v_code_expire_time']);
    }

    if(isset($_SESSION['no_error_no1'])){
        unset($_SESSION['no_error_no1']);
    }

    if(isset($_SESSION['no_error_no2'])){
        unset($_SESSION['no_error_no2']);
    }

    if(isset($_SESSION['no_error_no3'])){
        unset($_SESSION['no_error_no3']);
    }

    if(isset($_SESSION['no_error_no4'])){
        unset($_SESSION['no_error_no4']);
    }

    if(isset($_SESSION['no_error_count'])){
        unset($_SESSION['no_error_count']);
    }



}

function addErrorCount()
{
    if (isset($_SESSION['code_error_count'])) {
        $_SESSION['code_error_count']++;
    } else {
        $_SESSION['code_error_count'] = 1;
    }
}






