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

try {
    //リクエストの種類
    $operate = null;
    if (isset($_POST['operate'])) {
        $operate = $_POST['operate'];
    } else {
        //リクエスト種類が間違い
        throw new SysException("S0002");
    }
    if ($operate == 'search') {
        searchData($db);
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
			$intoData['birthdatetext'] = CommonTool::formatDate($row['birthdate'],"Y/m/d");
		}else{
			$intoData['birthdatetext'] = "";
		}
		if(isset($row['postcode'])){
			$intoData['postcode'] = substr_replace($row['postcode'],"-",3,0);
		}else{
			$intoData['postcode'] = "";
		}
	}
	
    $data ['status'] = 1;
    $data ['message'] = '';
    $data ['responseData'] = $intoData;
    echo json_encode($data);
    exit();
}