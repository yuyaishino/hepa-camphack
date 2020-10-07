<?php

/*変換ルール
メンバーコード(6桁)の【十万の位、十の位、万の位、一の位、x、千の位、百の位、y】の順番の数値文字列
x：(10-(一の位、百の位、万の位の合計×3+十の位、千の位、十万の位の合計)の一の位)の一の位
y：(10-(一の位、十の位、百の位の合計×7+千の位、万の位、十万の位の合計×3)の一の位)の一の位
例：メンバーコードが【123456】の場合
x：substr(10-substr((6+4+2)*3+(5+3+1),-1),-1)=5
y：substr(10-substr((4+5+6)*7+(1+2+3)*3,-1),-1)=7
*/

	function get_ssID($menber_code){     //メンバーコードからssIDを取得
		//メンバーコードを6桁の文字列にする
		$menber_code = str_pad($menber_code, 6, "0", STR_PAD_LEFT);
		//メンバーコードをそれぞれの位に分ける
		$cry[1] = substr($menber_code, 0, 1);     //十万の位
		$cry[2] = substr($menber_code, 1, 1);     //万の位
		$cry[3] = substr($menber_code, 2, 1);     //千の位
		$cry[4] = substr($menber_code, 3, 1);     //百の位
		$cry[5] = substr($menber_code, 4, 1);     //十の位
		$cry[6] = substr($menber_code, 5, 1);     //一の位
		//x,yを計算する
		$x = substr(10 - substr(($cry[6] + $cry[4] + $cry[2]) * 3 + ($cry[5] + $cry[3] + $cry[1]), -1), -1);
		$y = substr(10 - substr(($cry[4] + $cry[5] + $cry[6]) * 7 + ($cry[1] + $cry[2] + $cry[3]) * 3, -1), -1);
		//ssIDを返す
		$ssID = $cry[1] . $cry[5] . $cry[2] . $cry[6] . $x . $cry[4] . $cry[3] . $y;
		return $ssID;
	}
	
	function get_menber_code($ssID){     //ssIDからメンバーコードを取得
		//ssIDをそれぞれの位に分ける
		$cry[1] = substr($ssID, 0, 1);     //千万の位(十万の位)
		$cry[2] = substr($ssID, 1, 1);     //百万の位(十の位)
		$cry[3] = substr($ssID, 2, 1);     //十万の位(万の位)
		$cry[4] = substr($ssID, 3, 1);     //万の位(一の位)
		$cry[5] = substr($ssID, 4, 1);     //千の位(x)
		$cry[6] = substr($ssID, 5, 1);     //百の位(千の位)
		$cry[7] = substr($ssID, 6, 1);     //十の位(百の位)
		$cry[8] = substr($ssID, 7, 1);     //一の位(y)
		//チェックデジットがあっているか確認
		if($cry[5] == substr(10 - substr(($cry[4] + $cry[6] + $cry[3]) * 3 + ($cry[2] + $cry[7] + $cry[1]), -1), -1) and 
		   $cry[8] == substr(10 - substr(($cry[6] + $cry[2] + $cry[4]) * 7 + ($cry[1] + $cry[3] + $cry[7]) * 3, -1), -1)){
			//メンバーコードを返す
			$menber_code = intval($cry[1] . $cry[3] . $cry[7] . $cry[6] . $cry[2] . $cry[4]);
			return $menber_code;
		}else{
			//不正アクセス処理
			return "unknown_user";
		}
	}
	
	function create_uuid($prefix = ""){
		$str = md5(uniqid(mt_rand(), true));
		$uuid  = substr($str,0,8) . '-';
		$uuid .= substr($str,8,4) . '-';
		$uuid .= substr($str,12,4) . '-';
		$uuid .= substr($str,16,4) . '-';
		$uuid .= substr($str,20,12);
		return $prefix . $uuid;
	}
	
?>