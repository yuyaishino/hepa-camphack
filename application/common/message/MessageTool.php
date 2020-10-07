<?php
/**
 * メッセージの共通ツール
 *
 * Date:     2018/03/06
 * Author:   xuweiwei
 *
 **/

require_once("MessageDef.php");

class MessageTool{
	
	/**
	 * メッセージIDよりメッセージを取得する
	 * 
	 * @param string $strMessageCode メッセージID
	 * @return string メッセージ
	 */
	public static function getMessage($strMessageCode){
		$strMessage = "";
		if(!empty($strMessageCode)){
			$MessageDefinitionArray = getMessageDefinitionArray();
			$strMessage = $MessageDefinitionArray[$strMessageCode];
		}
		return $strMessage;
	}
	
	/**
	 * メッセージを取得する
	 * 
	 * @param string $strMessageCode　　メッセージID
	 * @param string $strReplaceCodes　切替メッセージID
	 * @return string　メッセージ
	 */
	public static function getMessageWithReplaceCode($strMessageCode,$strReplaceCodes){
		$strMessage = MessageTool::getMessage($strMessageCode);
		if(!empty($strMessage)){
			if (is_array($strReplaceCodes)){
				foreach ($strReplaceCodes as $strReplaceCode){
					$strReplace = MessageTool::getMessage($strReplaceCode);
					if(!empty($strReplace)){
						$strMessage = preg_replace("/###/",$strReplace,$strMessage,1);
					}
				}
			}else if(!empty($strReplaceCodes)){
				$strReplace = MessageTool::getMessage($strReplaceCodes);
				if(!empty($strReplace)){
					$strMessage = str_replace("###",$strReplace,$strMessage);
				}
			}
		}
		return $strMessage;
	}
	
	/**
	 * メッセージを取得する
	 * 
	 * @param string $strMessageCode　　メッセージID
	 * @param string $strReplaceTexts　切替メッセージ
	 * @return string　メッセージ
	 */
	public static function getMessageWithReplaceText($strMessageCode,$strReplaceTexts=''){
		
		$strMessage = MessageTool::getMessage($strMessageCode);
		if(!empty($strMessage)){
			if (is_array($strReplaceTexts)){
				foreach ($strReplaceTexts as $strReplaceText){
					if(!empty($strReplaceText)){
						$strMessage = preg_replace("/###/",$strReplaceText,$strMessage,1);
					}
				}
			}else if(!empty($strReplaceTexts)){
				$strMessage = str_replace("###",$strReplaceTexts,$strMessage);
			}
		}
		return $strMessage;
	}
}