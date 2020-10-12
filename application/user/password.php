<?php
/**
 * パスワード
 * User: guyating
 * Date: 2020/9/8
 * Time: 4:03
 */
require_once ("../../application/common/db/sessioninit.php");
require_once("../common/comUtils/SessionCheck.php");
require_once("../common/message/MessageTool.php");
require_once("../common/Exception/SysException.php");
require_once("../common/comUtils/CommonTool.class.php");
require_once '../common/db/dbini.php';
require_once '../common/PHPMailer/mailerInit.php';


try {
    //リクエストの種類
    $operate = null;
    if (isset($_POST['operate'])) {
        $operate = $_POST['operate'];
    } else {
        //リクエスト種類が間違い
        throw new SysException("S0002");
    }
    $operate = $_POST['operate'];
    if (!isset($_POST['data'])) {
        throw new SysException("S997", '', '');
    }
    $receiveData = json_decode($_POST['data'], true);
    if ($operate == 'search') {
        searchData($db);
    } else if ($operate == 'changepw') {
    	addAndupdateDb($db, $receiveData, $mailer);
    } 
} catch (SysException $e) {
	$db->endTransaction(false);
	//カストマイズException
	$errCode = $e->getErrCode();
	$messageStr = MessageTool::getMessageWithReplaceText($errCode, $e->getReplaceTexts());
	$response['status'] = 0;
	$response['message'] = $messageStr;
	echo json_encode($response);
	exit;
} catch (Exception $e) {
	$db->endTransaction(false);
	$ExceptionMessage = $e->getMessage();
	if ($ExceptionMessage == "pg_error") {
		//PostgreSQLの問題
		$messageStr = MessageTool::getMessage("S0005");
	} else {
		//原因不明エラー
		$messageStr = MessageTool::getMessage("S0001");
	}
	$response['status'] = 0;
	$response['message'] = $messageStr;
	echo json_encode($response);
	exit;
}


/**
 * 表示ボタン
 * @param $db
 * @param $receiveData
 */
function searchData($db)
{
	$intoData = array();
	$selectonesql = "select * from user where id=".$_SESSION['usr_id'];
	$row = $db->getRow($selectonesql);
	if ($row) {
		$intoData = $row;
	}
	
    $data ['status'] = 1;
    $data ['message'] = '';
    $data ['responseData'] = $intoData;
    echo json_encode($data);
    exit();
}

function addAndupdateDb($db, $receiveData, $mailer){
    $responseData = array(); // return data
    $insertSuccessTag = 1; // success 1, failure 0
    $db->startTransaction();
    
    $taita_id = 0;
    $curr_dttm = CommonTool::getDateTimeWithMillisecond();
    //Main
    checkAddPostMainData($receiveData);
    
    $id = $_SESSION['usr_id'];
    $selectonesql = "select id,email,update_time,name_prifix,name_suffix from user where id=".$id;
    $row = $db->getRow($selectonesql);
    if ($row) {
    		//排他
    		$dbUpd_dttm = $row['update_time'];
    		if($receiveData['update_time'] != null && $receiveData['update_time'] != $dbUpd_dttm){
    			throw new SysException('E0005');
    		}
    		$uptData = array(
    				'password' => $receiveData['cpassword'],
    				'update_time' => $curr_dttm,
    				'update_by' => $_SESSION['usr_id']
    		);
    		$uptRes = $db->update('user', $uptData, array('id' => $id));
/*     		$uptLog = CommonTool::updateWorkLog($db, 'user', $user, $uptData, $row,'user', false);
 */    		
    		if ($uptRes <= 0) {
    			$insertSuccessTag = 0;
    		}
    }else{
    	throw new SysException('E0001', '', '');
    }
    
    if ($insertSuccessTag == 1) {
    	$db->endTransaction(true);
    	
    	//送信
//    	$body = $row['name_prifix'].$row['name_suffix']."様<br />
//                                                  パスワード変更を受け付けました。<br />
//                                                 心当たりがございませんでしたら、事務局までご連絡ください。<br />
//        ";
        $body = '<label>'.  $_SESSION['name_prifix'] . $_SESSION['name_suffix'] .'様</label><br><br>'
        . ' <label>ヘパリーゼキャンプグッズプレゼントキャンペーン事務局です。</label><br><br>'
        . ' <label>パスワード変更を受け付けました。</label><br><br>'
        . ' <label>※このメールに心当たりがございませんでしたら、お手数をおかけしますが、下記事務局までお問い合わせください。</label><br>'
        . ' <label>※このメールの送信アドレスは送信専用となりますので、返信できません。</label><br>';
    	$mailer->sendOne($_SESSION['mail'],$body,'パスワード変更を受け付けました。');
    	
        $responseData['status'] = 1;
        $responseData['message'] = MessageTool::getMessage("R0004");
        $responseData['responseData'] = array();
        echo json_encode($responseData);
        exit();
    } else {
        $db->endTransaction(false);
        $responseData['status'] = 0;
        $responseData['message'] = '';
        $responseData['responseData'] = '';
        echo json_encode($responseData);
        exit();
    }
}

function checkAddPostMainData($params){
	if($params['npassword'] == null || $params['npassword'] == ""){
		throw new SysException('I0008', '', '');
	}
	if((!CommonTool::isPassword($params['npassword'])) || strlen($params['npassword']) < 6 || strlen($params['npassword']) > 20){
		throw new SysException('I0008', '', '');
	}
	
	if($params['npassword'] != $params['cpassword']){
		throw new SysException('I0009', '', '');
	}
	
}
