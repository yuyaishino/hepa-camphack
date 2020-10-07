<?php

/**
 * 操作ログの共通ツール
 *
 * PageID:   WorkLogTool
 * Date:     2018/03/21
 * Author:   xuweiwei
 *
 **/

class WorkLogTool{
	
	/**
	 * データ追加の操作ログを作成する
	 * @param string $tableName
	 * @param string $uniqueVal
	 * @param Array　 $updateRow
	 * @return 配列
	 */
	public static function getInsertLogRow($tableName,$uniqueVal,$updateRow,$workKbn = 1){
		$work_ato_data = "";
		$workLogRow = null;
		foreach($updateRow as $index => $value){
			$work_ato_data .= $index.":".$value.";";
		}
		//0検索　1新規　2更新　3削除
		$workLogRow['work_kbn'] = $workKbn;
		//操作対象テーブル
		$workLogRow['table_nm'] = $tableName;
		//ユニーク値
		$workLogRow['unique_value'] = $uniqueVal;
		//操作後のデータ
		$workLogRow['work_mae_data'] = $work_ato_data;
		//更新ユーザー
		$workLogRow['upd_usr'] = $_SESSION['usr_id'];
		return $workLogRow;
	}
	
	/**
	 * データ更新の操作ログを作成する
	 * @param string $tableName
	 * @param string $uniqueVal
	 * @param Array　 $updateRow
	 * @param Array　 $dbDataRow
	 * @return 配列
	 */
	public static function getUpdateLogRow($tableName,$uniqueVal,$updateRow,$dbDataRow){
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
		$workLogRow['upd_usr'] = $_SESSION['usr_id'];
		return $workLogRow;
	}
	
	/**
	 * データ削除の操作ログを作成する
	 * @param string $tableName
	 * @param string $uniqueVal
	 * @return 配列
	 */
	public static function getDeleteLogRow($tableName,$uniqueVal,$deleteRow = NULL){
		$workLogRow = null;
		//0検索　1新規　2更新　3削除
		$workLogRow['work_kbn'] = 3;
		//操作対象テーブル
		$workLogRow['table_nm'] = $tableName;
		//ユニーク値
		$workLogRow['unique_value'] = $uniqueVal;
		//操作前データ
		if(isset($deleteRow)){
			$work_mae_data= "";
			foreach($deleteRow as $index => $value){
				$work_mae_data .= $index.":".$value.";";
			}
			$workLogRow['work_mae_data'] = $work_mae_data;
		}
		//更新ユーザー
		$workLogRow['upd_usr'] = $_SESSION['usr_id'];
		return $workLogRow;
	}
}

?>