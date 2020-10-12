<?php
/**
 * 会員情報
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
    } else if ($operate == 'add') {
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
		if(isset($row['birthdate'])){
		$intoData['year'] = CommonTool::getYearOfDate($row['birthdate']);
		$intoData['month'] = number_format(CommonTool::getMonthOfDate($row['birthdate']));
		$intoData['day'] = number_format(CommonTool::getDayOfDate($row['birthdate']));
		}
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
    $itemPostData = $receiveData;
    $itemPostData['birthdate'] = CommonTool::formatDate($receiveData['year']."-".$receiveData['month']."-".$receiveData['day'],"Y-m-d");
    $selectonesql = "select id,email,update_time from user where id=".$_SESSION['usr_id'];
    $row = $db->getRow($selectonesql);
    if ($row) {
    		//排他
    		$dbUpd_dttm = $row['update_time'];
    		if($receiveData['update_time'] != null && $receiveData['update_time'] != $dbUpd_dttm){
    			throw new SysException('E0005');
    		}
    		$updatesql ='update user set name_prifix="'.$itemPostData['name_prifix'].'",name_suffix="'
    				.$itemPostData['name_suffix'].'",notation_prifix="'.$itemPostData['notation_prifix'].'",notation_sufix="'
    				.$itemPostData['notation_sufix'].'",postcode="'.$itemPostData['postcode'].'",address1="'
    				.$itemPostData['address1'].'",address2="'.$itemPostData['address2'].'",address3="'
    				.$itemPostData['address3'].'",tel="'.$itemPostData['tel'].'",sex="'
    				.$itemPostData['sex'].'",update_time="'.$curr_dttm.'",update_by="'.$_SESSION['usr_id'].'"';
			
			if($itemPostData['birthdate'] != null && $itemPostData['birthdate'] != ""){
				$updatesql = $updatesql .',birthdate="'.$itemPostData['birthdate'].'" where id="'.$_SESSION['usr_id'].'"';    			
    		}else{
    			$updatesql = $updatesql .',birthdate=null where id="'.$_SESSION['usr_id'].'"';
    		}
    		
    		$uptRes = $db->query($updatesql);
    		
    		if (!$uptRes) {
    			$insertSuccessTag = 0;
    		}
/*     		if(isset($_SESSION['isRecruit']) && $_SESSION['isRecruit'] == "point"){
    			$insertSuccessTag = sessionRecruit($db,$curr_dttm);
    		} */
    }else{
    	throw new SysException("I0010", '', '');
    }
    if ($insertSuccessTag == 1) {
    	$db->endTransaction(true);
    	
    	//送信
//    	$body = $itemPostData['name_prifix'].$itemPostData['name_suffix']."様<br />
//                                                  会員情報の変更を受け付けました。<br />
//                                                 心当たりがございませんでしたら、事務局までご連絡ください。<br />
//        ";
        $body = '<label>'.  $_SESSION['name_prifix'] . $_SESSION['name_suffix'] .'様</label><br><br>'
        . ' <label>ヘパリーゼキャンプグッズプレゼントキャンペーン事務局です。</label><br><br>'
        . ' <label>会員情報の変更を受け付けました。</label><br><br>'
        . ' <label>※このメールに心当たりがございませんでしたら、お手数をおかけしますが、下記事務局までお問い合わせください。</label><br>'
        . ' <label>※このメールの送信アドレスは送信専用となりますので、返信できません。</label><br>'
        . ' <br><br><label>-----------------------------------------------------------------------------------------------------</label><br>'
        . ' <label>お問い合わせ</label><br>'
        . ' <label>ヘパリーゼキャンプグッズプレゼントキャンペーン事務局</label><br>'
        . ' <label>受付時間：9:00～17:00　※2020年11月11日（水）のみ19:00まで</label><br>'
        . ' <label>受付期間：2020年12月18日(金)まで</label><br>'
        . ' <label>※上記の受付時間外は、inquiry@hepa-camphack-campaign.comでも</label><br>'
        . ' <label>　お問い合わせを承っております。</label><br>'
        . ' <label>-----------------------------------------------------------------------------------------------------</label>';        
    	$mailer->sendOne($_SESSION['mail'],$body,'会員情報の変更を受け付けました。');
    	
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
	if($params['name_prifix'] == null || $params['name_prifix'] == ""){
		throw new SysException('I0020', '', '');
	}
	if(!CommonTool::isFullAngle($params['name_prifix'])){
		throw new SysException('I0020', '', '');
	}
	/* !isZenKatakana(updateData.name_suffix) */
	if($params['name_suffix'] == null || $params['name_suffix'] == ""){
		throw new SysException('I0020', '', '');
	}
	if(!CommonTool::isFullAngle($params['name_suffix'])){
		throw new SysException('I0020', '', '');
	}
	/* !isZenKatakana(updateData.name_suffix) */
	if($params['notation_prifix'] == null || $params['notation_prifix'] == ""){
		throw new SysException('I0021', '', '');
	}
	if(!CommonTool::isFullAngle($params['notation_prifix'])){
		throw new SysException('I0021', '', '');
	}
	if(!CommonTool::isZenKatakana($params['notation_prifix'])){
		throw new SysException('I0021', '', '');
	}
	if($params['notation_sufix'] == null || $params['notation_sufix'] == ""){
		throw new SysException('I0021', '', '');
	}
	if(!CommonTool::isFullAngle($params['notation_sufix']) ){
		throw new SysException('I0021', '', '');
	}
	if(!CommonTool::isZenKatakana($params['notation_sufix'])){
		throw new SysException('I0021', '', '');
	}
	
	
	if(CommonTool::isNullOREmpty($params['year']) || CommonTool::isNullOREmpty($params['month']) || CommonTool::isNullOREmpty($params['day'])){
		throw new SysException('I0005', '', '');
	}
	if($params['year'] <= 1930 || $params['year'] >= 2021){
		throw new SysException('I0005', '', '');
	}
	if($params['month'] < 10){
		$params['month'] = "0".$params['month'];
	}
	if($params['day'] < 10){
		$params['day'] = "0".$params['day'];
	}
	$data = $params['year']."-".$params['month']."-".$params['day'];
	if(!CommonTool::checkDateIsValid($data)){
		throw new SysException('I0005', '', '');
	}
	if(CommonTool::isNullOREmpty($params['postcode'])){
		throw new SysException('I0006', '', '');
	}
	if(!CommonTool::isNumber($params['postcode']) || strlen($params['postcode'])!=7){
		throw new SysException('I0006', '', '');
	}
	if(CommonTool::isNullOREmpty($params['address1'])){
		throw new SysException('I0022', '', '');
	}
	if(CommonTool::isNullOREmpty($params['tel'])){
		throw new SysException('I0007', '', '');
	}
	if(!CommonTool::isNumber($params['tel']) || (strlen($params['tel']) != 11 && strlen($params['tel']) != 10)){
		throw new SysException('I0007', '', '');
	}
}

function sessionRecruit($db,$curr_dttm){
	$insertSuccessTag = 1;
	
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
		if($insRes > 0 ){
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
		if(isset($_SESSION['isRecruit'])){
			unset($_SESSION['isRecruit']);
		}
		if(isset($_SESSION['expireTime'])){
			unset($_SESSION['expireTime']);
			unset($_SESSION['verificationCode']);
			unset($_SESSION['campaign']);
		}
	}else{
		throw new SysException("S0002", '', '');
	}
	return $insertSuccessTag;
}