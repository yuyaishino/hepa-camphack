<?php
/**
 * 共通ツール
 *
 */
class CommonTool
{

    /**
     * 配列⇒オブジェクト
     *
     * @param array $arr 配列
     * @return object
     */
    public static function array_to_object($arr)
    {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)array_to_object($v);
            }
        }
        return (object)$arr;
    }

    /**
     * オブジェクト⇒配列
     *
     * @param object $obj オブジェクト
     * @return array
     */
    public static function object_to_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }
        return $obj;
    }

    /**
     * 日付をフォーマットする
     * @param unknown $dateStr
     * @param string $format
     * @return string
     */
    public static function formatDate($dateStr, $format = "Y年m月d日")
    {
        if (is_null($dateStr)) {
            return "";
        }
        $dateTime = strtotime($dateStr);
        if (!$dateTime) {
            return "";
        }
        return date($format, $dateTime) . "";
    }

    public static function getExpireTime($dateStr)
    {
        return CommonTool::formatDate($dateStr, 'Y-m-d H:i:s');
    }


    /**
     * 文字列日付が正しいかどうか
     * @param string $dateStr 文字列日付
     * @param array $formats
     * @return boolean
     */
    public static function isDateStr($dateStr, $formats = array("Ymd", "Y-m-d", "Y/m/d", "Ynj", "Y-n-j", "Y/n/j"))
    {
        if (is_null($dateStr)) {
            return false;
        }
        $dateTime = strtotime($dateStr);
        if (!$dateTime) {
            return false;
        }
        if (is_array($formats)) {
            foreach ($formats as $format) {
                if (date($format, $dateTime) == $dateStr) {
// 					print $dateStr."-->".date("Y-m-d", $dateTime)." OK</br>";
                    return true;
                }
            }
        } else {
            if (date($formats, $dateTime) == $dateStr) {
// 				print $dateStr."-->".date("Y-m-d", $dateTime)." OK</br>";
                return true;
            }
        }
        // 		print $dateTime."</br>";
// 		print $dateStr."-->".date("Y-m-d", $dateTime)." NG</br>";
        return false;
    }

    /**
     * 二つ日付の大小比較
     * @param date $date1
     * @param date $date2
     * @param string $compType 日d
     * @return int -1:＜       0:＝     1:＞
     */
    public static function compDate($date1, $date2, $compType = 'd')
    {
        $dateTime1 = null;
        $dateTime2 = null;
        if ($compType == 'd') {
            $dateTime1 = strtotime(CommonTool::formatDate($date1, 'Y-m-d'));
            $dateTime2 = strtotime(CommonTool::formatDate($date2, 'Y-m-d'));
        } else {
            $dateTime1 = strtotime($date1);
            $dateTime2 = strtotime($date2);
        }
        if ($dateTime1 == $dateTime2) {
            return 0;
        } elseif ($dateTime1 < $dateTime2) {
            return -1;
        } else {
            return 1;
        }
    }

    /**
     * NUMERICかどうか
     * @param unknown $numStr
     * @param unknown $precision
     * @param unknown $scale
     * @return boolean
     */
    public static function isNumeric($inStr, $precision = 9, $scale = 1)
    {
        if (!is_numeric($inStr)) {
// 			print $inStr."--> NG</br>";
            return false;
        }
        $num = $inStr + 0;
        $numStr = $num . "";
        if (strpos($numStr, ".")) {
            list($pointPre, $pointNext) = explode('.', $numStr);
            $pointPre = $pointPre + 0;
            $pointNext = $pointNext + 0;
            $pointPreStr = $pointPre . "";
            $pointNextStr = $pointNext . "";
        } else {
            $pointPreStr = $numStr;
            $pointNextStr = "";
        }
        $pointPreLen = $precision - $scale;
        $pointNextLen = $scale;
        if (strlen($pointPreStr) > $pointPreLen) {
// 			print $inStr."--> NG</br>";
            return false;
        }
        if (strlen($pointNextStr) > $pointNextLen) {
// 			print $inStr."--> NG</br>";
            return false;
        }
// 		print $inStr."--> OK</br>";
        return true;
    }

    /**
     * 整数形文字列かどうか
     * @param string $intStr
     * @return boolean
     */
    public static function isIntStr($intStr)
    {
        if (!is_numeric($intStr)) {
// 			print $intStr."--> NG</br>";
            return false;
        }
        if (is_int($intStr + 0)) {
// 			print $intStr."--> OK</br>";
            return true;
        }
// 		print $intStr."--> NG</br>";
        return false;
    }

    /**
     * ミリ秒 が含む日時を戻る
     * @return string
     */
    public static function getDateTimeWithMillisecond()
    {
        list($microsec, $sec) = explode(' ', microtime());
        list($pointPre, $pointNext) = explode('.', $microsec);
        return date("Y-m-d H:i:s.") . $pointNext;
    }

    /**
     * 日付の日を取得する
     *
     * @param unknown $date
     * @return string
     */
    public static function getDayOfDate($date)
    {
        return CommonTool::formatDate($date, "d");
    }

    /**
     * 日付の曜日を取得する
     *
     * @param unknown $date
     * @return string
     */
    public static function getJpWeekOfDate($date, $isFull = false)
    {
        $week = date("w", strtotime($date));
        if ($isFull) {
            if ($week == 0) {
                return "日曜日";
            } elseif ($week == 1) {
                return "月曜日";
            } elseif ($week == 2) {
                return "火曜日";
            } elseif ($week == 3) {
                return "水曜日";
            } elseif ($week == 4) {
                return "木曜日";
            } elseif ($week == 5) {
                return "金曜日";
            } elseif ($week == 6) {
                return "土曜日";
            } else {
                return "";
            }
        } else {
            if ($week == 0) {
                return "日";
            } elseif ($week == 1) {
                return "月";
            } elseif ($week == 2) {
                return "火";
            } elseif ($week == 3) {
                return "水";
            } elseif ($week == 4) {
                return "木";
            } elseif ($week == 5) {
                return "金";
            } elseif ($week == 6) {
                return "土";
            } else {
                return "";
            }
        }
    }

    /**
     * 日付の月を取得する
     *
     * @param unknown $date
     * @return string
     */
    public static function getMonthOfDate($date)
    {
        return CommonTool::formatDate($date, "m");
    }

    /**
     * 日付の年を取得する
     *
     * @param unknown $date
     * @return string
     */
    public static function getYearOfDate($date)
    {
        return CommonTool::formatDate($date, "Y");
    }

    /**
     * 日付をフォーマットする
     * @param unknown $dateStr
     * @param string $format
     * @return string
     */
    public static function formatMDWeakDate($dateStr, $format = "m/d")
    {
        if (is_null($dateStr)) {
            return "";
        }
        $dateTime = strtotime($dateStr);
        if (!$dateTime) {
            return "";
        }

        return date($format, $dateTime) . "(" . CommonTool::getJpWeekOfDate($dateStr) . ")";
    }

    /**
     * 日付をフォーマットする
     * @param unknown $dateStr
     * @param string $format
     * @return string
     */
    public static function formatDWeakDate($dateStr, $format = "d")
    {
        if (is_null($dateStr)) {
            return "";
        }
        $dateTime = strtotime($dateStr);
        if (!$dateTime) {
            return "";
        }
        return date($format, $dateTime) . CommonTool::getJpWeekOfDate($dateStr);
    }

    /**
     *
     * @param unknown $object　配列
     * @param unknown $keystr キー
     * @return boolean
     */
    public static function isUnsetOrNullOrEmpty($object, $keystr)
    {
        if (!isset($object)) {
            return true;
        }
        if (is_null($object)) {
            return true;
        }
        if (!isset($object[$keystr])) {
            return true;
        }
        if (is_null($object[$keystr])) {
            return true;
        }
        if ($object[$keystr] === "") {
            return true;
        }
        return false;
    }

    /**
     *
     * @param unknown $object　配列
     * @param unknown $keystr キー
     * @return boolean
     */
    public static function isSetAndNotNullEmpty($object, $keystr)
    {
        if (!isset($object)) {
            return false;
        }
        if (is_null($object)) {
            return false;
        }
        if (!isset($object[$keystr])) {
            return false;
        }
        if (is_null($object[$keystr])) {
            return false;
        }
        if ($object[$keystr] === "") {
            return false;
        }
        return true;
    }

    /**
     *
     * @param string $intStr
     * @return boolean
     */
    public static function isNullOREmpty($intStr)
    {
        if (!isset($intStr)) {
            return true;
        }
        if (is_null($intStr)) {
            return true;
        }
        if ($intStr === "") {
            return true;
        }
        return false;
    }

    /**
     *
     * @param string $intStr
     * @return boolean
     */
    public static function isNotNullAndEmpty($intStr)
    {
        if (!isset($intStr)) {
            return false;
        }
        if (is_null($intStr)) {
            return false;
        }
        if ($intStr === "") {
            return false;
        }
        return true;
    }

    /**
     * プリントログ
     * @param $info            コンテンツを印刷する
     * @param string $path ファイルパス
     * @param string $name ログファイル名
     */
    public static function prf($info, $path = '../../logs/', $name = 'log.txt')
    {
        $debugTemp = debug_backtrace();
        $debugInfo = $debugTemp[0];
        $log = "\r\n-----------------------------------------------------------------------------------------------------\r\n";
        $log .= "print_time：" . date('Y-m-d H:i:s');
        $log .= "\r\n-----------------------------------------------------------------------------------------------------\r\n";
        if (is_bool($info)) {
            if ($info) {
                $infoData = 'true';
            } else {
                $infoData = 'false';
            }
        } else {
            $infoData = $info;
        }
        $data['line'] = $debugInfo['line'];
        $data['file'] = $debugInfo['file'];
        $data['info'] = $infoData;
        $today = date('Y-m-d');
        $dataFormat = print_r($data, 1);
        $log .= $dataFormat;
        file_put_contents($path . $today . $name, $log, FILE_APPEND);
    }

    /**
     * 二つ日付の中に何の日数が経過
     * @param unknown $date1
     * @param unknown $date2
     * @return int
     */
    public static function diffBetweenTwoDate($date1, $date2)
    {
        $second1 = strtotime($date1);
        $second2 = strtotime($date2);
        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }

    /**
     * 日付が日数をプラス
     * @param unknown $date
     * @param number $days
     * @param string $format
     * @return unknown
     */
    public static function addDate($date, $days = 1, $format = "Y-m-d")
    {
        if ($days == 0) {
            return CommonTool::formatDate($date, $format);
        } else if ($days > 0) {
            $time = "+" . $days . " day";
        } else {
            $time = $days . " day";
        }
        $newDate = strtotime($time, strtotime($date));
        return date($format, $newDate);
    }


    /**
     *
     * 二つ数字の大小比較
     * @param unknown $left_operand
     * @param unknown $right_operand
     * @param number $scale　少数
     * @return int -1:＜       0:＝     1:＞
     */
    public static function bccompNumber($left_operand, $right_operand, $scale = 0)
    {
        if (is_null($left_operand)) {
            $left_operand = 0;
        }
        if (is_null($right_operand)) {
            $right_operand = 0;
        }
        $left_operand = $left_operand . "";
        $right_operand = $right_operand . "";
        if (strpos($left_operand, ".")) {
            $left_operand = $left_operand . "0000000000000000000000";
        } else {
            $left_operand = $left_operand . ".0000000000000000000000";
        }
        list($left_pointPre, $left_pointNext) = explode('.', $left_operand);
        if ($scale > 0) {
            $left_pointNext = substr($left_pointNext, 0, $scale);
        } else {
            $left_pointNext = 0;
        }
        $left_pointPre = $left_pointPre + 0;
        $left_pointNext = $left_pointNext + 0;

        if (strpos($right_operand, ".")) {
            $right_operand = $right_operand . "0";
        } else {
            $right_operand = $right_operand . ".0";
        }
        list($right_pointPre, $right_pointNext) = explode('.', $right_operand);
        if ($scale > 0) {
            $right_operand = substr($right_operand, 0, $scale);
        } else {
            $right_pointNext = 0;
        }
        $right_pointPre = $right_pointPre + 0;
        $right_pointNext = $right_pointNext + 0;

        if ($left_pointPre == $right_pointPre) {
            if ($left_pointNext == $right_pointNext) {
                return 0;
            } else if ($left_pointNext > $right_pointNext) {
                return 1;
            } else {
                return -1;
            }
        } else if ($left_pointPre > $right_pointPre) {
            return 1;
        } else {
            return -1;
        }
    }

    /**
     * シーケンス名称取得
     * @param  $seq_type 1:仕入 2:在庫 3:引取 4:室入 5:荷割 6:出荷 7:出荷仕入紐づけ 8:受注 9:受注出荷紐づけ 10:在庫コード 11:室入報告コード 12:原料
     * 13:在庫調整 14:委託加工原料 15:委託加工報告 16:委託加工出荷 17:仕入情報IF 18:入出荷情報IF 19:作業実績情報IF 20:売上情報IFを追加 21:伝票番号
     * @return シーケンス名称
     */
    public static function getSeqName($seq_type)
    {
        switch ($seq_type) {
            case 1:
                $seq_str = "仕入";
                break;
            case 2:
                $seq_str = "在庫";
                break;
            case 3:
                $seq_str = "引取";
                break;
            case 4:
                $seq_str = "室入";
                break;
            case 5:
                $seq_str = "荷割";
                break;
            case 6:
                $seq_str = "出荷";
                break;
            case 7:
                $seq_str = "出荷仕入紐づけ";
                break;
            case 8:
                $seq_str = "受注";
                break;
            case 9:
                $seq_str = "受注出荷紐づけ";
                break;
            case 10:
                $seq_str = "在庫";
                break;
            case 11:
                $seq_str = "室入報告";
                break;
            case 12:
                $seq_str = "原料";
                break;
            case 13:
                $seq_str = "在庫調整";
                break;
            case 14:
                $seq_str = "委託加工原料";
                break;
            case 15:
                $seq_str = "委託加工報告";
                break;
            case 16:
                $seq_str = "委託加工出荷";
                break;
            case 17:
                $seq_str = "仕入情報IF";
                break;
            case 18:
                $seq_str = "作業実績情報IF";
                break;
            case 19:
                $seq_str = "作業実績情報IF";
                break;
            case 20:
                $seq_str = "売上情報IF";
                break;
            case 21:
                $seq_str = "伝票番号";
                break;
            case 22:
                $seq_str = "仕入価格分割";
                break;
            case 23:
                $seq_str = "顧客伝票番号";
                break;
            case 24:
                $seq_str = "テンプレートID";
                break;
            case 25:
                $seq_str = "バッチコード";
                break;
            case 26:
                $seq_str = "変換コード";
                break;
            case 27:
                $seq_str = "出荷備考";
                break;
            case 28:
                $seq_str = "印刷ID";
                break;
            default:
                $seq_str = "シーケンス";
        }
        return $seq_str . "コード";
    }

    /**
     *  get primary key code
     * @param $db
     * @param  $seq_type 1:仕入 2:在庫 3:引取 4:室入 5:荷割 6:出荷 7:出荷仕入紐づけ 8:受注 9:受注出荷紐づけ 10:在庫コード 11:室入報告コード 12:原料
     * 13:在庫調整 14:委託加工原料 15:委託加工報告 16:委託加工出荷 17:仕入情報IF 18:入出荷情報IF 19:作業実績情報IF 20:売上情報IFを追加 21:伝票番号
     * @param $error : 错误字符串，比如荷割两个字
     * return primaryCode
     **/
    public static function getSeqPrimaryKeyCode($db, $seq_type, $error = "")
    {
        $nowDate = date('Ymd');
        if ($seq_type == 21) {
            $sql = "select nextval('denpyo_seq')";
            $list = $db->findListBySql($sql);
            if (empty($list)) {
                throw new SysException("E0006", '', CommonTool::getSeqName($seq_type));
            }
            $newIndex = $list[0]->nextval;
        } else {
            $db->table('daily_seq');
            unset($where);
            $where['seq_date'] = $nowDate;
            $where['seq_type'] = $seq_type;
            $dailySeqAll = $db->findAll($where);

            if (count($dailySeqAll) > 0) {
                $dailySeqObj = $dailySeqAll[0];
                $newIndex = $dailySeqObj->seq_index + 1;
                $dailySeqObj->seq_index = $newIndex;

                if ($dailySeqObj->seq_index > 999999) {
                    throw new SysException("E0006", '', CommonTool::getSeqName($seq_type));
                } else {
                    $db->table('daily_seq');
                    $whereCondition = 'seq_type =' . $seq_type . 'and '
                        . "seq_date = '$nowDate'";
                    $updateDailySeqObj['seq_index'] = $newIndex;
                    $updateDailySeqObj['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
                    $insetObj['upd_cnt'] = $newIndex;
                    $db->update($updateDailySeqObj, $whereCondition);
                }
            } else {
                $insetObj['seq_date'] = $nowDate;
                $insetObj['seq_index'] = 1;
                $insetObj['seq_type'] = $seq_type;
                $insetObj['ins_usr'] = $_SESSION['usr_id'];
                $insetObj['upd_usr'] = $_SESSION['usr_id'];
                $insetObj['ins_dttm'] = CommonTool::getDateTimeWithMillisecond();
                $insetObj['upd_dttm'] = CommonTool::getDateTimeWithMillisecond();
                $insetObj['upd_cnt'] = 1;
                $insertObjId = $db->add($insetObj);
                if ($insertObjId <= 0) {
                    throw new SysException("E0006", '', CommonTool::getSeqName($seq_type));
                }
                $newIndex = 1;
            }
        }

        $tempCount = '000000';
        if (($newIndex > 0) && ($newIndex < 10)) {
            $tempCount = '00000' . $newIndex;
        } elseif (($newIndex >= 10) && ($newIndex < 100)) {
            $tempCount = '0000' . $newIndex;
        } elseif (($newIndex >= 100) && ($newIndex < 1000)) {
            $tempCount = '000' . $newIndex;
        } elseif (($newIndex >= 1000) && ($newIndex < 10000)) {
            $tempCount = '00' . $newIndex;
        } elseif (($newIndex >= 10000) && ($newIndex < 100000)) {
            $tempCount = '0' . $newIndex;
        } elseif (($newIndex >= 100000) && ($newIndex <= 999999)) {
            $tempCount = $newIndex;
        }
        $displayDate = substr($nowDate, 2, strlen($nowDate));
        $keyCode = $displayDate . $tempCount;
        return $keyCode;
    }

    /**
     * 端数処理
     * @param unknown $decimal
     * @param unknown $model 1切り捨て  2四捨五入  3引上げ
     * @return boolean
     */
    public static function rounding($decimal, $model = '2')
    {
        if ($model == '1') {
            //切り捨て
            return floor($decimal);
        } elseif ($model == '3') {
            //引上げ
            return ceil($decimal);
        } else {
            //四捨五入
            return round($decimal);
        }
    }

    /**
     * 指定日時の1日ごとに
     * @param  Date $startdate from
     * @param  Date $enddate to
     * @return Array
     */
    public static function getDateFromRange($startdate, $enddate)
    {
        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);
        // 計算期間内に何日ありますか
        $days = ($etimestamp - $stimestamp) / 86400 + 1;
        // 毎日の日付を保存する
        $date = array();
        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $stimestamp + (86400 * $i));
        }

        return $date;
    }

    /**
     * 締日テーブルの締日
     * @param $db
     */
    public static function getClosingDateToSql($db)
    {
        $queryClosingDateSql = "SELECT closing_date FROM mdm_closing_date LIMIT 1";
        $ClosingDateListResult = $db->query($queryClosingDateSql);
        $closing_date = $db->fetchAll($ClosingDateListResult);
        $res = isset($closing_date[0]) ? $closing_date[0]->closing_date : '';
        $response['status'] = 1;
        $response['message'] = '';
        $response['responseData'] = $res;
        echo json_encode($response);
        exit;
    }

    /**
     * 締日テーブルの締日（后端判断使用）
     * @param $db
     * @return closingDateObjList
     */
    public static function getClosingDateObjListToSql($db)
    {
        $queryClosingDateSql = "SELECT closing_date FROM mdm_closing_date LIMIT 1";
        $ClosingDateListResult = $db->query($queryClosingDateSql);
        $closing_date = $db->fetchAll($ClosingDateListResult);
        $res = isset($closing_date[0]) ? $closing_date[0]->closing_date : '';
        return $res;
    }

    /**
     * 用途
     * @param $db
     */
    public static function getNaiyoToSql($db)
    {
        $purpose_kbn = CdkbnSikbtCd::purpose_kbn;
        $queryNaiyoSql = "SELECT kbn_naiyo,kbn_naiyo_nm as purpose_kbn FROM mdm_cdkbn WHERE sikbt_cd = '" . $purpose_kbn . "' AND del_flg = '0'";
        $NaiyoListResult = $db->query($queryNaiyoSql);
        $naiyo_nm = $db->fetchAll($NaiyoListResult);

        $response['status'] = 1;
        $response['message'] = '';
        $response['responseData'] = $naiyo_nm;
        echo json_encode($response);
        exit;
    }

    /**
     * 快速排序算法(升序)
     * @param 排序列表
     * @param $l 排序起始索引: 0
     * @param $r 排序截止索引:count($resultList) - 1
     * @param $conditionObj 排序条件数组，数组中包含$conditionObj['orderKey']
     **/
    public static function quickSortAsc(&$resultList, $l, $r, $conditionObj)
    {
        $key = $conditionObj ['orderKey'];
        foreach ($resultList as $index => $result) {
            if (!isset($result->$key)) {
                if ($key != "") {
                    $resultList[$index]->$key = "";
                }
            }
        }
        if ($key != '') {
            if ($l < $r) {
                $i = $l;
                $j = $r;
                $temp = $resultList[$l];
                while ($i < $j) {
                    while ($i < $j && $resultList[$j]->$key >= $temp->$key) {
                        $j--;
                    }
                    if ($i < $j) {
                        $resultList[$i++] = $resultList[$j];
                    }
                    while ($i < $j && $resultList[$i]->$key < $temp->$key) {
                        $i++;
                    }
                    if ($i < $j) {
                        $resultList[$j--] = $resultList[$i];
                    }
                }
                $resultList[$i] = $temp;
                self::quickSortAsc($resultList, $l, $i - 1, $conditionObj);
                self::quickSortAsc($resultList, $i + 1, $r, $conditionObj);
            }
        }
    }

    /**
     * 快速排序算法(降序)
     * @param 排序列表
     * @param $l 排序起始索引: 0
     * @param $r 排序截止索引:count($resultList) - 1
     * @param $conditionObj 排序条件数组，数组中包含$conditionObj['orderKey']
     **/
    public static function quickSortDesc(&$resultList, $l, $r, $conditionObj)
    {
        $key = $conditionObj ['orderKey'];
        foreach ($resultList as $index => $result) {
            if (!isset($result->$key)) {
                if ($key != "") {
                    $resultList[$index]->$key = "";
                }
            }
        }
        if ($key != '') {
            if ($l < $r) {
                $i = $l;
                $j = $r;
                $temp = $resultList[$l];
                while ($i < $j) {
                    while ($i < $j && $resultList[$j]->$key <= $temp->$key) {
                        $j--;
                    }
                    if ($i < $j) {
                        $resultList[$i++] = $resultList[$j];
                    }
                    while ($i < $j && $resultList[$i]->$key > $temp->$key) {
                        $i++;
                    }
                    if ($i < $j) {
                        $resultList[$j--] = $resultList[$i];
                    }
                }
                $resultList[$i] = $temp;
                self::quickSortDesc($resultList, $l, $i - 1, $conditionObj);
                self::quickSortDesc($resultList, $i + 1, $r, $conditionObj);
            }
        }
    }

    /**
     * 依据室入报告code获取在库数
     * @param $db
     * @param $dataList 包含室入报告code对象数组
     * @param mergeTag, case 在库数与pack在库数是否分开计算的标志，mergeTag为 0，分开计算，mergeTag为 1，不分开计算，
     * return $dataList 每个对象添加了inventoryCnt字段
     **/
    public static function getInventoryCntAccordIntoRoomReportCd($db, $mergeTag, $dataList)
    {

        // 查询出荷报告
        $shippingSql = "
        SELECT
into_room_report_cd,
case_cnt,
pack_cnt
FROM
shipping_report
WHERE del_flg = '0'
    ";
        $shippingRes = $db->query($shippingSql);
        $shippingResList = $db->fetchAll($shippingRes);

        // 查询出库原料表
        $materialSql = "
        SELECT
delivery_cnt,
into_room_report_cd
FROM
delivery_material
WHERE del_flg = '0'
    ";
        $materialRes = $db->query($materialSql);
        $materialResList = $db->fetchAll($materialRes);
        if ($mergeTag == 1) {// case数pack数合并的算法
            foreach ($dataList as $data) {
                $intoRoomReportCnt = 0;
                $totalShippingCnt = 0;
                $totalMaterialCnt = 0;
                foreach ($shippingResList as $shipping) {
                    if ($data->into_room_report_cd == $shipping->into_room_report_cd) {
                        if (isset($shipping->pack_cnt)
                            && $shipping->pack_cnt != ''
                            && isset($shipping->quantity_per_carton)
                            && $shipping->quantity_per_carton != '') {
                            $totalShippingCnt += (float)$shipping->case_cnt + (float)$shipping->pack_cnt / (float)$shipping->quantity_per_carton;
                        } else {
                            $totalShippingCnt += (float)$shipping->case_cnt;
                        }
                    }
                }
                foreach ($materialResList as $material) {
                    if ($data->into_room_report_cd == $material->into_room_report_cd) {
                        $totalMaterialCnt += (float)$material->delivery_cnt;
                    }
                }
                if (isset($data->pack_cnt)
                    && $data->pack_cnt != ''
                    && isset($data->quantity_per_carton)
                    && $data->quantity_per_carton != '') {
                    $intoRoomReportCnt += (float)$data->case_cnt + (float)$data->pack_cnt / (float)$data->quantity_per_carton;
                } else {
                    $intoRoomReportCnt += (float)$data->case_cnt;
                }
                $data->inventory_cnt = $intoRoomReportCnt - $totalShippingCnt - $totalMaterialCnt;
            }
        } else {// case数与pack数分开的算法
            foreach ($dataList as $data) {
                $totalShippingCaseCnt = 0;
                $totalShippingPackCnt = 0;
                foreach ($shippingResList as $shipping) {
                    if ($data->into_room_report_cd == $shipping->into_room_report_cd) {
                        $totalShippingCaseCnt += (float)$shipping->case_cnt;
                        $totalShippingPackCnt += (float)$shipping->pack_cnt;
                    }
                }
                $totalMaterialCnt = 0;
                foreach ($materialResList as $material) {
                    if ($data->into_room_report_cd == $material->into_room_report_cd) {
                        $totalMaterialCnt += (float)$material->delivery_cnt;
                    }
                }
                $data->inven_case_cnt = $data->case_cnt - $totalMaterialCnt - $totalShippingCaseCnt;
                $data->inven_pack_cnt = $data->pack_cnt - 0 - $totalShippingPackCnt;
            }
        }
        return $dataList;
    }

    /**
     * 依据室入报告code获取在库数(给G3312出荷入力使用)
     * @param $db
     * @param $dataList 包含室入报告code对象数组
     * @param mergeTag, case 在库数与pack在库数是否分开计算的标志，mergeTag为 0，分开计算，mergeTag为 1，不分开计算，
     * return $dataList 每个对象添加了inventoryCnt字段
     **/
    public static function getInventoryCntAccordIntoRoomReportCdTemp($db, $mergeTag, $dataList)
    {

        // 查询出荷报告
        $shippingSql = "
        SELECT
into_room_report_cd,
shipping_cnt
FROM
shipping_into_room
WHERE del_flg = '0'
    ";
        $shippingRes = $db->query($shippingSql);
        $shippingResList = $db->fetchAll($shippingRes);

        // 查询出库原料表
        $materialSql = "
        SELECT
delivery_cnt,
into_room_report_cd
FROM
delivery_material
WHERE del_flg = '0'
    ";
        $materialRes = $db->query($materialSql);
        $materialResList = $db->fetchAll($materialRes);
        if ($mergeTag == 1) {// case数pack数合并的算法
            foreach ($dataList as $data) {
                $intoRoomReportCnt = 0;
                $totalShippingCnt = 0;
                $totalMaterialCnt = 0;
                foreach ($shippingResList as $shipping) {
                    if ($data->into_room_report_cd == $shipping->into_room_report_cd) {
                        if (isset($shipping->shipping_cnt)
                            && $shipping->shipping_cnt != ''
                        ) {
                            $totalShippingCnt += (float)$shipping->shipping_cnt;
                        } else {
                            $totalShippingCnt += 0;
                        }
                    }
                }
                foreach ($materialResList as $material) {
                    if ($data->into_room_report_cd == $material->into_room_report_cd) {
                        $totalMaterialCnt += (float)$material->delivery_cnt;
                    }
                }
                if (isset($data->pack_cnt)
                    && $data->pack_cnt != ''
                    && isset($data->quantity_per_carton)
                    && $data->quantity_per_carton != '') {
                    $intoRoomReportCnt += (float)$data->case_cnt + (float)$data->pack_cnt / (float)$data->quantity_per_carton;
                } else {
                    $intoRoomReportCnt += (float)$data->case_cnt;
                }
                $data->inventory_cnt = $intoRoomReportCnt - $totalShippingCnt - $totalMaterialCnt;
            }
        } else {// case数与pack数分开的算法
            foreach ($dataList as $data) {
                $totalShippingCaseCnt = 0;
                $totalShippingPackCnt = 0;
                foreach ($shippingResList as $shipping) {
                    if ($data->into_room_report_cd == $shipping->into_room_report_cd) {
                        $totalShippingCaseCnt += (float)$shipping->case_cnt;
                        $totalShippingPackCnt += (float)$shipping->pack_cnt;
                    }
                }
                $totalMaterialCnt = 0;
                foreach ($materialResList as $material) {
                    if ($data->into_room_report_cd == $material->into_room_report_cd) {
                        $totalMaterialCnt += (float)$material->delivery_cnt;
                    }
                }
                $data->inven_case_cnt = $data->case_cnt - $totalMaterialCnt - $totalShippingCaseCnt;
                $data->inven_pack_cnt = $data->pack_cnt - 0 - $totalShippingPackCnt;
            }
        }
        return $dataList;
    }

    /***
     * G2115とG2116の在庫数の计算
     * 引取ドライバーテーブルの引取数 － 同一引取コード、仕入コード、仕入価格カウンタ、ドライバーコードの室入報告のケース数
     * @param $db
     * @param $dataList
     */
    public static function getInventoryCnt($db, $dataList)
    {
        $sql = " SELECT
    into_room_report.purchase_cd,
    into_room_report.purchase_price_cnt,
    into_room_report.transaction_cd,
    into_room_report.driver_cd,
    (transactions_driver.transaction_cnt - COALESCE(SUM(into_room_report.case_cnt), 0)) AS inventory_cnt
    FROM transactions_driver
    LEFT JOIN into_room_report ON
    into_room_report.purchase_cd = transactions_driver.purchase_cd
    AND into_room_report.purchase_price_cnt = transactions_driver.purchase_price_cnt
    AND into_room_report.transaction_cd = transactions_driver.transaction_cd
    AND into_room_report.driver_cd = transactions_driver.driver_cd
    WHERE transactions_driver.del_flg = '0'
    ";
        foreach ($dataList as $key => $data) {
            if ($key == 0) {
                $sql .= " AND (";
                $sql .= "  ( into_room_report.purchase_cd = '" . $data->purchase_cd . "'";
                $sql .= "  AND into_room_report.purchase_price_cnt = '" . $data->purchase_price_cnt . "'";
                $sql .= "  AND into_room_report.transaction_cd = '" . $data->transaction_cd . "'";
                $sql .= "  AND into_room_report.driver_cd = '" . $data->driver_cd . "') ";
            } else {
                $sql .= " OR ( into_room_report.purchase_cd = '" . $data->purchase_cd . "'";
                $sql .= "  AND into_room_report.purchase_price_cnt = '" . $data->purchase_price_cnt . "'";
                $sql .= "  AND into_room_report.transaction_cd = '" . $data->transaction_cd . "'";
                $sql .= "  AND into_room_report.driver_cd = '" . $data->driver_cd . "') ";
            }
            if ($key == count($dataList) - 1) {
                $sql .= ")";
            }
        };
        $sql .= " GROUP BY
            transactions_driver.transaction_cnt,
            into_room_report.purchase_cd,
            into_room_report.purchase_price_cnt,
            into_room_report.transaction_cd,
            into_room_report.driver_cd";
        $resDataList = $db->findListBySql($sql);

        foreach ($dataList as $keyIndex => $data) {
            $key = $data->purchase_cd . $data->purchase_price_cnt . $data->transaction_cd . $data->driver_cd;
            foreach ($resDataList as $resData) {
                $resKey = $resData->purchase_cd . $resData->purchase_price_cnt . $resData->transaction_cd . $resData->driver_cd;
                if ($resKey == $key) {
                    $dataList[$keyIndex]->inventory_cnt = $resData->inventory_cnt;
                }
            }
        }
        return $dataList;
    }

    /**
     * @param $var
     * @param bool $exit
     * @return bool
     */
    public static function printR($var, $exit = true)
    {
        if (!isset($var)) {
            exit('please input');
        }
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
            var_export($var);
        } else {
            echo "<pre style='font-size: 18px'>";
            var_export($var);
            echo "</pre>";
        };
        if ($exit) {
            exit();
        }
        return true;
    }


    /**
     * @param $db
     * @param $tableName
     * @param $uniqueVal
     * @param $dataRow
     * @param $pageId
     * @param $callTicsIfFlag TICSIF共通ツールの実行Flag
     * @return bool
     */
    public static function insertWorkLog($db, $tableName, $uniqueVal, $dataRow, $pageId, $callTicsIfFlag = True)
    {
        $dataRow = is_array($dataRow) ? (object)$dataRow : $dataRow;
        //操作ログを作成する
        $insertLogRow = WorkLogTool::getInsertLogRow($tableName, $uniqueVal, $dataRow);
        $db->table("work_log");
        $insertLogRow['page_cd'] = $pageId;
        $insertLogRow['upd_dttm'] = self::getDateTimeWithMillisecond();
        $logRes = $db->add($insertLogRow);
        if ($callTicsIfFlag) {
            //TICSIF共通ツールを実行する
            $ticsRes = TICSIFTool::insertIFRow($db, $tableName, $dataRow, $pageId);
            if ($logRes === true && $ticsRes === true) {
                return true;
            }
        }
        if ($logRes === true) {
            return true;
        }
        return false;
    }

    /**
     * @param $db
     * @param $tableName
     * @param $uniqueVal
     * @param $dataRow
     * @param $beforeDataRow
     * @param $pageId
     * @param $callTicsIfFlag TICSIF共通ツールの実行Flag
     * @return bool
     */
    public static function updateWorkLog($db, $tableName, $uniqueVal, $dataRow, $beforeDataRow, $pageId, $callTicsIfFlag = True)
    {
        $dataRow = self::getLogDataRow($dataRow, $beforeDataRow);
        //操作ログを作成する
        $insertLogRow = WorkLogTool::getUpdateLogRow($tableName, $uniqueVal, $dataRow, $beforeDataRow);
        $db->table("work_log");
        $insertLogRow['page_cd'] = $pageId;
        $insertLogRow['upd_dttm'] = self::getDateTimeWithMillisecond();
        $logRes = $db->add($insertLogRow);
        if ($callTicsIfFlag) {
            //TICSIF共通ツールを実行する
            $ticsRes = TICSIFTool::updateIFRow($db, $tableName, $beforeDataRow, $dataRow, $pageId);
            if ($logRes === true && $ticsRes === true) {
                return true;
            }
        }
        if ($logRes === true) {
            return true;
        }
        return false;
    }

    /**
     * @param $db
     * @param $tableName
     * @param $uniqueVal
     * @param $dataRow
     * @param $pageId
     * @param $callTicsIfFlag TICSIF共通ツールの実行Flag
     * @return bool
     */
    public static function deleteWorkLog($db, $tableName, $uniqueVal, $dataRow, $pageId, $callTicsIfFlag = True)
    {
        $dataRow = is_array($dataRow) ? (object)$dataRow : $dataRow;
        //操作ログを作成する
        $insertLogRow = WorkLogTool::getDeleteLogRow($tableName, $uniqueVal, $dataRow);
        $db->table("work_log");
        $insertLogRow['page_cd'] = $pageId;
        $insertLogRow['upd_dttm'] = self::getDateTimeWithMillisecond();
        $logRes = $db->add($insertLogRow);
        if ($callTicsIfFlag) {
            //TICSIF共通ツールを実行する
            $ticsRes = TICSIFTool::deleteIFRow($db, $tableName, $dataRow, $pageId);
            if ($logRes === true && $ticsRes === true) {
                return true;
            }
        }
        if ($logRes === true) {
            return true;
        }
        return false;
    }

    /**
     * @param $dataRow
     * @param $beforeDataRow
     * @return object
     */
    public static function getLogDataRow($dataRow, $beforeDataRow)
    {
        if (is_array($dataRow)) {
            foreach ($beforeDataRow as $k => $v) {
                if (!isset($dataRow[$k])) {
                    $dataRow[$k] = $v;
                }
            }
            return (object)$dataRow;
        } else {
            foreach ($beforeDataRow as $k => $v) {
                if (!isset($dataRow->$k)) {
                    $dataRow->$k = $v;
                }
            }
            return $dataRow;
        }
    }

    public static function finalNumberFormat($number)
    {
        $number = $number . '';
        if (strpos($number, '.') > 0) {
            $numberArr = explode(".", $number);
            if ($numberArr[1] == 0) {
                return $numberArr[0];
            } else {
                return $numberArr[0] . '.' . substr($numberArr[1], 0, 1);
            }
        } else {
            return $number;
        }
    }

    /**
     * isNotOverLength
     * @param string $str
     * @return boolean
     */
    public static function isNotOverLength($str, $lengthsize)
    {
        if ($str == null || $str == "") {
            return true;
        }
        if (mb_strlen($str, 'utf8') <= $lengthsize) {
            return true;
        }
        return false;
    }

    /**
     * 「AAA△BBB」と入力した場合、%AAA%と％BBB%を条件にあいまい検索を行う。
     * @param    $key
     * @param    $value
     * @param    $tableAsName
     * @return    $sql
     */
    public static function getLikeConditionSQL($key, $value, $tableAsName)
    {
        $valueTransList = str_replace("　", " ", $value);
        $valueTransList = preg_replace("/\s(?=\s)/", "\\1", $valueTransList);
        $valueTransList = trim($valueTransList);
        $valueList = explode(" ", $valueTransList);

        //「AAA△BBB」と入力した場合、%AAA%と％BBB%を条件にあいまい検索を行う。
        $sql = "";
        for ($i = 0; $i < count($valueList); $i++) {
            $sql .= $tableAsName . "." . $key . " LIKE '%" . $valueList[$i] . "%'  and ";
        }
        $sql = substr($sql, 0, -4);
        return $sql;
    }

    /**
     * 条件で「AAA△BBB」と入力した場合、SQL用のArrayを作成すること
     * @param    $value
     * @return    $valueList
     */
    public static function getSpaceStrToArray($value)
    {
        $valueTransList = str_replace("　", " ", $value);
        $valueTransList = preg_replace("/\s(?=\s)/", "\\1", $valueTransList);
        $valueTransList = trim($valueTransList);
        $valueList = explode(" ", $valueTransList);
        return $valueList;

    }

    /**
     * 全角かどうかを判断する
     * @param str　テストする文字列
     * @returns　true/false
     */
    public static function isFullAngle($str)
    {
        if (preg_match('/[^\x{00}-\x{ff}]+/u', $str) == 1) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 全角かどうかを判断する
     * @param str　テストする文字列
     * @returns　true/false
     */
    public static function isContainsUpperCase($str)
    {
        if (preg_match('/[A-Z]+/u', $str) == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 全角かどうかを判断する
     * @param str　テストする文字列
     * @returns　true/false
     */
    public static function isContainsLowerCase($str)
    {
        if (preg_match('/[a-z0-9]+/u', $str) == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否 email
     * @param str　テストする文字列
     * @returns　true/false
     */
    public static function isEmail($str)
    {
        if (preg_match('/^([a-z0-9])+([a-z0-9]*[\-\_]*[a-z0-9]*\.*[a-z0-9\-\_\.])*@[a-z0-9\-\_\.]+([\-\_]*[a-z0-9]+)*(\.*[a-z0-9\-\_]*)+/u', $str,$dir) == 1) {
            if(sizeof($dir) > 0 && $dir[0] == $str){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * 半角かどうかを判断する
     * @param str　テストする文字列
     * @returns　true/false
     */
    public static function isHalfAngle($str)
    {
        if (preg_match('/[\x{00}-\x{ff}]+/u', $str) == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  パスワードですか
     */
    public static function isPassword($str)
    {
        if (preg_match('/^[A-Za-z0-9]+$/', $str) == 1) {
            return true;
        } else {
            return false;
        }
    }

    private static $str = '0123456789';

    public static function randomCode()
    {
        $str2 = str_shuffle(CommonTool::$str);
        $str3 = substr($str2, -4);
        return $str3;
    }

    public static function isZenKatakana($str)
    {
        $str = ($str == null) ? "" : $str;
        if (preg_match('/^[ァ-ヶー　]+$/u', $str)) {    //"ー"の後ろの文字は全角スペースです。
            return true;
        } else {
            return false;
        }
    }


    public static function curl_get_https($url,$timeout,$user,$password)
    {
        $authStr = $user.':'.$password;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $authStr);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  // 跳过检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  // 跳过检查
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        $resp[] = curl_exec($curl);  //$tmpInfo
        $resp[] = curl_getinfo($curl, CURLINFO_HTTP_CODE); //$http_code
        if($resp[1] == 401){
            $resp[] = false;
        } else if (curl_error($curl) != '' && curl_error($curl) != null) {
            $resp[] = false;  //$status
        } else {
            $resp[] = true;
        }
        curl_close($curl);
        return $resp;   //返回json对象
    }

    public static function myIsFullAngle($str)
    {
        $rep = '[^\x00-\xff]';
        if ($str != null && $str != "") {
            //var_dump(preg_match($rep,$str));
            if (preg_match($rep, $str)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function generatePassword($length = 8)
    {
        $password = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < $length; $i++) {

            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * 日付の書式が正しいかどうかを検証します。
     *
     * @param string $date 日付
     * @param string $formats 検査が必要なフォーマット配列
     * @return boolean
     */
    public static function checkDateIsValid($date, $formats = array("Y-m-d", "Y/m/d"))
    {

        $unixTime = strtotime($date);
        if (!$unixTime) { //strtotime日付の書式は明らかに違います。
            return false;
        }

        //日付の有効性を確認します。フォーマットを一つ満たしていれば大丈夫です。
        foreach ($formats as $format) {
            if (date($format, $unixTime) == $date) {
                return true;
            }
        }
        return false;

    }


}

