<?php
/*
	写一个程序（shell、php均可），监控一个web访问日志文件，如果最近5分钟内某IP地址访问同一URL超过100次，则通过iptables将该IP地址设为禁止访问。
	
	以下一行是Nginx的一行日志格式，以此作判断:
	
	2015/07/15 16:11:09 [error] 25138#0: *1255466 access forbidden by rule, client: 113.105.95.122, server: www.test.com, request: "GET /lapuda/login.php?dopost=showad HTTP/1.1", host: "www.test.com"
*/
	$fp = fopen('./log.log', 'r');
	$ip_count = Array();  //每个元素以md5(ip地址 + 访问地址)为下标，存储Array(ip地址，访问次数)
	while(!feof($fp)){
		$line = fgets($fp);
		$line = trim($line);
		if(stripos($line, 'request:')){
			preg_match('/\d{4}\/\d{1,2}\/\d{1,2}\s(\d{1,2}:){2}\d{2}/', $line, $matches);
			$time = $matches[0]; //访问时间
			
			if(strtotime($time) > time()-300){ //在5分钟内
				preg_match('/(\d{1,3}\.){3}\d{1,3}/', $line, $matches); //获取IP地址
				$ip = $matches[0];
				preg_match('/(request:\s"GET\s)\S*/', $line, $matches); //获取访问的URL
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
	
	uasort($ip_count, "mySort"); //按访问次数降序排列
	//print_r($ip_count);
	function mySort($a, $b){
		return $a[1]>$b[1] ? 0 : 1;
	}
	
	foreach($ip_count AS $arr){
		if($arr[1] >= 100){
			exec("iptables –t filter –A OUTPUT –p tcp –d {$arr[0]} –dport 80 –j DROP", $rs);
			exec("service iptables restart", $rs);
			echo("{$arr[0]} 被禁止，");
		}else{
			break;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	