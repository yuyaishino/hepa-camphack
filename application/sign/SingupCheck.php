<?php
/**
 * 荷割入力
 *
 * PageID:   G3111
 * Date:     2018/04/19
 * Author:   haominglei
 *
 **/

// セッション共通処理
require_once("../common/message/MessageTool.php");
require_once("../common/Exception/SysException.php");
require_once("../common/comUtils/CommonTool.class.php");
require_once '../common/db/dbini.php';
require_once '../common/db/WorkLogTool.php';
require_once '../common/PHPMailer/mailerInit.php';
require_once("../../application/common/db/sessioninit.php");
//require_once ("../../application/common/comUtils/SessionCheck.php");
try{

    $db->startTransaction();
    if(isset($_SESSION['temp_email'])){
        $where = array('email'=>$_SESSION['temp_email']);
    } else {
        throw new SysException("I0023", '', '');
    }
    
    $result = $db->findRow("user",$where);


    // 如果 用户表中已经有该用户信息，则抛出异常
    if(isset($result)){
        throw new SysException("I0092", '', array('氏名（フリガナ）'));
    }


    $result = $db->findRow("user_temp",$where);

    $expire_time = CommonTool::getExpireTime('+1 hours');
    $create_time = CommonTool::getDateTimeWithMillisecond();

    if(isset($result)){  //已经存在，进行更新操作
        $data = array();
        $data['password'] = $_SESSION['temp_password'];
        $data['name_prifix'] = $_SESSION['temp_name_prifix'];
        $data['name_suffix'] = $_SESSION['temp_name_suffix'];
        $data['notation_prifix'] = $_SESSION['temp_notation_prifix'];
        $data['notation_sufix'] = $_SESSION['temp_notation_sufix'];
        $data['expire_time'] = $expire_time;
        $data['create_time'] = $create_time;
        $where = array('id' => $result['id']);
        $status = $db->update('user_temp',$data,$where);
        if($status < 0){
            //原因不明エラー
            throw new SysException("S0001", '', '');
        }
        $id = $where['id'];
    } else {    // 不存在，插入操作
        $result = $db->findRow('user_temp','',' max(id) as max_id ');
        $id = $result['max_id'] + 1;
        $sql = "INSERT INTO user_temp (id,email,password,name_prifix,name_suffix,notation_prifix,notation_sufix,code,expire_time,create_time) VALUES (
           '" . $id . "',
            '" . $_SESSION['temp_email'] . "',
            '" . $_SESSION['temp_password'] . "',
             '" . $_SESSION['temp_name_prifix'] . "',
              '" . $_SESSION['temp_name_suffix'] . "',
                  '" . $_SESSION['temp_notation_prifix'] . "',
                  '" . $_SESSION['temp_notation_sufix'] . "',
                  '',
                  '" .  $expire_time . "',
                  '" .  $create_time . "'
                  )";
        $db->query($sql);
    }


   // header("Location: /webapps/sign/signupcheck.php");

    $url = 'https://'.$_SERVER['HTTP_HOST'].'/webapps/sign/signuplogin.php?id='.$id;

//    $body = '<label>'.  $_SESSION['temp_name_prifix'] . $_SESSION['temp_name_suffix'] .'様</label><br>'
//        . ' <label>まだ登録は完了していません。</label><br>'
//        . '<a href="' .$url . '">・仮登録メールの新規登録用ログイン画面のＵＲＬからログインし、本登録を完了してください</a><br>'
//        . '1時間以内に本登録してください。';
    $body = '<label>'.  $_SESSION['temp_name_prifix'] . $_SESSION['temp_name_suffix'] .'様</label><br><br>'
        . ' <label>ヘパリーゼW×CAMP HACK　厳選キャンプグッズプレゼントキャンペーンに仮登録頂きまして</label><br>'
        . ' <label>ありがとうございます。</label><br><br>'
        . ' <label>まだ登録は完了していません。</label><br><br>'
        . ' <label>下記のＵＲＬにアクセスし、ログインいただいたうえ、本登録を完了してください。</label><br>'
        . ' <label>▼本登録用ページへアクセス</label><br>'
        . ' <a href="' .$url . '">"'.$url.'"</a><br><br>'
        . ' <label>※1時間以内に本登録していただかないと、リンクが無効となります。</label><br>'    
        . ' <label>※このメールに心当たりがございませんでしたら、お手数をおかけしますが、削除していただきますようお願いいたします。</label><br>'
        . ' <label>※このメールの送信アドレスは送信専用となりますので、返信できません。</label><br>'
        . ' <br><br><label>-----------------------------------------------------------------------------------------------------</label><br>'
        . ' <label>お問い合わせ</label><br>'
        . ' <label>ヘパリーゼキャンプグッズプレゼントキャンペーン事務局</label><br>'
        . ' <label>TEL：0120-785-173 </label><br>'
        . ' <label>受付時間：10:00～17:00　※2020年11月11日（水）のみ19:00まで</label><br>'
        . ' <label>受付期間：2020年12月18日(金)まで</label><br>'
        . ' <label>※上記の受付時間外は、inquiry@hepa-camphack-campaign.comでも</label><br>'
        . ' <label>　お問い合わせを承っております。</label><br>'
        . ' <label>-----------------------------------------------------------------------------------------------------</label>';
    
//    $body = "".$_SESSION['temp_name_prifix']."".$_SESSION['temp_name_suffix']."様
//
//ヘパリーゼW×CAMP HACK　厳選キャンプグッズプレゼントキャンペーンに仮登録頂きまして
//ありがとうございます。
//
//まだ登録は完了していません。
//
//下記のＵＲＬにアクセスし、ログインいただいたうえ、本登録を完了してください。
//▼本登録用ページへアクセス
//$url
//
//※1時間以内に本登録していただかないと、リンクが無効となります。
//※このメールに心当たりがございませんでしたら、お手数をおかけしますが、削除していただきますようお願いいたします。
//※このメールの送信アドレスは送信専用となりますので、返信できません。
//
//
//-----------------------------------------------------------------------------------------------------
//お問い合わせ
//ヘパリーゼキャンプグッズプレゼントキャンペーン事務局
//TEL：0120-785-173
//受付時間：10:00～17:00　※2020年11月11日（水）のみ19:00まで
//受付期間：2020年12月18日(金)まで
//※上記の受付時間外は、inquiry@hepa-camphack-campaign.comでも
//お問い合わせを承っております。
//----------------------------------------------------------------------------------------------------- ";
                    
    $status = $mailer->sendOne( $_SESSION['temp_email'] ,$body,'まだ仮登録です','SingupCheck.php');
    
//    //-----MCS追加(2020/10/12) 削除予定----///
//    $before = date('Y-m-d H:i:s', $_SESSION['beforeTime'][0]) . '.' .$_SESSION['beforeTime'][1];
//    $after = date('Y-m-d H:i:s', $_SESSION['afterTime'][0]) . '.' .$_SESSION['afterTime'][1];
//    $filename = 'C:\Apache24\htdocs\hepa-camphack\logs\php_time.log';
//    $outputString = $before . "\n" .$after;
//    //中身をファイルに書き出します。
//    file_put_contents($filename, $outputString, FILE_APPEND | LOCK_EX);
//    //-----MCS追加(2020/10/12) 削除予定----///
    
    $response = array();
    // 清空 SESSION 中保存的临时用户信息
    if($status){
        $_SESSION['temp_email'] = '';
        $_SESSION['temp_password'] = '';
        $_SESSION['temp_name_prifix'] = '';
        $_SESSION['temp_name_suffix'] = '';
        $_SESSION['temp_notation_prifix'] = '';
        $_SESSION['temp_notation_sufix'] = '';
        $response['status'] = 1;
        $response['message'] = "";
    } else {
        $response['status'] = 0;
        $response['message'] = MessageTool::getMessage("I0102");
        $db->endTransaction(false);
    }
    $db->endTransaction(true);
    echo json_encode ($response);

}catch (SysException $e) {
    //カストマイズException
    $db->endTransaction(false);
    $errCode = $e->getErrCode();
    $messageStr = MessageTool::getMessageWithReplaceText($errCode, $e->getReplaceTexts());
    $response['status'] = 0;
    $response['message'] = $messageStr;
    echo json_encode ($response);
    exit;
} catch (Exception $e) {
    $db->endTransaction(false);
    $ExceptionMessage = $e->getMessage();
    if ($ExceptionMessage == "pg_error"){
        //PostgreSQLの問題
        $messageStr = MessageTool::getMessage("S0002");
    }else{
        //原因不明エラー
        $messageStr = MessageTool::getMessage("S0001");
    }
    $response = array();
    $response['status'] = 0;
    $response['message'] = $messageStr;
    echo json_encode ($response);
    exit;
}

