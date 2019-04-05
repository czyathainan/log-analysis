<?php
	set_time_limit(0);
	include(dirname(__FILE__).'/function.inc.php');
	include(dirname(__FILE__).'/rule.cache.php');
	
	list($mico_second, $second) = explode(' ', microtime());
	$start_time = $mico_second + $second;
	
	$get_filename = isset($_GET['filename']) ? $_GET['filename'] : '';
?>
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>统计C ip段的访问次数</title>
</head>
<style type="text/css">
	body{}
	form{margin:0;}
	.left{float:left;}
	.right{float:right;}
	.clear{clear:both;}
	*{font-size:12px; line-height:1.5;}
	.result p{margin:6px 0; line-height:1.0;}
	.result p span.red{background:#ff0000; color:#ffffff; padding:0 3px;}
	.result u{background:#b3a994; text-decoration:none;}
	.result em{background:#000000; color:#ffffff; font-style:normal;}
	textarea{border:1px solid #cecece; margin-right:15px;}
	.redBG_white{background:#ff0000;color:#ffffff;padding:0 5px;}
	.toolBar{border:1px solid #cecece; position:fixed; background:#ffffff; width:95%; padding:5px 10px 10px; min-width:1000px; border-bottom-width:2px;}
	
	.result u{background:#d4d0c8; text-decoration:none;}
	textarea{border:1px solid #cecece; margin-right:15px; line-height:1.2;}
</style>
<body>
<div class="result">
<?php
	if(empty($get_filename)){
		echo('<p>未选择文件！</p>');
	}else{
		if(!file_exists(dirname(__FILE__).'/log/'.$get_filename)){
			echo('<p style="color:#ff0000;">日志文件'.$get_filename.'未找到，请检查！</p>');
		}else{
			$fp 			= fopen(dirname(__FILE__).'/in_wd.txt', 'r');//匹配词
			$in_wd			= [];
			$ip_net_data	= [];
			while(!feof($fp)){
				$s1 = fgets($fp);
				$s1 = trim($s1);
				if(empty($s1) || substr($s1, 0, 1)=='#'){
					continue;
				}
				$in_wd[] = $s1;
			}
			$in_wd_length = count($in_wd);
			fclose($fp);
			
			$fp = fopen(dirname(__FILE__).'/over_wd.txt', 'r');//排除词
			$over_wd = Array();
			while(!feof($fp)){
				$s2 = fgets($fp);
				$s2 = trim($s2);
				if(empty($s2) || substr($s2, 0, 1)=='#'){
					continue;
				}
				$over_wd[] = $s2;
			}
			$over_wd_length = count($over_wd);
			fclose($fp);
			
			$fp = fopen(dirname(__FILE__).'/log/'.$get_filename, 'r');
			$c = 0;             //被匹配到的行数
			$all_line = 0;      //总行数
			while(!FEOF($fp)){
				$sl = fgets($fp);
				$sl = trim($sl);
				$sl = urldecode($sl);
				$sl = str_ireplace('<', '&lt;', $sl);
				$sl = str_ireplace('>', '&gt;', $sl);
				
				if($sl){
					$all_line++;
				}else{
					continue;
				}
				
				if($all_line % 10000 == 0){
					flush();
					ob_flush();
					sleep(1);
				}
				
				$sl_pass = 1; //当前行的记录是否符合筛选规则，1：是，0否
				
				if($in_wd_length && $cfg_rule_cache['wd_priority']=='in_wd') //查找优先
				{
					$sl_pass = 0;
					foreach($in_wd AS $key){
						if(substr_count($sl, $key)){
							$sl_pass = 1;
							break;
						}
					}
				}
				else if($over_wd_length && $cfg_rule_cache['wd_priority']=='over_wd') //排除优先
				{
					foreach($over_wd AS $key){
						if(substr_count($sl, $key)){
							$sl_pass = 0;
							break;
						}
					}
				}
				
				if(!$sl_pass){
					continue;
				}
				
				$left_str = $sl; //默认all匹配
				if('location' == $cfg_rule_cache['in_wd_range']){
					if(preg_match('/\sHTTP\/1\.\d/', $sl)){ //HTTP/1.x左侧包含用户请求服务器端的地址
						list($left_str, $right_str) = explode(' HTTP/1.', $sl);
						$sl = str_ireplace('HTTP/1.', '<b>HTTP/1.</b>', $sl);
					}else{
						$sl = '<span class="redBG_white">(未找到HTTP/1.，已进入贪婪匹配)</span> '.$sl;
					}
				}
				
				
				if('and' == $cfg_rule_cache['in_wd_relation']){//查找关键词and关系
					if($in_wd_length && $sl_pass){//再匹配
						$sl_pass = 1;
						for($i=0; $i<$in_wd_length; $i++){
							if(!substr_count($left_str, $in_wd[$i])){
								$sl_pass = 0;
								break;
							}else{
								$sl = str_ireplace($in_wd[$i], '<em>'.$in_wd[$i].'</em>', $sl);
							}
						}
					}
				}else{//查找关键词or关系匹配，默认
					if($in_wd_length && $sl_pass){//再匹配
						$sl_pass = 0;
						for($i=0; $i<$in_wd_length; $i++){
							if(substr_count($left_str, $in_wd[$i])){
								$sl = str_ireplace($in_wd[$i], '<em>'.$in_wd[$i].'</em>', $sl);
								$sl_pass = 1;
								//break;
							}
						}
					}
				}
				
				if(!$sl_pass){
					continue;
				}
				
				//echo "<p> {$sl} </p>\n";
				if($sl)
				{
					preg_match('/(\d{1,3}\.){3}(?=\d{1,3})/', $sl, $match);
					//print_r( $match );
					if(isset($match[0]) && $match[0])
					{
						$ip_net_data[]	= trim($match[0], '.');
					}
				}
				
				$c++;
			}
			fclose($fp);
			
			//print_r( $ip_net_data );
			$ip_net_count_values_data	= array_count_values($ip_net_data);
			arsort($ip_net_count_values_data, SORT_NUMERIC);
			echo "<p><b>共有 ".array_sum($ip_net_count_values_data)." 条日志，".count($ip_net_count_values_data)."个ip段</b></p>";
			foreach($ip_net_count_values_data AS $ip_net=>$v)
			{
				echo "<p><a href=\"http://api.jiayyy.com/v1/get-ip-info?ip={$ip_net}.1\" target=\"_blank\">{$ip_net}.*</a> &nbsp; &nbsp; 访问 {$v} 次</p>";
			}
		}
		
		list($mico_second, $second) = explode(' ', microtime());
		$end_time = $mico_second + $second;
		
		echo '<p>耗时：'.round($end_time-$start_time, 6).'秒</p>';
	}
?>
</div>
<p>&nbsp; </p><p>&nbsp; </p>

</body>
</html>