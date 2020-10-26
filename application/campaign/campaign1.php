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
    $receiveList = json_decode($_POST['data'], true);
    if ($operate == 'search') {
        searchData($db);
    }  else if ($operate == 'add') {
    	addAndupdateDb($db, $receiveList, $mailer);
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
	$selectonesql = "select IFNULL(point ,0) AS point from user where id=".$_SESSION['usr_id'];
	$row = $db->getRow($selectonesql);
	if ($row) {
		$data ['status'] = 1;
		$data ['message'] = '';
		$data ['responseData'] = $row;
		echo json_encode($data);
		exit();
	}
}

function addAndupdateDb($db, $receiveList, $mailer){
    $responseData = array(); // return data
    $insertSuccessTag = 1; // success 1, failure 0
    
    //Main
    $uptData = checkAddPostMainData($db,$receiveList);
    $_SESSION['campaign'] = $receiveList;
    $verificationCode = CommonTool::randomCode();
    
    $_SESSION['verificationCode'] = $verificationCode;
    $_SESSION['expireTime'] = CommonTool::getExpireTime("+10 minute");
    if ($insertSuccessTag == 1) {			
    	//送信
//    	$body = $uptData['name_prifix'].$uptData['name_suffix']."様<br />
//                                                  応募用の認証コードです。<br />
//                ".$verificationCode."
//                <br />10分以内に上記認証コードを入力ください。<br />
//        ";
        $body = '<label>'.  $uptData['name_prifix'] . $uptData['name_suffix'] .'様</label><br><br>'
        . ' <label>ヘパリーゼキャンプグッズプレゼントキャンペーン事務局です。</label><br><br>'
        . ' <label>応募用の認証コードです。</label><br>'
        . ' <label>'.$verificationCode.'</label><br><br>'
        . ' <label>※10分以内に上記認証コードを入力していただかないと、認証コードが無効となります。</label><br>'    
        . ' <label>※このメールに心当たりがございませんでしたら、お手数をおかけしますが、削除していただきますようお願いいたします。</label><br>'
        . ' <label>※このメールの送信アドレスは送信専用となりますので、返信できません。</label><br>'
        . ' <br><br><label>-----------------------------------------------------------------------------------------------------</label><br>'
        . ' <label>お問い合わせ</label><br>'
        . ' <label>ヘパリーゼキャンプグッズプレゼントキャンペーン事務局</label><br>'
        . ' <label>TEL：0120-785-173 </label><br>'
        . ' <label>受付時間：10:00～17:00　※2020年11月11日（水）のみ19:00まで</label><br>'
        . ' <label>受付期間：2020年12月18日(金)まで</label><br>'
        . ' <label>※上記の受付時間外は、inquiry@hepa-camphack-campaign.comでも</label><br>'
        . ' <label>　お問い合わせを承っております。</label><br>'
        . ' <label>-----------------------------------------------------------------------------------------------------</label>';
        
//            $body = "".$uptData['name_prifix']."".$uptData['name_suffix']."様
//
//ヘパリーゼキャンプグッズプレゼントキャンペーン事務局です。
//応募用の認証コードです。
//
//$verificationCode
//
//※10分以内に上記認証コードを入力していただかないと、認証コードが無効となります。
//※このメールに心当たりがございませんでしたら、お手数をおかけしますが、削除していただきますようお願いいたします。
//※このメールの送信アドレスは送信専用となりますので、返信できません。
//
//
//-----------------------------------------------------------------------------------------------------
//お問い合わせ
//ヘパリーゼキャンプグッズプレゼントキャンペーン事務局
//TEL：0120-785-173
//受付時間：10:00～17:00　※2020年11月11日（水）のみ19:00まで
//受付期間：2020年12月18日(金)まで
//※上記の受付時間外は、inquiry@hepa-camphack-campaign.comでも
//お問い合わせを承っております。
//----------------------------------------------------------------------------------------------------- ";
    	$mailer->sendOne($_SESSION['mail'],$body,'応募用の認証コードです。','campaign1.php');
    	
    	
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

function checkAddPostMainData($db,$params){
	$needPoint = 0;
	foreach ($params AS $edata){
		switch ($edata['type']) {
			case "A":
				$needPoint = $needPoint + 20*$edata['apply_count'];
				break;
			case "B":
				$needPoint = $needPoint + 15*$edata['apply_count'];
				break;
			case "C":
				$needPoint = $needPoint + 10*$edata['apply_count'];
				break;
			case "D":
				$needPoint = $needPoint + 2*$edata['apply_count'];
				break;
			case "E":
				$needPoint = $needPoint + 1*$edata['apply_count'];
				break;
			default:
				break;
		}	
	}
	if($needPoint == 0){
		throw new SysException("I0012", '', '');
	}
	$selectonesql = "select email, name_prifix, name_suffix, IFNULL(point ,0) AS point from user where id=".$_SESSION['usr_id'];
	$row = $db->getRow($selectonesql);
	if ($row) {
		if($row['point'] < $needPoint){
			throw new SysException("I0011", '', '');
		}
		return $row;
	}else{
		throw new SysException("S0008", '', array('ID（メールアドレス）'));
	}
	
}
