<?php
/* utf-8和gb字符互相操作模块 */
function is_utf8($mp_str1){//判断字符串是否是utf-8编码
	if(preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/", $mp_str1) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/", $mp_str1) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/", $mp_str1) == true){
		return true;//是UTF8
	}else{
		return false;//非UTF8
	}
}
function utf8_to_gbk($mp_str1){
	return is_utf8($mp_str1) ? iconv('utf-8', 'gbk//ignore', $mp_str1) : $mp_str1; // //ignore会忽略不能转换的字符
}
function utf8_to_gb2312($mp_str1){
	return is_utf8($mp_str1) ? iconv('utf-8', 'gb2312//ignore', $mp_str1) : $mp_str1;
}
/* **** */
function unescape($str){//将js的escape()函数反解码，使用该函数后再用utf8_to_gbk()进行还原，防止UTF8乱码
	$str = rawurldecode($str);
	preg_match_all("/%u.{4}|&#x.{4};|&#d+;|.+/U", $str, $r);
	$ar = $r[0];
	foreach($ar as $k=>$v){
		if(substr($v,0,2) == "%u"){
			$ar[$k] = iconv("UCS-2", "GBK", pack("H4", substr($v,-4)));
		}else if(substr($v,0,3) == "&#x"){
			$ar[$k] = iconv("UCS-2", "GBK", pack("H4", substr($v,3,-1)));
		}else if(substr($v,0,2) == "&#"){
			$ar[$k] = iconv("UCS-2", "GBK", pack("n", substr($v,2,-1)));
		}
	}
	return join("", $ar);
}