<?php
/**
 * Created by PhpStorm.
 * User: 84770
 * Date: 2020/9/8
 * Time: 16:13
 */

require_once 'src/PHPMailer.php';
require_once 'src/SMTP.php';
require_once 'src/OAuth.php';
require_once 'src/Exception.php';


class Mailer
{

  private $SMTPDebug;

  private $isSMTP;

  private $SMTPAuth;

  private $host;

  private $SMTPSecure;

  private $Port;

  private $CharSet;

  private $FromName;

  private $Username;

  private $Password;

  // 发件人邮箱地址
  private $From;

  private static $mail;

  private $errors_log;


    //私有的构造方法
    private function __construct($SMTPDebug,$isSMTP,$SMTPAuth,$SMTPSecure,$host,$Port,$Username,$Password,$From,$CharSet,$FromName){
        $this-> SMTPDebug = $SMTPDebug;
        $this-> isSMTP = $isSMTP;
        $this-> SMTPAuth = $SMTPAuth;
        $this-> SMTPSecure = $SMTPSecure;
        $this-> host =  $host;
        $this-> Port =  $Port;
        $this-> Username =  $Username;
        $this-> Password =  $Password;
        $this-> From = $From;
        $this-> CharSet = $CharSet;
        $this-> FromName = $FromName;
    }

    /**
     *  发送 HTML
     * @param $body
     */
    public function sendOne($address,$body,$subject){
        $this->initMail();
        self::$mail->isHTML(true);
        self::$mail->addAddress($address);
        self::$mail->Subject = $subject;
        self::$mail->Body = $body;
        //送信直前の時間を計測
        $beforeTime = explode('.',microtime(true));
        $_SESSION['beforeTime'] = $beforeTime;
        $status = self::$mail->send();
        //送信直後の時間を計測
        $aftertime = explode('.',microtime(true));
        $_SESSION['afterTime'] = $aftertime;
        if(!$status){
            error_log(self::$mail ->ErrorInfo . "\n",3,$GLOBALS['error_log_path']);
        }
        return $status;
    }

    private function initMail(){
        self::$mail = new PHPMailer\PHPMailer\PHPMailer();
        self::$mail->SMTPDebug = $this-> SMTPDebug ;
        self::$mail->isSMTP();
        self::$mail->SMTPAuth = $this->SMTPAuth;
        self::$mail->Host = $this->host;
        self::$mail->SMTPSecure = $this->SMTPSecure;
        self::$mail->Port = $this->Port;
        self::$mail->CharSet = $this->CharSet;
        self::$mail->FromName = $this->FromName;
        self::$mail->Username = $this->Username;
        self::$mail->Password = $this->Password;
        self::$mail->From = $this->From;
    }

    //公用的静态方法
    public static function getIntance($SMTPDebug,$isSMTP,$SMTPAuth,$SMTPSecure,$host,$Port,$Username,$Password,$From,$CharSet,$FromName){
        if(self::$mail==false){
            self::$mail=new self($SMTPDebug,$isSMTP,$SMTPAuth,$SMTPSecure,$host,$Port,$Username,$Password,$From,$CharSet,$FromName);
        }
        return self::$mail;
    }

}