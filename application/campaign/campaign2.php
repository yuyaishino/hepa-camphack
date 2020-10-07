<?php
/**
 * キャンペーン応募
 * User: guyating
 * Date: 2020/9/11
 * Time: 4:03
 */
require_once ("../../application/common/db/sessioninit.php");
require_once("../common/comUtils/SessionCheck.php");
require_once("../common/message/MessageTool.php");
require_once("../common/Exception/SysException.php");
require_once("../common/comUtils/CommonTool.class.php");
require_once '../common/db/dbini.php';


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
    }  else if ($operate == 'add') {
    	addAndupdateDb($db,$receiveData);
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
	if(isset($_SESSION['isRecruit']) && $_SESSION['isRecruit'] == "point"){
		header("Location: /webapps/user/user.php");
	}
	//$verificationCode = $_SESSION['verificationCode'];
	$receiveList = $_SESSION['campaign'];
	$responseData = array(/* 
		"verificationCode" => $verificationCode, */
		"campaign" => $receiveList
	);
	if ($receiveList) {
		$data ['status'] = 1;
		$data ['message'] = '';
		$data ['responseData'] = $responseData;
		echo json_encode($data);
		exit();
	}
}

function addAndupdateDb($db,$receiveData){
    $responseData = array(); // return data
    $insertSuccessTag = 1; // success 1, failure 0
    //Main
    $curr_dttm = CommonTool::getDateTimeWithMillisecond();
    checkAddPostMainData($db,$receiveData['verificationCode'],$receiveData['countDiff'],$curr_dttm);
    $receiveList = $_SESSION['campaign'];
    $applyTime = CommonTool::formatDate("now",'Y/m/d');
    
    
    $selectonesql = "select postcode, email,IFNULL(point ,0) AS point from user where id=".$_SESSION['usr_id'];
    $row = $db->getRow($selectonesql);

    /* if($row && !CommonTool::isNullOREmpty($row['postcode'])){ */
    if($row){
    	
	    $needPoint = 0;
	    $insRes = 0;
	    foreach ($receiveList AS $edata){
	    	$insData = array(
	    			'user_id' => $_SESSION['usr_id'],
	    			'apply_count' => $edata['apply_count'],
	    			'time' => $applyTime,
	    			'create_time' => $curr_dttm,
	    			'create_by' => $_SESSION['usr_id'],
	    			'update_time' => $curr_dttm,
	    			'update_by' => $_SESSION['usr_id']
	    	);
	    	switch ($edata['type']) {
	    		case "A":
	    			$insData['type'] = 0;
	    			$needPoint = $needPoint + 20*$edata['apply_count'];
	    			break;
	    		case "B":
	    			$insData['type'] = 1;
	    			$insData['color'] = $edata['color'];
	    			$needPoint = $needPoint + 15*$edata['apply_count'];
	    			break;
	    		case "C":
	    			$insData['type'] = 2;
	    			$needPoint = $needPoint + 10*$edata['apply_count'];
	    			break;
	    		case "D":
	    			$insData['type'] = 3;
	    			$needPoint = $needPoint + 2*$edata['apply_count'];
	    			break;
	    		case "E":
	    			$insData['type'] = 4;
	    			$needPoint = $needPoint + 1*$edata['apply_count'];
	    			break;
	    		default:
	    			break;
	    	}
	    	$insRes = $db->insert('apply', $insData);
	    	if ($insRes <= 0) {
	    		$insertSuccessTag = 0;
	    		break;
	    	}
	    }
	    if($insRes>0 ){
	    	$point = $row['point'] - $needPoint;
	    	$newPoint = array(
	    			'point' => $point,
	    			'update_time' => $curr_dttm,
	    			'update_by' => $_SESSION['usr_id']
	    	);
	    	$uptRes = $db->update('user', $newPoint, array('id' => $_SESSION['usr_id']));
	    	if ($uptRes <= 0) {
	    		$insertSuccessTag = 0;
	    	}
	    }
	    if ($insertSuccessTag == 1) {
	    	if(isset($_SESSION['isRecruit'])){
	    		unset($_SESSION['isRecruit']);
	    	}
	    	if(isset($_SESSION['expireTime'])){
	    		unset($_SESSION['expireTime']);
	    		unset($_SESSION['verificationCode']);
	    		unset($_SESSION['campaign']);
	    	}
	    	$responseData['status'] = 1;
	    }
	    if(CommonTool::isNullOREmpty($row['postcode'])){
	    	$responseData['status'] = 2;
	    }
    }else{
/*     	$_SESSION['isRecruit'] = "point";
    	$responseData['status'] = 2; */
    	throw new SysException("S0002", '', '');
    }
    if ($insertSuccessTag == 1) {
    	$db->endTransaction(true);
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

function checkAddPostMainData($db,$newCode,$countDiff,$curr_dttm){
	
	if((!isset($_SESSION['expireTime'])) || (!isset($_SESSION['verificationCode']))){
		throw new SysException("I0014", '', '');
	}
	$expireTime = $_SESSION['expireTime'];
	$oldCode = $_SESSION['verificationCode'];
	if(!isset($newCode)){
		throw new SysException("I0096", '', '');
	}
	if($newCode != $oldCode){
		if($countDiff >= 2){
			if(isset($_SESSION['ssID'])){
				unset($_SESSION['ssID']);
				unset($_SESSION['no_error_count']);
				unset($_SESSION['expireTime']);
				unset($_SESSION['verificationCode']);
				unset($_SESSION['campaign']);
			}
			throw new SysException("I0098", '', '');
		}
		throw new SysException("I0096", '', '');
	}
	if($expireTime < $curr_dttm){
		if(isset($_SESSION['ssID'])){
			unset($_SESSION['ssID']);
			unset($_SESSION['no_error_count']);
			unset($_SESSION['expireTime']);
			unset($_SESSION['verificationCode']);
			unset($_SESSION['campaign']);
		}
		throw new SysException("I0014", '', '');
	}
	
}
