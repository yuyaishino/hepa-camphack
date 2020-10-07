<?php
/**
 * 引取実績集計
 *
 * PageID:   B3003
 * Date:     2019/10/16
 * Author:   hayakawa
 *
 **/

//メッセージの共通ツールをロードする
require_once("../common/message/MessageTool.php");
require_once("../common/Exception/SysException.php");
require_once("../common/comUtils/CommonTool.class.php");
require_once('../common/db/dbini.php');
try {
	// トランザクション開始
	$db->startTransaction();

	// 集計開始日を取得
	$fromDate = getFromDate($db);

	// 集計結果を取得
	$fromStr =  date('Y-m-d', $fromDate);
	$shippingList = getTransactionList($db, $fromStr);

	// 引取先一覧を取得
	$transactionPlaceList = getTransactionPlaceList($db);

	// システム日付を取得
	$nowDate = strtotime(date('Y-m-d'));
	
	// 集計開始日が未来になるまで繰り返す。
	while($fromDate < $nowDate) {
		// 集計終了日を算出
		$toDate = strtotime($fromStr . ' +6 day');
		$toStr = date('Y-m-d', $toDate);

		// 週の中央の日付を算出
		$midDate = strtotime($fromStr . ' +3 day');

		// 集計年、集計週を算出
		$syuke_year = date('Y', $midDate);
		$syuke_week = getWeekNumber($midDate);

		// 全拠点に対して処理を行う
		foreach($transactionPlaceList as $transactionPlace){
			$place_cd = $transactionPlace->place_cd;
			
			// 集計種別毎に処理を行う
			for($syuke_calssification_cd = 1;$syuke_calssification_cd <=3;$syuke_calssification_cd++){
				// 週次集計を算出
				$case_cnt = getCaseSum($shippingList, $place_cd, $syuke_calssification_cd, $fromStr, $toStr);
				$dbFromStr =  date('Ymd', $fromDate);
				$dbToStr = date('Ymd', $toDate);
				// 引取実績集計の登録
				insertTransactionAggregation($db, $place_cd, $syuke_calssification_cd, $syuke_year, $syuke_week, $dbFromStr, $dbToStr, $case_cnt);
			}
		}
		// 集計開始日を翌週日付に
		$fromDate = strtotime($fromStr . ' +7 day');
		$fromStr =  date('Y-m-d', $fromDate);
	}
	// トランザクション終了
    $db->endTransaction(true);
} catch (SysException $e) {
    $db->endTransaction(false);
   	//カストマイズException
	$errCode = $e->getErrCode();
	$messageStr = MessageTool::getMessage($errCode);
	$response['status'] = 0;
	$response['message'] = $messageStr . '(' . $e->getTraceAsString() . ')';
	echo json_encode ($response);
	exit;
} catch (Exception $e) {
    $db->endTransaction(false);
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
 * 集計開始日を取得
 *
 * @param object $db DBコンテンション
 * @return int 開始日時
 * @throws SysException
 */
function getFromDate($db) {
	$lastDate = null;
	$lastTransaction = null;

	// 最新集計日の翌日を取得
	$kikanList = getLastKikan($db);
	if (!empty($kikanList[0]->max)){
		$lastDate = strtotime($kikanList[0]->max . ' +1 day');
	}else{
		// 最古の引取日を取得
		$transactionList = getLastTransaction($db);
		if (!empty($transactionList[0]->min)){
			$lastDate = strtotime($transactionList[0]->min);
		}
	}
	// 直近編集引取日を取得
	$transactionList = getLastUpdateTransaction($db);
	if (!empty($transactionList[0]->min)){
		$lastTransaction = strtotime($transactionList[0]->min);
	}
	if ($lastDate == null){
		$lastDate = $lastTransaction;
  	}else if ($lastTransaction != null && $lastDate > $lastTransaction){
		$lastDate = $lastTransaction;
	}
	// 集計開始日の週の月曜日の日付を算出
	$dateOfWeek = date('w', $lastDate);
	switch($dateOfWeek){
		case 0:
			$fromDate = strtotime(date('Y-m-d', $lastDate) . ' -6 day');
			break;
		default:
			$tmpValue = $dateOfWeek - 1;
			$fromDate = strtotime(date('Y-m-d', $lastDate) . ' -' . $tmpValue . ' day');
	}
	return $fromDate;
}


/**
 * 最新集計日を取得
 *
 * @param object $db DBコンテンション
 * @return List 検索結果
 * @throws SysException
 */
function getLastKikan($db) {

	$queryCondition = 
		"SELECT max(kikan_to_date)
		FROM transaction_aggregation
		WHERE del_flg='0'";
	return $db->findListBySql($queryCondition);
}

/**
 * 直近一週間で更新した最古の引取日を取得
 * @param object $db DBコンテンション
 * @return List 検索結果
 * @throws SysException
 **/
function getLastUpdateTransaction($db){
	$queryCondition = 
		"select min(transaction_date)
		from transactions
		join transactions_driver on transactions_driver.transaction_cd = transactions.transaction_cd
		where transactions_driver.upd_dttm > now() - interval '2 week'";
	return $db->findListBySql($queryCondition);
}

/**
 * 最古の引取日を取得
 *
 * @param object $db DBコンテンション
 * @return List 検索結果
 * @throws SysException
 **/
function getLastTransaction($db){
	$queryCondition = 
		"select min(transaction_date)
		from transactions";
	return $db->findListBySql($queryCondition);
}

/**
 * 集計結果を取得
 *
 * @param object $db DBコンテンション
 * @param String $fromStr 検索開始日
 * @return List 検索結果
 * @throws SysException
 **/
function getTransactionList($db, $fromStr){
	$queryCondition = 
		"select transactions.transaction_saki_place_cd, calssification_cd, transaction_date, sum(transactions_driver.transaction_cnt)
		from transactions
		join transactions_driver on transactions_driver.transaction_cd = transactions.transaction_cd
		 and transactions_driver.del_flg = '0'
		join mdm_syhn on mdm_syhn.syhn_ktbn = transactions.syhn_ktbn
		where transaction_date >= '" . $fromStr . "' 
		group by transactions.transaction_saki_place_cd,calssification_cd, transaction_date
		order by transactions.transaction_saki_place_cd,calssification_cd, transaction_date";
	return $db->findListBySql($queryCondition);
}

/**
* date('W')は、以下の場合に週番号として'01'を返す。
* (1) 12月29日が月曜日の場合。
* (2) 12月30日が月曜日または火曜日の場合。
* (3) 12月31日が月曜日・火曜日・水曜日のいずれかの場合。
*
* この関数では、12/29〜31で、週番号が'01'となる場合には、'53'を返す。
*
* @param $time UNIXタイムのタイムスタンプ
* @return string
*/
function getWeekNumber($time)
{
    $week_number     = date('W', $time); // 週番号（01から52）
    $month_and_day   = date('m-d', $time); // 月-日
    $day_of_the_week = date('w', $time); // 0 (日曜)から 6 (土曜)

    // 処理方法(1): 仕様通りに月日と曜日で判定
    if ($month_and_day === '12-29' && in_array($day_of_the_week, array(1))) {
        $week_number = '53';
    }
    if ($month_and_day === '12-30' && in_array($day_of_the_week, array(1, 2))) {
        $week_number = '53';
    }
    if ($month_and_day === '12-31' && in_array($day_of_the_week, array(1, 2, 3))) {
        $week_number = '53';
    }
    
    //処理方法(2): 月日+date('W')の値で判定（こちらでも実用上は問題ないかと…）
    // if (in_array(date('m-d', $time), array('12-29', '12-30', '12-31')) && date('W', $time) === '01') {
    //    $week_number = '53';
    // }

    return $week_number;
}

/**
 * 引取先リストを取得
 *
 * @param object $db DBコンテンション
 * @return List 検索結果
 * @throws SysException
 **/
function getTransactionPlaceList($db){
	$queryCondition = 
		"select place_cd
		from mdm_place
		where transaction_flag = '1'
		and del_flg = '0'
		order by place_cd";
	return $db->findListBySql($queryCondition);
}

/**
 * 週次集計を算出
 *
 * @param List $transactionList 日次集計結果
 * @param string $transaction_saki_place_cd 引取先場所コード
 * @param int $syuke_calssification_cd 集計種別
 * @param string $fromStr 集計開始日
 * @param string $toStr 集計終了日
 * return int 週次集計
 **/
function getCaseSum($transactionList, $transaction_saki_place_cd, $syuke_calssification_cd, $fromStr, $toStr){
	$sum = 0.0;
	foreach($transactionList as $transactionData){
		if ($transaction_saki_place_cd == $transactionData->transaction_saki_place_cd){
			if ($fromStr <= $transactionData->transaction_date &&
				$toStr >= $transactionData->transaction_date){
				switch($syuke_calssification_cd){
					case 1:
						if ($transactionData->calssification_cd == '1'){
							$sum += $transactionData->sum;
						}
						break;
					case 2:
						if ($transactionData->calssification_cd == '2'){
							$sum += $transactionData->sum;
						}
						break;
					default:
						if ($transactionData->calssification_cd != '1' &&
							$transactionData->calssification_cd != '2'){
							$sum += $transactionData->sum;
						}
				}
			}
		}
	}
	return $sum;
}

/**
 * 引取実績集計登録
 * @param object $db DBコンテンション
 * @param string $transaction_saki_place_cd 引取先場所コード
 * @param int $syuke_calssification_cd 集計種別
 * @param string $syuke_year 集計年
 * @param string $syuke_week 集計週
 * @param string $kikan_from_date 期間From
 * @param string $kikan_to_date 期間To
 * @param int $case_cnt 出荷数
 **/
function insertTransactionAggregation($db, $transaction_saki_place_cd, $syuke_calssification_cd, $syuke_year, $syuke_week, $kikan_from_date, $kikan_to_date, $case_cnt){
	unset($where);
	$db->table('transaction_aggregation');
	$where['transaction_saki_place_cd'] = $transaction_saki_place_cd;
	$where['syuke_calssification_cd'] = $syuke_calssification_cd;
	$where['syuke_year'] = $syuke_year;
	$where['syuke_week'] = $syuke_week;
	$transactionAggregationData = $db->findOne($where);
	if(empty($transactionAggregationData)){
		$transactionAggregationWhere['transaction_saki_place_cd'] = $transaction_saki_place_cd;
		$transactionAggregationWhere['syuke_calssification_cd'] = $syuke_calssification_cd;
		$transactionAggregationWhere['syuke_year'] = $syuke_year;
		$transactionAggregationWhere['syuke_week'] = $syuke_week;
		$transactionAggregationWhere['kikan_from_date'] = $kikan_from_date;
		$transactionAggregationWhere['kikan_to_date'] = $kikan_to_date;
		$transactionAggregationWhere['case_cnt'] = $case_cnt;
		$transactionAggregationWhere['ins_dttm'] = CommonTool::getDateTimeWithMillisecond();
		$transactionAggregationWhere['ins_usr'] = 'SYST';
		$transactionAggregationWhere['ins_pgmid'] = 'G0000';
		$transactionAggregationWhere['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
		$transactionAggregationWhere['upd_usr'] = 'SYST';
		$transactionAggregationWhere['upd_pgmid'] = 'G0000';
		$db->add($transactionAggregationWhere, 'transaction_aggregation');
	}else{
		unset($updateParams);
		$updateParams['kikan_from_date'] = $kikan_from_date;
		$updateParams['kikan_to_date'] = $kikan_to_date;
		$updateParams['case_cnt'] = $case_cnt;
		$updateParams['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
		$updateParams['upd_usr'] = 'SYST';
		$updateParams['upd_pgmid'] = 'G0000';
		$rows =  $db->update($updateParams, $transactionAggregationData);
	}
}
