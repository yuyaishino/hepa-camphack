<?php
/**
 * 非同期で実行ツール
 *
 * PageID:   CallAsyncPhpTool
 * Date:     2020/07/27
 * Author:   xuweiwei
 *
 **/

require_once("CommonTool.class.php");
require_once "SessionCheck.php";

global $MY_HOST_BASE_URL;
$MY_HOST_BASE_URL = $PHP_SERVER_BASE_URL;

class CallAsyncPhpTool{	
	
	/**
	 * 非同期リクエスト
	 * 
	 * @param String $path   URLパス
	 * @param Object $param　パラメータ
	 * @return integer|boolean 書き込んだバイト数、またはエラー時に FALSE を返します。 
	 */
	public static function asyncRequest($path, $param = null){
// 		ini_set('max_execution_time', '3600');
		global $MY_HOST_BASE_URL;
		$query=null;
		if(isset($param) && !empty($param)){
			$query = http_build_query($param);
		}
		//指定した場合は、システムコール connect() で発生したエラーのエラー番号が格納されます。
		$errno = 0;
		//エラーメッセージを表す文字列
		$errstr = '';
		//接続タイムアウト秒数
		$timeout = 300;
		
		$info=parse_url($MY_HOST_BASE_URL);
		//ホスト
		$host = $info["host"];
		//ホストで指定したリソースへのソケット接続を開始します。 
		$fp = @fsockopen($host, 80, $errno, $errstr, $timeout);
		
		if (!$fp) {
			//開けません
			return "ERROR: $errno - $errstr";
		}
		if ($errno || !$fp) {
			//開けません
			return "ERROR: $errno - $errstr";
		}
		
		//ストリームのブロックモードを有効にする / 解除する
		stream_set_blocking($fp,0);
		//ストリームにタイムアウトを設定する
		stream_set_timeout($fp, 30);
		
		$out  = "POST " . $path . " HTTP/1.1".PHP_EOL;
		$out .= "host:" . $host .PHP_EOL;
		$out .= "content-length:" . strlen($query) .PHP_EOL;
		$out .= "content-type:application/x-www-form-urlencoded".PHP_EOL;
		$out .= "connection:close".PHP_EOL.PHP_EOL;
		$out .= $query;
		
		//バイナリセーフなファイル書き込み処理
		$result = @fputs($fp, $out);
		
		//オープンされたファイルポインタをクローズする
		@fclose($fp);
		return $result;
		
	
	}
}