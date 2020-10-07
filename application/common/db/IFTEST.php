<?php
/**
 * ワークフロー承認状態更新
 *
 * PageID:   G9000
 * Date:     2018/06/14
 * Author:   hayakawa
 *
 **/

// セッション共通処理
//require_once ("../common/comUtils/SessionCheck.php");

//メッセージの共通ツールをロードする
require_once("../message/MessageTool.php");
require_once("../Exception/SysException.php");
require_once("../comUtils/CommonTool.class.php");
require_once('../db/dbini.php');
require_once '../db/TICSTEST.php';
require_once '../db/WorkLogTool.php';

try {
    $db->table('shipping_report');
    unset($wheres);
//    $wheres['purchase_cd'] =181009000003;
//    $wheres['into_room_report_cd'] = 190328000001;
//    $wheres['purchase_price_cnt'] = 1;
    $wheres['shipping_cd'] = 190911000308;
    $wheres['driver_cd'] = 1061;
    $wheres['into_room_report_cd'] = 190902000334;
//    $wheres['inventory_adjustment_cd'] = 190320000002;
//    $wheres['transaction_cd'] = 190529000118;
//    $wheres['commission_shipping_cd'] = 190412000002;
//    $wheres['orders_cd'] = 190412000001;


    $beforeData = $db->findOne($wheres);
//	$beforeData->sagyo_material_cd = 10;
    $afterData = $db->findOne($wheres);
	$beforeData->case_cnt = 8;
//	$afterData->pack_cnt = 0;
//    TICSIFTool::insertIFRow($db,'shipping_report', $beforeData,'SYSTEM');
    TICSIFTool::updateIFRow($db,'shipping_report', $beforeData, $afterData,'SYSTEM');
    $db->table('shipping_report');
    unset($wheres);
    $wheres['shipping_cd'] = 190911000313;
    $wheres['driver_cd'] = 1061;
    $wheres['into_room_report_cd'] = 190902000342;
    $beforeData = $db->findOne($wheres);
    $afterData = $db->findOne($wheres);
	$beforeData->case_cnt = 10;
    TICSIFTool::updateIFRow($db,'shipping_report', $beforeData, $afterData,'SYSTEM');
    
} catch (SysException $e) {
	//カストマイズException
	$errCode = $e->getErrCode();
	$messageStr = MessageTool::getMessage($errCode);
	$response['status'] = 0;
	$response['message'] = $messageStr;
	echo json_encode ($response);
	var_dump($e);
	exit;
} catch (Exception $e) {
	var_dump($e);
	$ExceptionMessage = $e->getMessage();
	if ($ExceptionMessage == "pg_error"){
		//PostgreSQLの問題
		$messageStr = MessageTool::getMessage("S0002");
	}else{
		//原因不明エラー
		$messageStr = MessageTool::getMessage("S0001");
	}
	$response['status'] = 0;
	$response['message'] = $messageStr;
	echo json_encode ($response);
	exit;
}
