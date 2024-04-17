<?php
/* utf-8��gb�ַ��������ģ�� */
function is_utf8($mp_str1){//�ж��ַ����Ƿ���utf-8����
	if(preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/", $mp_str1) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/", $mp_str1) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/", $mp_str1) == true){
		return true;//��UTF8
	}else{
		return false;//��UTF8
	}
}
function utf8_to_gbk($mp_str1){
	return is_utf8($mp_str1) ? iconv('utf-8', 'gbk//ignore', $mp_str1) : $mp_str1; // //ignore����Բ���ת�����ַ�
}
function utf8_to_gb2312($mp_str1){
	return is_utf8($mp_str1) ? iconv('utf-8', 'gb2312//ignore', $mp_str1) : $mp_str1;
}
/* **** */
function unescape($str){//��js��escape()���������룬ʹ�øú���������utf8_to_gbk()���л�ԭ����ֹUTF8����
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