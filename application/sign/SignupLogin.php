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
//require_once ("../../application/common/comUtils/SessionCheck.php");
require_once("../common/message/MessageTool.php");
require_once("../common/Exception/SysException.php");
require_once("../common/comUtils/CommonTool.class.php");
require_once '../common/db/dbini.php';
require_once '../common/db/WorkLogTool.php';
require_once '../common/PHPMailer/mailerInit.php';
require_once("../common/comUtils/unknown_access.php");
require_once("../../application/common/db/sessioninit.php");

try {



    // 验证
    $email = $_POST['email'];
    if (!isset($email) || CommonTool::isNullOREmpty($email)) {
        throw new SysException("I0001", '', array('ID（メールアドレス）'));
    }

    $sql = "SELECT email FROM user WHERE email = '" . mysqli_escape_string($db->getLink(),$email) . "' AND delete_flag = 0";
    $result = $db->getAll($sql);
    if (count($result) > 0) {
        throw new SysException("I0075", '', array('このID'));
    }

    $password = $_POST['password'];
    if (!isset($password) || CommonTool::isNullOREmpty($password)) {
        throw new SysException("I0001", '', array('パスワード'));
    }

    if (!isset($_POST['id'])) {
        throw new SysException("I0001", '', array('id'));
    }

    // 验证ID 存在性
    $sql = "SELECT * FROM user_temp WHERE id = '" . mysqli_escape_string($db->getLink(),$_POST['id']) . "'";
    $result = $db->getAll($sql);
    if (count($result) <= 0) {
        //id 不存在
        throw new SysException("I0001", '', array('IDまたはパスワードが正しくありません'));
    }

    // 1.通过id 取出 temp 表中的数据，并验证是否过期
    if( CommonTool::getDateTimeWithMillisecond() > $result[0]['expire_time'] ){
        // 如果过期,则给出过期错误信息
        throw new SysException("I0001", '', array('已经过期'));
    }

    // 没有过期，验证用户输入的用户名密码和 临时表中的用户名和密码是否一致
    if($_POST['email'] != $result[0]['email'] || $_POST['password'] != $result[0]['password']){
        throw new SysException("I0001", '', array('IDまたはパスワードが正しくありません。'));
    }


    $db->startTransaction();
    // 如果一致,则把临时表中的数据保存到 真实用户信息表中
    $maxResult =  $db->findRow('user','',' max(id) maxId ');
    $id =$maxResult['maxId'] + 1;
    $currentTime = CommonTool::getDateTimeWithMillisecond();
    $data = array();
    $data['id'] = $id;
    $data['email'] = $email;
    $data['password'] = $password;
    $data['name_prifix'] = $result[0]['name_prifix'];
    $data['name_suffix'] = $result[0]['name_suffix'];
    $data['notation_prifix'] = $result[0]['notation_prifix'];
    $data['notation_sufix'] = $result[0]['notation_sufix'];
    $data['delete_flag'] = 0;
    $data['create_time'] = $currentTime;
    $data['create_by'] = $id;
    $data['update_time'] = $currentTime;
    $data['update_by'] = $id;

    $db->insert('user',$data);


    // 删除临时表中已经过期的信息。
    $where = array();
    $where['id'] = $result[0]['id'];
    $db->deleteOne('user_temp',$where);
    $db->commit();

    // 返回成功信息.

    if(!isset($_SESSION['ssID'])){
        $_SESSION['ssID'] = null;
    }
    $_SESSION['usr_id'] = $id;
    //$_SESSION['usr_nm'] = $mdm_usr[0]->usr_nm;
    $_SESSION['mail'] = $email;
    $_SESSION['ssID'] = get_ssID($id);
    //$_SESSION['common_base_cd'] = $mdm_usr[0]->base_cd;
    $_SESSION['name_prifix'] = $result[0]['name_prifix'];
    $_SESSION['name_suffix'] = $result[0]['name_suffix'];

    if(isset($_SESSION['no_error_count'])){
        unset($_SESSION['no_error_count']);
    }

    if(isset($_SESSION['code_error_count'])){
        unset($_SESSION['code_error_count']);
    }

    $_SESSION['SESSION_CSRF_MIDDLE_TOKEN'] = create_uuid();
    if(isset($_SESSION['loginURL'])){
        $go_page = $_SESSION['loginURL'];
    }else{
        $go_page = "index.php";        //以前
    }
    unset($_SESSION['loginURL']);

    $response = array();
    $response['status'] = 1;
    $response['message'] = "";
    echo json_encode($response);

} catch (SysException $e) {
    //カストマイズException
    $db->rollback();
    $errCode = $e->getErrCode();
    $messageStr = MessageTool::getMessageWithReplaceText($errCode, $e->getReplaceTexts());
    $response['status'] = 0;
    $response['message'] = $messageStr;
    echo json_encode($response);
    exit;
} catch (Exception $e) {
    //$db->endTransaction(false);
    $ExceptionMessage = $e->getMessage();
    if ($ExceptionMessage == "pg_error") {
        //PostgreSQLの問題
        $messageStr = MessageTool::getMessage("S0002");
    } else {
        //原因不明エラー
        $messageStr = MessageTool::getMessage("S0001");
    }
    $response = array();
    $response['status'] = 0;
    $response['message'] = $messageStr;
    echo json_encode($response);
    exit;
}

