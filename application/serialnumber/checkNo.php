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
require_once '../common/globalConfig.php';

try {

    $response = array();
    $response['status'] = 1;
    $response['message'] = "";

    $db->startTransaction();

    if (verifyData()) {

        //通过验证
        $no = $_POST['no1'] . $_POST['no2'] . $_POST['no3'] . $_POST['no4'];
        $sql = "SELECT * FROM no WHERE no='" . $no . "'";
        $result = $db->getAll($sql);

        // 判断no 是否已经被使用
        if (sizeof($result) > 0) {
            // 如果出错，则将 error_no 放到 session 中
            //page2 将会用到该数据
            $msg = MessageTool::getMessageWithReplaceText("I0094", '');
            $_SESSION['no_error_message'] =$msg;
            $db->endTransaction(false);

            $db->startTransaction();
            $logData = array('status'=>0,'no' => $no,'message'=> $msg);
            insertLog($db,$logData);
            $db->endTransaction(true);

            throw new SysException("I0094", '', '');
        }


        //var_dump($api_url);

        //3.  请求服务器
        //$reqData = array('campid' => $campid, 'serialno' => $no);
        $url = $api_url . '?campid=' . $campid . '&serialno=' . $no;

        list($tmpInfo,$http_code,$status) = CommonTool::curl_get_https($url,$timeout,$user,$password);
        if(!$status){
            if(isset( $_SESSION['no_error_count'])){
                $_SESSION['no_error_count'] --;
            } else {
                $_SESSION['no_error_count'] = -1;
            }

            // 保存错误日志
            $msg = MessageTool::getMessageWithReplaceText("I0102", '');
            $_SESSION['no_error_message'] =$msg;
            $db->endTransaction(false);

            $db->startTransaction();
            $logData = array('status'=>0,'no' => $no,'message'=> $msg,'http_resp_code'=> $http_code);
            insertLog($db,$logData);
            $db->endTransaction(true);

            // 抛出异常
            throw new SysException("I0102", '', '');
        }

        $receiveData = json_decode($tmpInfo, true);

       // var_dump($receiveData);
        //4. 根据返回信息，做相应的处理
        if ($receiveData['result'] == '0000') {
            $currentTime = CommonTool::getDateTimeWithMillisecond();
            $userId = $_SESSION['usr_id'];
            $db->insert('no', array(
                'no' => $no,
                'create_time' => $currentTime,
                'create_by' => $userId,
                'update_time' => $currentTime,
                'update_by' => $userId
            ));

            $logData = array('status'=>1,'no' => $no,'http_resp_code'=> $http_code);
            insertLog($db,$logData);

        } else if ($receiveData['result'] == '0020') {
            //insert log
            $msg = MessageTool::getMessageWithReplaceText("I0097", '');
            $_SESSION['no_error_message'] =$msg;
            $logData = array('status'=>0,'no' => $no,'message'=> $msg,'http_resp_code'=> $http_code);
            insertLog($db,$logData);
            $db->endTransaction(true);
            throw new SysException("I0097", '', '');
        } else {
            if(isset(  $_SESSION['no_error_count'])){
                $_SESSION['no_error_count'] --;
            }

            //insert log
            $msg = MessageTool::getMessageWithReplaceText("I0102", '');
            $logData = array('status'=>0,'no' => $no,'message'=> $msg,'http_resp_code'=> $http_code);
            insertLog($db,$logData);
            $db->endTransaction(true);

            throw new SysException("I0102", '', '');
        }


        // 查询当前用户信息
        $resultUser = $db->findRow('user', array('email' => $_SESSION['mail']));

        if($resultUser['point'] == null){
            $resultUser['point'] = 0;
        }
        $resultUser['point']++;


        $status = $db->update('user',
            array('point' => $resultUser['point']),
            array('email' => $_SESSION['mail']));
        if ($status) {
            $response['data'] = array('point' => $resultUser['point']);
            $_SESSION['no_error_count'] = 0;
            $db->commit();
        } else {
            $response['status'] = 0;
            $response['status'] = $messageStr = MessageTool::getMessage("S0001");;
        }
    }


    echo json_encode($response);

} catch (SysException $e) {
    $db->rollback();
    //カストマイズException
    $errCode = $e->getErrCode();
    $messageStr = MessageTool::getMessageWithReplaceText($errCode, $e->getReplaceTexts());
    $response['status'] = 0;
    $response['message'] = $messageStr;

    // 错误次数加一
    addErrorCount();
    if ($_SESSION['no_error_count'] >= 3) {
        $response = handleError($response, $db, $mailer);
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
    if (!isset($_POST['no1'])) {
        throw new SysException("I0093", '', '');
    }

    if (!isset($_POST['no2'])) {
        throw new SysException("I0093", '', '');
    }

    if (!isset($_POST['no3'])) {
        throw new SysException("I0093", '', '');
    }

    if (!isset($_POST['no4'])) {
        throw new SysException("I0093", '', '');
    }

    if (mb_strlen($_POST['no1'], 'utf-8') > 4 || mb_strlen($_POST['no1'], 'utf-8') < 4) {
        throw new SysException("I0093", '', '');
    }

    if (mb_strlen($_POST['no2'], 'utf-8') > 4 || mb_strlen($_POST['no2'], 'utf-8') < 4) {
        throw new SysException("I0093", '', '');
    }

    if (mb_strlen($_POST['no3'], 'utf-8') > 4 || mb_strlen($_POST['no3'], 'utf-8') < 4) {
        throw new SysException("I0093", '', '');
    }

    if (mb_strlen($_POST['no4'], 'utf-8') > 4 || mb_strlen($_POST['no4'], 'utf-8') < 4) {
        throw new SysException("I0093", '', '');
    }

    if (!CommonTool::isNumeric($_POST['no1'])) {
        throw new SysException("I0093", '', '');
    }

    if (!CommonTool::isNumeric($_POST['no2'])) {
        throw new SysException("I0093", '', '');
    }
    if (!CommonTool::isNumeric($_POST['no3'])) {
        throw new SysException("I0093", '', '');
    }

    if (!CommonTool::isNumeric($_POST['no4'])) {
        throw new SysException("I0093", '', '');
    }

    return true;
}

function addErrorCount()
{
    if (isset($_SESSION['no_error_count'])) {
        $_SESSION['no_error_count']++;
    } else {
        $_SESSION['no_error_count'] = 1;
    }
}


function handleError($response, $db, $mailer)
{
    $response['status'] = 2;

    $where = array('id' => $_SESSION['usr_id']);
    $result = $db->findRow('user', $where);

    $_SESSION['no_error_point'] = $result['point'];

    $_SESSION['v_code'] = CommonTool::randomCode();
//    $body = '<label>'.  $_SESSION['name_prifix'] . $_SESSION['name_suffix'] . '様  </label><br>'
//        . ' <label>シリアル№入力にて3回エラーとなりました。</label><br>'
//        . ' <label>認証コードの入力が必要となります。下記の認証コードを画面より入力ください。</label><br>'
//        . ' <label>' . $_SESSION['v_code'] . '</label><br>'
//        . ' <label> 10分以内に上記認証コードを入力ください。</label><br>';
    $body = '<label>'.  $_SESSION['name_prifix'] . $_SESSION['name_suffix'] .'様</label><br><br>'
        . ' <label>ヘパリーゼキャンプグッズプレゼントキャンペーン事務局です。</label><br><br>'
        . ' <label>シリアル№入力にて3回エラーとなりました。</label><br>'
        . ' <label>認証コードの入力が必要となります。下記の認証コードを画面より入力ください。</label><br>'
        . ' <label>"'.$_SESSION['v_code'].'"</label><br><br>'
        . ' <label>※10分以内に上記認証コードを入力していただかないと、認証コードが無効となります。</label><br>'    
        . ' <label>※このメールに心当たりがございませんでしたら、お手数をおかけしますが、削除していただきますようお願いいたします。</label><br>'
        . ' <label>※このメールの送信アドレスは送信専用となりますので、返信できません。</label><br>';
    $status = $mailer->sendOne( $_SESSION['mail'] , $body, '本人確認用の認証コードです');

    if ($status) {

        $_SESSION['v_code_expire_time'] = CommonTool::getExpireTime("+10 minute");

        $_SESSION['no_error_no1'] = $_POST['no1'];
        $_SESSION['no_error_no2'] = $_POST['no2'];
        $_SESSION['no_error_no3'] = $_POST['no3'];
        $_SESSION['no_error_no4'] = $_POST['no3'];
    } else {
        $response['status'] = 0;
        $response['message'] = $messageStr = MessageTool::getMessage("S0001");
    }
    return $response;
}

function insertLog($db,$data){
    $data['create_time'] = CommonTool::getDateTimeWithMillisecond();
    $data['user_id'] = isset( $_SESSION['usr_id']) ? $_SESSION['usr_id'] : null;
    $data['ip'] = $_SERVER['REMOTE_ADDR'];
    $db->insert('user_no_log',$data);
}





