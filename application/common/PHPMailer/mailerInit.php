<?php
/**
 * Created by PhpStorm.
 * User: 84770
 * Date: 2020/9/8
 * Time: 16:42
 */

$SMTPDebug = 0;
$isSMTP = true;
$SMTPAuth = true;
$SMTPSecure = 'ssl';
$host =  'smtp.gmail.com';
$Port =  '465';
$Username =  'info@hepa-camphack-campaign.com';
$Password =  's58j3a2k';
$From = 'info@hepa-camphack-campaign.com';
$CharSet = 'utf-8';
$FromName = 'ヘパリーゼ・キャンペーン事務局';



require_once 'Mailer.class.php';
$mailer = Mailer::getIntance($SMTPDebug,$isSMTP,$SMTPAuth,$SMTPSecure,$host,$Port,$Username,$Password,$From,$CharSet,$FromName);
