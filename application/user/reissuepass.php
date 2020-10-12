<?php
/**
 * パスワード
 * User: guyating
 * Date: 2020/9/8
 * Time: 4:03
 */
require_once("../common/message/MessageTool.php");
require_once("../common/Exception/SysException.php");
require_once("../common/comUtils/CommonTool.class.php");
require_once '../common/db/dbini.php';
require_once '../common/PHPMailer/mailerInit.php';

require_once ("../../application/common/db/sessioninit.php");

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
    if ($operate == 'changepw') {
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
    
    $curr_dttm = CommonTool::getDateTimeWithMillisecond();
    //Main
    checkAddPostMainData($receiveData);
    $email = $receiveData['email'];
    $selusersql = "select id,email,update_time,name_prifix,name_suffix from user where binary email='".$email."'";
    $userRow = $db->getRow($selusersql);
    
    if(isset($userRow) ){
    	if($userRow['email'] != $email){
    		throw new SysException("I0010", '', '');
    	}
    	$newpassword = CommonTool::generatePassword(8);
    	$nexpire_time = CommonTool::getExpireTime("+10 minute");
    	$selectonesql = "select id,expire_time from user_temp where email='".$email."'";
    	$usertempRow = $db->getRow($selectonesql);
    	if(isset($usertempRow)){
    		$newData = array(
    				'email' => $receiveData['email'],
    				'password' => $newpassword,
    				'expire_time' => $nexpire_time,
    				'update_time' => $curr_dttm
    		);
    		$uptRes = $db->update('user_temp', $newData, array('id' => $usertempRow['id']));
    		if ($uptRes <= 0) {
    			$insertSuccessTag = 0;
    		}
    	}else{
    		$newData = array(
    				'email' => $receiveData['email'],
    				'password' => $newpassword,
    				'expire_time' => $nexpire_time,
    				'update_time' => $curr_dttm,
    				'create_time' => $curr_dttm
    		);
    		$addRow = $db->insert('user_temp',$newData);
    		
    		if ($addRow == false){
    			$insertSuccessTag = 0;
    			return false;
    		}
    	}
    }else{
    	throw new SysException("I0010", '', '');
    }
    
    if ($insertSuccessTag == 1) {
    	$db->endTransaction(true);
    	
    	//送信
//    	$body = $userRow['name_prifix'].$userRow['name_suffix']."様<br />
//                                                  ワンタイムパスワードを発行いたします。<br />
//                                                 有効時間は10分間です。<br />
//                                                 ログイン後に必ずパスワード変更をお願いいたします。<br />
//                                                 ワンタイムパスワード <br />
//                <b>".$newpassword."</b>
//                <br />です。 <br />
//                                                 心当たりがございませんでしたら、事務局までご連絡ください。<br />
//        ";
        
        $body = '<label>'.  $_SESSION['temp_name_prifix'] . $_SESSION['temp_name_suffix'] .'様</label><br><br>'
        . ' <label>ヘパリーゼキャンプグッズプレゼントキャンペーン事務局です。</label><br><br>'
        . ' <label>ワンタイムパスワードを発行いたします。</label><br>'
        . ' <label>有効時間は10分間です。</label><br>'
        . ' <label>ログイン後に必ずパスワード変更をお願いいたします。</label><br><br>'
        . ' <label>ワンタイムパスワード</label><br>'
        . ' <label>"'.$newpassword.'"</label><br><br>'        
        . ' <label>※このメールに心当たりがございませんでしたら、お手数をおかけしますが、下記事務局までお問い合わせください。</label><br>'
        . ' <label>※このメールの送信アドレスは送信専用となりますので、返信できません。</label><br>';
        
    	$mailer->sendOne($email,$body,'ワンタイムパスワードを発行いたします。');
    	
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
	if($params['email'] == null || $params['email'] == ""){
		throw new SysException('I0010', '', '');
	}
/* 	if((!CommonTool::isEmail($params['email']))){
		throw new SysException('I0008', '', '');
	} */
}
