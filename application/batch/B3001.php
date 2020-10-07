<?php
/**
 * 出荷実績集計
 *
 * PageID:   B3001
 * Date:     2018/11/28
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
	$shippingList = getShippingList($db, $fromStr);

	// 拠点一覧を取得
	$baseList = getBaseCdList($db);

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
		foreach($baseList as $baseData){
			$base_cd = $baseData->base_cd;
			
			// 集計種別毎に処理を行う
			for($syuke_calssification_cd = 1;$syuke_calssification_cd <=3;$syuke_calssification_cd++){
				// 週次集計を算出
				$case_cnt = getCaseSum($shippingList, $base_cd, $syuke_calssification_cd, $fromStr, $toStr);
				$dbFromStr =  date('Ymd', $fromDate);
				$dbToStr = date('Ymd', $toDate);
				// 出荷実績集計の登録
				insertShippingAggregation($db, $base_cd, $syuke_calssification_cd, $syuke_year, $syuke_week, $dbFromStr, $dbToStr, $case_cnt);
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
	$lastShipping = null;

	// 最新集計日の翌日を取得
	$kikanList = getLastKikan($db);
	if (!empty($kikanList[0]->max)){
		$lastDate = strtotime($kikanList[0]->max . ' +1 day');
	}else{
		// 最古の出荷日を取得
		$shippingList = getLastShipping($db);
		if (!empty($shippingList[0]->min)){
			$lastDate = strtotime($shippingList[0]->min);
		}
	}
	// 直近編集出荷日を取得
	$shippingList = getLastUpdateShipping($db);
	if (!empty($shippingList[0]->min)){
		$lastShipping = strtotime($shippingList[0]->min);
	}
	if ($lastDate == null){
		$lastDate = $lastShipping;
  	}else if ($lastShipping != null && $lastDate > $lastShipping){
		$lastDate = $lastShipping;
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
		FROM shipping_aggregation
		WHERE del_flg='0'";
	return $db->findListBySql($queryCondition);
}

/**
 * 直近一週間で更新した最古の出荷日を取得
 * @param object $db DBコンテンション
 * @return List 検索結果
 * @throws SysException
 **/
function getLastUpdateShipping($db){
	$queryCondition = 
		"select min(shipping_date)
		from shipping
		join shipping_report on shipping_report.shipping_cd = shipping.shipping_cd
		where shipping_report.upd_dttm > now() - interval '2 week'";
	return $db->findListBySql($queryCondition);
}

/**
 * 最古の出荷日を取得
 *
 * @param object $db DBコンテンション
 * @return List 検索結果
 * @throws SysException
 **/
function getLastShipping($db){
	$queryCondition = 
		"select min(shipping_date)
		from shipping";
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
function getShippingList($db, $fromStr){
	$queryCondition = 
		"select shipping.base_cd, calssification_cd, shipping_date,sum(case_cnt + pack_cnt/quantity_per_carton)
		from shipping
		join shipping_report on shipping_report.shipping_cd = shipping.shipping_cd
		 and shipping_report.del_flg = '0'
		join mdm_syhn on mdm_syhn.syhn_ktbn = shipping.syhn_ktbn
		where shipping_date >= '" . $fromStr . "' 
		group by shipping.base_cd,calssification_cd, shipping_date
		order by shipping.base_cd,calssification_cd, shipping_date";
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
 * 拠点コードリストを取得
 *
 * @param object $db DBコンテンション
 * @return List 検索結果
 * @throws SysException
 **/
function getBaseCdList($db){
	$queryCondition = 
		"select base_cd
		from mdm_base
		where del_flg = '0'
		order by base_cd";
	return $db->findListBySql($queryCondition);
}

/**
 * 週次集計を算出
 *
 * @param List $shippingList 日次集計結果
 * @param string $base_cd 拠点コード
 * @param int $syuke_calssification_cd 集計種別
 * @param string $fromStr 集計開始日
 * @param string $toStr 集計終了日
 * return int 週次集計
 **/
function getCaseSum($shippingList, $base_cd, $syuke_calssification_cd, $fromStr, $toStr){
	$sum = 0.0;
	foreach($shippingList as $shippingData){
		if ($base_cd == $shippingData->base_cd){
			if ($fromStr <= $shippingData->shipping_date &&
				$toStr >= $shippingData->shipping_date){
				switch($syuke_calssification_cd){
					case 1:
						if ($shippingData->calssification_cd == '1'){
							$sum += $shippingData->sum;
						}
						break;
					case 2:
						if ($shippingData->calssification_cd == '2'){
							$sum += $shippingData->sum;
						}
						break;
					default:
						if ($shippingData->calssification_cd != '1' &&
							$shippingData->calssification_cd != '2'){
							$sum += $shippingData->sum;
						}
				}
			}
		}
	}
	return $sum;
}

/**
 * 出荷実績集計登録
 * @param object $db DBコンテンション
 * @param string $base_cd 拠点コード
 * @param int $syuke_calssification_cd 集計種別
 * @param string $syuke_year 集計年
 * @param string $syuke_week 集計週
 * @param string $kikan_from_date 期間From
 * @param string $kikan_to_date 期間To
 * @param int $case_cnt 出荷数
 **/
function insertShippingAggregation($db, $base_cd, $syuke_calssification_cd, $syuke_year, $syuke_week, $kikan_from_date, $kikan_to_date, $case_cnt){
	unset($where);
	$db->table('shipping_aggregation');
	$where['base_cd'] = $base_cd;
	$where['syuke_calssification_cd'] = $syuke_calssification_cd;
	$where['syuke_year'] = $syuke_year;
	$where['syuke_week'] = $syuke_week;
	$shippinAggregationData = $db->findOne($where);
	if(empty($shippinAggregationData)){
		$shippinAggregationWhere['base_cd'] = $base_cd;
		$shippinAggregationWhere['syuke_calssification_cd'] = $syuke_calssification_cd;
		$shippinAggregationWhere['syuke_year'] = $syuke_year;
		$shippinAggregationWhere['syuke_week'] = $syuke_week;
		$shippinAggregationWhere['kikan_from_date'] = $kikan_from_date;
		$shippinAggregationWhere['kikan_to_date'] = $kikan_to_date;
		$shippinAggregationWhere['case_cnt'] = $case_cnt;
		$shippinAggregationWhere['ins_dttm'] = CommonTool::getDateTimeWithMillisecond();
		$shippinAggregationWhere['ins_usr'] = 'SYST';
		$shippinAggregationWhere['ins_pgmid'] = 'B3001';
		$shippinAggregationWhere['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
		$shippinAggregationWhere['upd_usr'] = 'SYST';
		$shippinAggregationWhere['upd_pgmid'] = 'B3001';
		$db->add($shippinAggregationWhere, 'shipping_aggregation');
	}else{
		unset($updateParams);
		$updateParams['kikan_from_date'] = $kikan_from_date;
		$updateParams['kikan_to_date'] = $kikan_to_date;
		$updateParams['case_cnt'] = $case_cnt;
		$updateParams['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
		$updateParams['upd_usr'] = 'SYST';
		$updateParams['upd_pgmid'] = 'B3001';
		$rows =  $db->update($updateParams, $shippinAggregationData);
	}
}
