<?php
/**
 * フォントサイズ調整
 * 
 * @param unknown $pdf
 * @param unknown $PrintStr　　　　　印刷文字
 * @param unknown $Current_Font　　現在FontSize
 * @param unknown $Width　　　　　　　印刷幅
 */
function Adjust_FontSize(&$pdf, $PrintStr, $Current_Font, $Width){
    $pdf->SetFontSize($Current_Font);
	$w = $pdf->GetStringWidth($PrintStr);
	while($w >= $Width){
		$Current_Font -= 0.2;
		$pdf->SetFontSize($Current_Font);
		$w = $pdf->GetStringWidth($PrintStr);
	}
	$Current_Font -= 0.3;
	$pdf->SetFontSize($Current_Font);
}

/**
 * 印刷用紙を取得する
 * @param string $paper_type 紙種類
 * @param char   $muki　　　　　縦横向き
 * @return $paper
 */
function Get_Paper($paper_type, $muki){
	switch($paper_type){
		case 'A4':
			$max_width = 297;
			$min_width = 210;
			break;
		case 'B4':
			$max_width = 364;
			$min_width = 257;
			break;
		case 'A3':
			$max_width = 420;
			$min_width = 297;
			break;
		case 'A5':
			$max_width = 148;
			$min_width = 210;
			break;
		case '10x5inch':
			$max_width = 254;
			$min_width = 127;
			break;
		case '11x5inch':
			$max_width = 279.4;
			$min_width = 127.0;
			break;
	}
	
	switch($muki){
		case 'P':
			$paper['height'] = $max_width;
			$paper['width'] = $min_width;
			break;
		case 'L':
			$paper['height'] = $min_width;
			$paper['width'] = $max_width;
			break;
	}
	return $paper;
}

/**
 * SJIS-WIN⇒UTF-8文字エンコーディングを変換する
 * @param string $printStr
 * @return string
 */
function str_jp_utf8_encoding($printStr){
	return mb_convert_encoding($printStr, 'SJIS-WIN', 'UTF-8');
}

/**
 * 文字を縦で出力すす
 * 
 * @param object $pdf
 * @param number $w
 * @param number $h
 * @param string/array $txt
 * @param number $border
 * @param string $align
 * @param number $fill
 */
function printVerticalCell($pdf ,$w, $h = 0, $txt="", $border = 0, $align = '', $fill = 0,$defFontSize=8){
	
	$txtArr = array();
	if (is_string($txt)) {
		$txtArr = preg_split('/(?<!^)(?!$)/u', $txt);
	}elseif (is_array($txt)) {
		$txtArr = $txt;
	}else{
		$pdf->Cell($w, $h, "", $border, 0, $align, $fill);
		return;
	}
	$txtArrCnt =count($txtArr);
	if($txtArrCnt == 0){
		$pdf->Cell($w, $h, "", $border, 0, $align, $fill);
		return;
	}elseif ($txtArrCnt == 1){
		$printStr = str_jp_utf8_encoding($txtArr[0]);
		Adjust_FontSize($pdf, $printStr, $defFontSize, $w);
		$pdf->Cell($w, $h, $printStr, $border, 0, $align, $fill);
	}else{
		$lineHeight = $h/$txtArrCnt;
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		$newY = $y;
		for($index=0;$index<$txtArrCnt;$index++)
		{
			$pdf->SetX($x);
			$printStr = str_jp_utf8_encoding($txtArr[$index]);
			Adjust_FontSize($pdf, $printStr, $defFontSize, $w);
			if($border>0){
				if($index == 0){
					$pdf->Cell($w, $lineHeight, $printStr, "LTR", 0, $align, $fill);
				}elseif($index == $txtArrCnt-1){
					$pdf->Cell($w, $lineHeight, $printStr, "LBR", 0, $align, $fill);
				}else{
					$pdf->Cell($w, $lineHeight, $printStr, "LR", 0, $align, $fill);
				}
			}else {
				$pdf->Cell($w, $lineHeight, $printStr, 0, 0, $align, $fill);
			}
			$pdf->ln();
			$newY += $lineHeight;
			$pdf->SetY($newY);
		}
		$pdf->SetY($y);
		$pdf->SetX($x+$w);
	}
}

function printHeader($printStr,$pdf,$Width=20,$autoFontSize=false,$height=5,$changRow=false){
	if(!isset($printStr)){
		$printStr = "";
	}
	$pdf->SetFont(GOTHIC, '' ,10);
	if($autoFontSize){
		$printStr = mb_convert_encoding($printStr, 'SJIS-WIN', 'UTF-8');
		Adjust_FontSize($pdf, $printStr, 10, $Width);
		$pdf->Cell($Width, $height, $printStr, 1, 0, 'C', 1);
	}elseif ($changRow){
		if(strpos($printStr,"|")){
			$x = $pdf->getx();
			$y = $pdf->gety();
			$pdf->Cell($Width, $height, "", 1, 0, 'C', 1);
			$fieldList = explode('|',$printStr);
			$rowCnt = count($fieldList);
// 			if($rowCnt>2){
// 				$wordheight = $height/$rowCnt;
// 				echo $wordheight;
// 				exit;
// 			}
			$wordheight = ($height-0.4)/$rowCnt;
			$printStr = mb_convert_encoding($fieldList[0], 'SJIS-WIN', 'UTF-8');
			if(bccomp($Width, $height)>-1){
				Adjust_FontSize($pdf, $printStr, 10, $Width);
			}else{
				Adjust_FontSize($pdf, $printStr, 10, $wordheight);
			}
			$wordRowY = $y+0.2;
			foreach($fieldList as $field){
				$pdf->setxy($x,$wordRowY);
				$printStr = mb_convert_encoding($field, 'SJIS-WIN', 'UTF-8');
				$pdf->Cell($Width, $wordheight, $printStr, 0, 0, 'C', 0);
				$wordRowY += $wordheight;
			}
			$pdf->setxy($x+$Width,$y);
		}else{
			$printStr = mb_convert_encoding($printStr, 'SJIS-WIN', 'UTF-8');
            Adjust_FontSize($pdf, $printStr, 10, $Width);
			$pdf->Cell($Width, $height, $printStr, 1, 0, 'C', 1);
		}
	}else{
		$printStr = mb_convert_encoding($printStr, 'SJIS-WIN', 'UTF-8');
        Adjust_FontSize($pdf, $printStr, 10, $Width);
		$pdf->Cell($Width, $height, $printStr, 1, 0, 'C', 1);
	}
	$pdf->SetFont(GOTHIC, '' ,10);
}

function formatDate($dateStr,$dateFormat="Y/m/d"){
	if($dateStr){
		$dateFormat = str_replace("-", "/", $dateFormat);
		$date=date($dateFormat,strtotime($dateStr));
		return $date."";
	}
	return "";
}
?>