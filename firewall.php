<?php
/*
	дһ������shell��php���ɣ������һ��web������־�ļ���������5������ĳIP��ַ����ͬһURL����100�Σ���ͨ��iptables����IP��ַ��Ϊ��ֹ���ʡ�
	
	����һ����Nginx��һ����־��ʽ���Դ����ж�:
	
	2015/07/15 16:11:09 [error] 25138#0: *1255466 access forbidden by rule, client: 113.105.95.122, server: www.test.com, request: "GET /lapuda/login.php?dopost=showad HTTP/1.1", host: "www.test.com"
*/
	$fp = fopen('./log.log', 'r');
	$ip_count = Array();  //ÿ��Ԫ����md5(ip��ַ + ���ʵ�ַ)Ϊ�±꣬�洢Array(ip��ַ�����ʴ���)
	while(!feof($fp)){
		$line = fgets($fp);
		$line = trim($line);
		if(stripos($line, 'request:')){
			preg_match('/\d{4}\/\d{1,2}\/\d{1,2}\s(\d{1,2}:){2}\d{2}/', $line, $matches);
			$time = $matches[0]; //����ʱ��
			
			if(strtotime($time) > time()-300){ //��5������
				preg_match('/(\d{1,3}\.){3}\d{1,3}/', $line, $matches); //��ȡIP��ַ
				$ip = $matches[0];
				preg_match('/(request:\s"GET\s)\S*/', $line, $matches); //��ȡ���ʵ�URL
				$url = $matches[0];
				if(!array_key_exists(md5($ip.$url), $ip_count)){
					$ip_count[md5($ip.$url)] = Array($ip, 1);
				}else{
					$ip_count[md5($ip.$url)][1]++;
				}
			}
		}
	}
	fclose($fp);
	
	uasort($ip_count, "mySort"); //�����ʴ�����������
	//print_r($ip_count);
	function mySort($a, $b){
		return $a[1]>$b[1] ? 0 : 1;
	}
	
	foreach($ip_count AS $arr){
		if($arr[1] >= 100){
			exec("iptables �Ct filter �CA OUTPUT �Cp tcp �Cd {$arr[0]} �Cdport 80 �Cj DROP", $rs);
			exec("service iptables restart", $rs);
			echo("{$arr[0]} ����ֹ��");
		}else{
			break;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	