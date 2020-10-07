<?php
/**
 * セッション共通ツール
 *
 * Date:     2018/03/16
 * Author:   xuweiwei
 *
 **/

//セッション起動

//ベースURL
// $PHP_SERVER_BASE_URL = "http://localhost/coresys";
// $PHP_SERVER_BASE_URL = "https://coresys.hynesen.info";
// $PHP_SERVER_BASE_URL = "https://coresys.hynesen.jp";
$PHP_SERVER_BASE_URL = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'];

//SELFのURL
$PHP_SELF = $_SERVER['PHP_SELF'];

//ログインしてなかったらログイン画面へ
if(!isset($_SESSION['ssID'])){
	$_SESSION['loginURL'] = $PHP_SELF;
	// 	//URL
	// 	$PHP_SELF = $_SERVER['PHP_SELF'];
	// 	$subLen = strpos($PHP_SELF,'/webapps/');
	// 	if(!$subLen){
	// 		$subLen = strpos($PHP_SELF,'/application/');
	// 	}
	// 	$baseUrl = substr($PHP_SELF,0,$subLen);
	// 	$loginUrl = $baseUrl."/webapps/Login.php";
	$loginUrl = $PHP_SERVER_BASE_URL."/webapps/Login.php";

	if($_SERVER['REQUEST_METHOD'] == 'GET'){
        header("Location: ".$loginUrl);
    } else if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $responseData = array();
        $responseData['status'] = 403;
        $responseData['message'] = '';
        $responseData['responseData'] = '';
        echo json_encode($responseData);
    }
	exit;
}

//業務処理のアクセス制御
if(strpos($PHP_SELF,'/application/')){
	if (isset($_SERVER['HTTP_REFERER'])){
		$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
		//他のサーバからのURLを禁止する
		if(0!=strpos($HTTP_REFERER,$PHP_SERVER_BASE_URL)){
			$_SESSION['Common_Error_Cd'] = "S999";
			$errorUrl = $PHP_SERVER_BASE_URL."/webapps/Error.php";
			header("Location: ".$errorUrl);
			exit;
		}
	}else{
		//業務URLは直接にアクセスできない
		$_SESSION['Common_Error_Cd'] = "S998";
		$errorUrl = $PHP_SERVER_BASE_URL."/webapps/Error.php";
		header("Location: ".$errorUrl);
		exit;
	}
	//CSRFのTOKENチェック
	if(!isset($_POST['CSRF_MIDDLE_TOKEN'])){
		$_SESSION['Common_Error_Cd'] = "S997";
		$errorUrl = $PHP_SERVER_BASE_URL."/webapps/Error.php";
		header("Location: ".$errorUrl);
		exit;
	}
	//CSRF制御
	$postCsrfMiddleToken = $_POST['CSRF_MIDDLE_TOKEN'];
	$sessionCsrfMiddleToken = $_SESSION['SESSION_CSRF_MIDDLE_TOKEN'];
	if($postCsrfMiddleToken != $sessionCsrfMiddleToken){
		$_SESSION['Common_Error_Cd'] = "S997";
		$errorUrl = $PHP_SERVER_BASE_URL."/webapps/Error.php";
		header("Location: ".$errorUrl);
		exit;
	}
}

//OSコマンドインジェクションは、OSコマンドを実行しないんで不要だと思うけど。

?>