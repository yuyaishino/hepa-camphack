<?php
/**
 * 郵便番号で住所をとる
 * 
 *
 * PageID:   Postcode.php
 * Date:      2020/09/8
 * Author:   guyating
 *
 **/
require_once("../common/db/dbini.php");
require_once("../common/message/MessageTool.php");

require_once ("../../application/common/db/sessioninit.php");

try {
	$responseData = array();
	if (isset($_POST['data'])) {
		//パラメータで渡された得意先コードあり
		$postcode = $_POST['data'];
		$responseData = getData($db, $postcode);
	}
	$response = array();
	$response['status'] = 1;
	$response['message'] = '';
	$response['responseData'] = $responseData;
	echo json_encode ( $response);
	exit ();
	
} catch (Exception $e) {
	$response = array();
	$response['status'] = 0;
	//原因不明エラー
	$messageStr = MessageTool::getMessage("S0001");
	$response['message'] = $messageStr;
	echo json_encode ( $response);
	exit ();
}

function getData($db,$postcode){
	$queryCondition = "
			SELECT
				province,
				city,
				village,
                thirteen
			FROM postcode
            WHERE code = '".$postcode."'
			ORDER BY
				id ASC";
	$responseData = $db->getRow($queryCondition);
	
	return  $responseData;
}
