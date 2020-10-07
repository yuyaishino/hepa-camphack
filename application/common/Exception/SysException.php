<?php

// 错误日志路径
$GLOBALS['error_log_path'] = '/data/vhosts/1/logs/php_error.log';

/**
 * カスタマイズシステム異常
 * @author    xuweiwei
 * @date:     2018/03/20
 *
 */

//ユーザー定義のエラーハンドラ関数を設定する
set_error_handler('exceptionHandler');



/**
 * エラーハンドラ関数
 * @param int $errno		発生させる エラーのレベル
 * @param string $errstr	エラーメッセージ
 * @param string $errfile	エラーが発生したファイルの名前
 * @param int $errline	 	エラーが発生した行番号
 * @throws Exception
 */
function exceptionHandler($errno , $errstr, $errfile, $errline)
{
	if (!(error_reporting() & $errno)) {
		// error_reporting 設定に含まれていないエラーコードのため、
		// 標準の PHP エラーハンドラに渡されます。
		return;
	}
	
	//以下のエラータイプは、ユーザー定義の関数では扱えません。 
	//E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING
	switch ($errno) {
		case E_USER_WARNING:
			break;
		case E_USER_NOTICE:
			break;			
		case E_USER_ERROR:
			logOutput($errstr, $errfile, $errline);
			throw new Exception($errstr,$errno);
			break;			
		default:
			logOutput($errstr, $errfile, $errline);
			throw new Exception($errstr,$errno);
			break;
	}
	
	/* PHP の内部エラーハンドラを実行しません */
	return true;
}

/**
 * エラー情報をファイルに書き込む
 * @param string $errstr	エラーメッセージ
 * @param string $errfile	エラーが発生したファイルの名前
 * @param int $errline	 	エラーが発生した行番号
 */
function logOutput($errstr, $errfile, $errline) {
	//データを書き込むファイルへのパス
// 	$filename = 'C:\Users\84770\Desktop/php_error.log';
	//$filename = 'D:/wamp64/logs/php_error.log';
    $filename = $GLOBALS['error_log_path'];
	// 	$filename = '/tmp/php_errors.log';
	//$filename = '/var/log/php-fpm/www-error.log';
	//書き込む文字列
	$outputString = date("Y-m-d H:i:s")." ".$errstr;
	if(isset($errfile)){
		$outputString = $outputString." in ".$errfile;
	}
	if(isset($errline)){
		$outputString = $outputString." on line".$errline;
	}
	$outputString = $outputString."\n";
	//中身をファイルに書き出します。
	file_put_contents($filename, $outputString, FILE_APPEND|LOCK_EX);
}

class SysException extends Exception{
	/**
	 * エラーコード
	 */
	protected $errCode = "";
	/**
	 * 替換文字
	 */
	protected $replaceTexts　= null;
	
	public function getErrCode(){
		return $this->errCode;
	}
	
	public function getReplaceTexts(){
		return $this->replaceTexts;
	}
	
	/**
	 * 初期化
	 * @param unknown $errCode　　　エラーコード
	 * @param string $errMessage　　エラーメッセージ
	 * @param unknown $replaceTexts　替換文字
	 */
	public function __construct($errCode,$errMessage="",$replaceTexts=null,$previous = null){
		parent::__construct($errMessage);
		$this->errCode = $errCode;
		$this->replaceTexts = $replaceTexts;
		if(isset($previous)){
			$errTraceList = $previous->getTrace();
			if(isset($errTraceList)){
				$errstr = "Trace：\n";
				foreach ($errTraceList as $key => $err){
					if(isset($err['file'])){
						$errstr.= "#".$key." ";
						$errstr.= $err['file']."(";
						if(isset($err['line'])){
							$errstr.= $err['line'];
						}
						$errstr.= "): ";
						if(isset($err['class'])){
							$errstr.= $err['class'];
						}
						if(isset($err['type'])){
							$errstr.= $err['type'];
						}
						if(isset($err['function'])){
							$errstr.= $err['function'];
						}
						$errstr.= "\n";
					}
				}
				logOutput($errstr,null,null);
			}
		}
	}
}