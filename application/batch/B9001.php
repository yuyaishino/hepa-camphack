<?php
/**
 * ワークフロー承認状態更新
 *
 * PageID:   B9001
 * Date:     2018/06/14
 * Author:   hayakawa
 *
 **/

// セッション共通処理
//require_once ("../common/comUtils/SessionCheck.php");

//メッセージの共通ツールをロードする
require_once("../common/message/MessageTool.php");
require_once("../common/Exception/SysException.php");
require_once("../common/comUtils/CommonTool.class.php");
require_once('../common/db/dbini.php');
require_once '../common/db/WorkLogTool.php';
define ("BASE_URL", "https://sateraito-apps-workflow.appspot.com/877.co.jp/api/public");
define ("API_KEY", "118558674633ffa4248987b508b96901");
define ("IMPERSONATE_EMAIL", "hynesen@877.co.jp");

try {
	// ワークフローのアクセストークン取得
    $result = getAccessToken();
    if ($result['code'] != 0){
        echo 'ワークフローシステムとの通信に失敗しました。(code=[' . $result['code'] . "] error_code=[" . $result['error_code'] . "] error_msg=[" . $result['error_code'] . "])";
		exit;
    }
    $access_token = $result['access_token'];
    
    //仕入の承認済更新
	purchaseProc($db, $access_token);

	//仕入価格の承認済更新
	purchasePriceProc($db, $access_token);
	
	//荷割の承認済更新
	subCargoProc($db, $access_token);
	
	//荷割価格の承認済更新
	subCargoPriceProc($db, $access_token);
	
	//受注の承認済更新
	ordersProc($db, $access_token);
	
} catch (SysException $e) {
	//カストマイズException
	$errCode = $e->getErrCode();
	$messageStr = MessageTool::getMessage($errCode);
	$response['status'] = 0;
	$response['message'] = $messageStr . '(' . $e->getTraceAsString() . ')';
	echo json_encode ($response);
	exit;
} catch (Exception $e) {
	$ExceptionMessage = $e->getMessage();
	if ($ExceptionMessage == "pg_error"){
		//PostgreSQLの問題
		$messageStr = MessageTool::getMessage("S0002");
		$messageStr .= '(' . $e->getTraceAsString() . ')';
	}else{
		//原因不明エラー
		$messageStr = MessageTool::getMessage("S0001");
		$messageStr .= '(' . $e->getTraceAsString() . ')';
	}
	$response['status'] = 0;
	$response['message'] = $messageStr;
	echo json_encode ($response);
	exit;
}

/**
 * ワークフローのアクセストークンを取得する
 *
 * @throws SysException
 * @return Object ワークフローシステムからのResponse
 */
function getAccessToken(){
    // URLパラメータを APIに渡すパラメータとして再構成
    $data = array();
    $data['api_key'] = API_KEY;
    $data['impersonate_email'] = IMPERSONATE_EMAIL;

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, BASE_URL .'/auth');
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); // post
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // jsonデータを送信
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);

    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    $result = json_decode($body, true);

    curl_close($curl);

    return $result;
}

/**
 * ワークフローから承認状態を取得する
 *
 * @param string $access_token アクセストークン
 * @param string $doc_id 承認状態を取得する対象のドキュメントID
 * @throws SysException
 * @return Object ワークフローシステムからのResponse
 */
function getDocStatus($access_token, $doc_id) {
    // URLパラメータを APIに渡すパラメータとして再構成
    $data = array();
    $data['access_token'] =$access_token;
    $data['impersonate_email'] = IMPERSONATE_EMAIL;
    $data['viewer_email'] = IMPERSONATE_EMAIL;
	$data['doc_id'] = $doc_id;
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, BASE_URL .'/docs/get');
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); // post
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // jsonデータを送信
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);
    $response = curl_exec($curl);

    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    $result = json_decode($body, true);
    return $result;
}

/**
 * 仕入の承認状態を更新する
 *
 * @param object $db DBコンテンション
 * @param string $access_token アクセストークン
 * @throws SysException
 */
function purchaseProc($db, $access_token) {
	//未承認のワークフロー連携番号１を求める
	$db->startTransaction();
	$queryCondition = 
		"SELECT workflow_cd1
		FROM purchase
		WHERE del_flg='0' AND admit_kbn='0' AND workflow_cd1 <> '' 
		GROUP BY workflow_cd1
		ORDER BY workflow_cd1 ASC ";
	$purchaseList = $db->findListBySql($queryCondition);
	$db->endTransaction(true);
	
	foreach($purchaseList as $purchaseData){

		// WFシステムに承認状態を問い合わせる
		$result = getDocStatus($access_token, $purchaseData->workflow_cd1);
		// 承認状態取得成功
		if ($result['code'] == 0){

			// 承認済時
			if ($result['data']['status'] == 'final_approved'){
				$db->startTransaction();
				$db->table('purchase');
				unset($where);
				$where['admit_kbn'] = '0';
				$where['workflow_cd1'] = $purchaseData->workflow_cd1;
				$where['del_flg'] = '0';
				$purchaseUpdateList = $db->findAll($where);
				// 承認済の全レコードについて更新する
				foreach($purchaseUpdateList as $purchaseUpdateData){
					$db->table('purchase');
					$updateParams = array();
					// 件数のみ承認済
					$updateParams['admit_kbn'] = '1';
					$updateParams['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
					$updateParams['upd_usr'] = 'SYST';
					$updateParams['upd_cnt'] = $purchaseUpdateData->upd_cnt + 1;
					$rows =  $db->update($updateParams, $purchaseUpdateData);
					$insertLogRow =  getUpdateLogRow("purchase", $purchaseUpdateData->purchase_cd, $updateParams, $purchaseUpdateData);
					$db->table("work_log");
					$insertLogRow['page_cd'] = "B9001";
					$insertLogRow['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
					$insertRes = $db->add($insertLogRow);
					// 更新エラー
					if ($rows != 1) {
						echo "更新エラー";
					}
				}
				$db->endTransaction(true);
			}
		// 取得失敗
		}else{
			echo "通信エラー";
		}
	}
}

/**
 * 仕入価格の承認状態を更新する
 *
 * @param object $db DBコンテンション
 * @param string $access_token アクセストークン
 * @throws SysException
 */
function purchasePriceProc($db, $access_token) {
	//未承認のワークフロー連携番号２を求める
	$db->startTransaction();
	$queryCondition = 
		"SELECT workflow_cd2
		FROM purchase_price
		WHERE del_flg='0' AND admit_kbn='0' AND workflow_cd2 <> '' 
		GROUP BY workflow_cd2
		ORDER BY workflow_cd2 ASC ";
	$purchasePriceList = $db->findListBySql($queryCondition);
	$db->endTransaction(true);

	foreach($purchasePriceList as $purchasePriceData){
	
		// WFシステムに承認状態を問い合わせる
		$result = getDocStatus($access_token, $purchasePriceData->workflow_cd2);
		// 承認状態取得成功
		if ($result['code'] == 0){

			// 承認済時
			if ($result['data']['status'] == 'final_approved'){
				$db->startTransaction();
				$db->table('purchase_price');
				unset($where);
				$where['admit_kbn'] = '0';
				$where['workflow_cd2'] = $purchasePriceData->workflow_cd2;
				$where['del_flg'] = '0';
				$purchasePriceUpdateList = $db->findAll($where);
				$purchaseArray = array();
				// 承認済の全レコードについて更新する
				foreach($purchasePriceUpdateList as $purchasePriceUpdateData){
					$db->table('purchase_price');
					unset($updateParams);
					// 承認済み
					$updateParams['admit_kbn'] = '9';
					$updateParams['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
					$updateParams['upd_usr'] = 'SYST';
					$updateParams['upd_cnt'] = $purchasePriceUpdateData->upd_cnt + 1;
					$rows =  $db->update($updateParams, $purchasePriceUpdateData);
					$insertLogRow =  getUpdateLogRow("purchase_price", 
						$purchasePriceUpdateData->purchase_cd . '-' . $purchasePriceUpdateData->purchase_price_cnt, $updateParams, $purchasePriceUpdateData);
					$db->table("work_log");
					$insertLogRow['page_cd'] = "B9001";
					$insertLogRow['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
					$insertRes = $db->add($insertLogRow);
					// 更新エラー
					if ($rows != 1) {
						echo "更新エラー";
					}
					if (!in_array($purchasePriceUpdateData->purchase_cd, $purchaseArray)) {
						array_push($purchaseArray, $purchasePriceUpdateData->purchase_cd);
					}
				}
				foreach($purchaseArray as $purchase_cd){
					$db->table('purchase');
					unset($where);
					$where['admit_kbn'] = '0';
					$where['purchase_cd'] = $purchase_cd;
					$where['del_flg'] = '0';
					$purchaseUpdateList = $db->findAll($where);
					// 承認済の全レコードについて更新する
					foreach($purchaseUpdateList as $purchaseUpdateData){
						$db->table('purchase');						
						unset($updateParams);
						// 件数のみ承認済
						$updateParams['admit_kbn'] = '1';
						$updateParams['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
						$updateParams['upd_usr'] = 'SYST';
						$updateParams['upd_cnt'] = $purchaseUpdateData->upd_cnt + 1;
						$rows =  $db->update($updateParams, $purchaseUpdateData);
						$insertLogRow =  getUpdateLogRow("purchase", $purchaseUpdateData->purchase_cd, $updateParams, $purchaseUpdateData);
						$db->table("work_log");
						$insertLogRow['page_cd'] = "B9001";
						$insertLogRow['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
						$insertRes = $db->add($insertLogRow);
						// 更新エラー
						if ($rows != 1) {
							echo "更新エラー";
						}
					}
				}
				$db->endTransaction(true);
			}
		// 取得失敗
		}else{
			echo "通信エラー";
		}
	}
}

/**
 * 荷割の承認状態を更新する
 *
 * @param object $db DBコンテンション
 * @param string $access_token アクセストークン
 * @throws SysException
 */
function subCargoProc($db, $access_token) {
	//未承認のワークフロー連携番号１を求める
	$db->startTransaction();
	$queryCondition = 
		"SELECT workflow_cd1
		FROM sub_cargo
		WHERE del_flg='0' AND admit_kbn='0' AND workflow_cd1 <> '' 
		GROUP BY workflow_cd1
		ORDER BY workflow_cd1 ASC ";
	$subCargoList = $db->findListBySql($queryCondition);
	$db->endTransaction(true);

	foreach($subCargoList as $subCargoData){
	
		// WFシステムに承認状態を問い合わせる
		$result = getDocStatus($access_token, $subCargoData->workflow_cd1);
		// 承認状態取得成功
		if ($result['code'] == 0){

			// 承認済時
			if ($result['data']['status'] == 'final_approved'){
				$db->startTransaction();
				$db->table('sub_cargo');
				unset($where);
				$where['admit_kbn'] = '0';
				$where['workflow_cd1'] = $subCargoData->workflow_cd1;
				$where['del_flg'] = '0';
				$subCargoUpdateList = $db->findAll($where);
				// 承認済の全レコードについて更新する
				foreach($subCargoUpdateList as $subCargoUpdateData){
					$db->table('sub_cargo');
					unset($updateParams);
					// 件数のみ承認済
					$updateParams['admit_kbn'] = '1';
					$updateParams['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
					$updateParams['upd_usr'] = 'SYST';
					$updateParams['upd_cnt'] = $subCargoUpdateData->upd_cnt + 1;
					$rows =  $db->update($updateParams, $subCargoUpdateData);
					$insertLogRow =  getUpdateLogRow("sub_cargo", $subCargoUpdateData->sub_cargo_cd, $updateParams, $subCargoUpdateData);
					$db->table("work_log");
					$insertLogRow['page_cd'] = "B9001";
					$insertLogRow['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
					$insertRes = $db->add($insertLogRow);
					// 更新エラー
					if ($rows != 1) {
						echo "更新エラー";
					}
				}
				$db->endTransaction(true);
			}
		// 取得失敗
		}else{
			echo "通信エラー";
		}
	}
}


/**
 * 荷割価格の承認状態を更新する
 *
 * @param object $db DBコンテンション
 * @param string $access_token アクセストークン
 * @throws SysException
 */
function subCargoPriceProc($db, $access_token) {
	//未承認のワークフロー連携番号２を求める
	$db->startTransaction();
	$queryCondition = 
		"SELECT workflow_cd2
		FROM sub_cargo_price
		WHERE del_flg='0' AND admit_kbn='0' AND workflow_cd2 <> '' 
		GROUP BY workflow_cd2
		ORDER BY workflow_cd2 ASC ";
	$subCargoPriceList = $db->findListBySql($queryCondition);
	$db->endTransaction(true);
	foreach($subCargoPriceList as $subCargoPriceData){

		// WFシステムに承認状態を問い合わせる
		$result = getDocStatus($access_token, $subCargoPriceData->workflow_cd2);
		// 承認状態取得成功
		if ($result['code'] == 0){

			// 承認済時
			if ($result['data']['status'] == 'final_approved'){
				$db->startTransaction();
				$db->table('sub_cargo_price');
				unset($where);
				$where['admit_kbn'] = '0';
				$where['workflow_cd2'] = $subCargoPriceData->workflow_cd2;
				$where['del_flg'] = '0';
				$subCargoPriceUpdateList = $db->findAll($where);
				$subCargoArray = array();
				// 承認済の全レコードについて更新する
				foreach($subCargoPriceUpdateList as $subCargoPriceUpdateData){
					$db->table('sub_cargo_price');
					unset($updateParams);
					// 承認済み
					$updateParams['admit_kbn'] = '9';
					$updateParams['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
					$updateParams['upd_usr'] = 'SYST';
					$updateParams['upd_cnt'] = $subCargoPriceUpdateData->upd_cnt + 1;
					$db->table('sub_cargo_price');
					$rows =  $db->update($updateParams, $subCargoPriceUpdateData);
					$insertLogRow =  getUpdateLogRow("sub_cargo_price", 
						$subCargoPriceUpdateData->sub_cargo_cd . '-' . $subCargoPriceUpdateData->sub_cargo_price_cnt, $updateParams, $subCargoPriceUpdateData);
					$db->table("work_log");
					$insertLogRow['page_cd'] = "B9001";
					$insertLogRow['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
					$insertRes = $db->add($insertLogRow);
					// 更新エラー
					if ($rows != 1) {
						echo "更新エラー";
					}
					if (!in_array($subCargoPriceUpdateData->sub_cargo_cd, $subCargoArray)) {
						array_push($subCargoArray, $subCargoPriceUpdateData->sub_cargo_cd);
					}
					
					// 荷割価格と対応する荷割情報を求める
					$sub_cargo_data = getSubCargoBySubCargoCd($db, $subCargoPriceUpdateData->sub_cargo_cd);
					if ($sub_cargo_data != null){
						if ($sub_cargo_data->sub_cargo_kbn != '2' && $sub_cargo_data->purchase_cd != null){
							// 仕入の出荷数を更新する
							updatePurchaseBySubCargoCnt($db, $sub_cargo_data->purchase_cd, $subCargoPriceUpdateData->sub_cargo_cnt);
						}
					}
				}
				foreach($subCargoArray as $sub_cargo_cd){
					$db->table('sub_cargo');
					unset($where);
					$where['admit_kbn'] = '0';
					$where['sub_cargo_cd'] = $sub_cargo_cd;
					$where['del_flg'] = '0';
					$subCargoUpdateList = $db->findAll($where);
					// 承認済の全レコードについて更新する
					foreach($subCargoUpdateList as $subCargoUpdateData){
						$db->table('sub_cargo');
						unset($updateParams);
						// 件数のみ承認済
						$updateParams['admit_kbn'] = '1';
						$updateParams['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
						$updateParams['upd_usr'] = 'SYST';
						$updateParams['upd_cnt'] = $subCargoUpdateData->upd_cnt + 1;
						$rows =  $db->update($updateParams, $subCargoUpdateData);
						$insertLogRow =  getUpdateLogRow("sub_cargo", $subCargoUpdateData->sub_cargo_cd, $updateParams, $subCargoUpdateData);
						$db->table("work_log");
						$insertLogRow['page_cd'] = "B9001";
						$insertLogRow['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
						$insertRes = $db->add($insertLogRow);
						// 更新エラー
						if ($rows != 1) {
							echo "更新エラー";
						}
					}
				}
				$db->endTransaction(true);
			}
		// 取得失敗
		}else{
			echo "通信エラー";
		}
	}
}


/**
 * 受注の承認状態を更新する
 *
 * @param object $db DBコンテンション
 * @param string $access_token アクセストークン
 * @throws SysException
 */
function ordersProc($db, $access_token) {
	//未承認のワークフロー連携番号２を求める
	$db->startTransaction();
	$queryCondition = 
		"SELECT workflow_cd2
		FROM orders
		WHERE del_flg='0' AND admit_kbn='0' AND workflow_cd2 <> '' 
		GROUP BY workflow_cd2
		ORDER BY workflow_cd2 ASC ";
	$ordersList = $db->findListBySql($queryCondition);
	$db->endTransaction(true);

	foreach($ordersList as $ordersData){
	
		// WFシステムに承認状態を問い合わせる
		$result = getDocStatus($access_token, $ordersData->workflow_cd2);
		// 承認状態取得成功
		if ($result['code'] == 0){

			// 承認済時
			if ($result['data']['status'] == 'final_approved'){
				$db->startTransaction();
				$db->table('orders');
				unset($where);
				$where['admit_kbn'] = '0';
				$where['workflow_cd2'] = $ordersData->workflow_cd2;
				$where['del_flg'] = '0';
				$ordersUpdateList = $db->findAll($where);
				// 承認済の全レコードについて更新する
				foreach($ordersUpdateList as $ordersUpdateData){
					$db->table('orders');
					unset($updateParams);
					// 承認済み
					$updateParams['admit_kbn'] = '9';
					$updateParams['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
					$updateParams['upd_usr'] = 'SYST';
					$updateParams['upd_cnt'] = $ordersUpdateData->upd_cnt + 1;
					$rows =  $db->update($updateParams, $ordersUpdateData);
					$insertLogRow =  getUpdateLogRow("orders", $ordersUpdateData->orders_cd, $updateParams, $ordersUpdateData);
					$db->table("work_log");
					$insertLogRow['page_cd'] = "B9001";
					$insertLogRow['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
					$insertRes = $db->add($insertLogRow);
					// 更新エラー
					if ($rows != 1) {
						echo "更新エラー";
					}
					
				}
				$db->endTransaction(true);
			}
		// 取得失敗
		}else{
			echo "通信エラー";
		}
	}
}

/**
 * 荷割コードをキーに荷割情報を求める
 *
 * @param object $db DBコンテンション
 * @param string $sub_cargo_cd 荷割コード
 * @return array 荷割情報
 * @throws SysException
 */
function getSubCargoBySubCargoCd($db, $sub_cargo_cd){
	$db->table('sub_cargo');
	unset($where);
	$where['sub_cargo_cd'] = $sub_cargo_cd;
	$where['del_flg'] = '0';
	$SubCargoRow = $db->findAll($where);
	if (count($SubCargoRow) == 1){
		return $SubCargoRow[0];
	}
	return null;
}

/**
 * 仕入テーブルの出荷数を更新する。
 *
 * @param object $db DBコンテンション
 * @param string $purchase_cd 仕入コード
 * @param int    $sub_cargo_cnt 荷割数
 * @throws SysException
 */
function updatePurchaseBySubCargoCnt($db, $purchase_cd, $sub_cargo_cnt){
	$db->table('purchase');
	unset($where);
	$where['purchase_cd'] = $purchase_cd;
	$where['del_flg'] = '0';
	$purchaseUpdateList = $db->findAll($where);
	foreach($purchaseUpdateList as $purchaseUpdateData){
		$db->table('purchase');
		unset($updateParams);
		// 出荷数に荷割数を加える
		$updateParams['delivery_cnt'] = $purchaseUpdateData->delivery_cnt + $sub_cargo_cnt;
		
		// 全仕入が出荷、原料、ロスで使われた場合は、全出荷日にシステム日付をセットする。
		if ($purchaseUpdateData->purchase_cnt == ($purchaseUpdateData->delivery_cnt + $purchaseUpdateData->product_cnt + $purchaseUpdateData->loss_cnt + $sub_cargo_cnt)){
			$updateParams['all_delivery_date'] = date('Y-m-d');
		}
		$updateParams['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
		$updateParams['upd_usr'] = 'SYST';
		$updateParams['upd_cnt'] = $purchaseUpdateData->upd_cnt + 1;
		$rows =  $db->update($updateParams, $purchaseUpdateData);
		$insertLogRow =  getUpdateLogRow("purchase", $purchaseUpdateData->purchase_cd, $updateParams, $purchaseUpdateData);
		$db->table("work_log");
		$insertLogRow['page_cd'] = "B9001";
		$insertLogRow['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
		$insertRes = $db->add($insertLogRow);
		// 更新エラー
		if ($rows != 1) {
			echo "更新エラー";
		}
	}
}
/**
 * データ更新の操作ログを作成する
 * @param string $tableName
 * @param string $uniqueVal
 * @param Array　 $updateRow
 * @param Array　 $dbDataRow
 * @return 配列
 */
function getUpdateLogRow($tableName,$uniqueVal,$updateRow,$dbDataRow){
	$work_mae_data = "";
	$work_ato_data = "";
	$workLogRow = null;
	foreach($updateRow as $index => $value){
		if(is_array($dbDataRow)){
			if(isset($dbDataRow[$index])){
				$work_mae_data .= $index.":".$dbDataRow[$index].";";
			}else{
				$work_mae_data .= $index.":NULL;";
			}
		}else{
			if(isset($dbDataRow->$index)){
				$work_mae_data .= $index.":".$dbDataRow->$index.";";
			}else{
				$work_mae_data .= $index.":NULL;";
			}
		}
		$work_ato_data .= $index.":".$value.";";
	}
	//0検索　1新規　2更新　3削除
	$workLogRow['work_kbn'] = 2;
	//操作対象テーブル
	$workLogRow['table_nm'] = $tableName;
	//ユニーク値
	$workLogRow['unique_value'] = $uniqueVal;
	//操作前データ	work_mae_data
	$workLogRow['work_mae_data'] = $work_mae_data;
	//操作後データ	work_ato_data
	$workLogRow['work_ato_data'] = $work_ato_data;
	//更新ユーザー
	$workLogRow['upd_usr'] = 'SYST';
	return $workLogRow;
}
