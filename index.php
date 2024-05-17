<?php
	set_time_limit(0);
	$cfg_rule_cache = include(dirname(__FILE__).'/runtime/rule.cache.php');
	
	list($mico_second, $second) = explode(' ', microtime());
	$start_time = $mico_second + $second;
	
	$get_filename = isset($_GET['filename']) ? $_GET['filename'] : '';
	
	if(!file_exists(dirname(__FILE__).'/runtime/in_wd.txt'))
	{
		file_put_contents(dirname(__FILE__).'/runtime/in_wd.txt', '');
	}
	if(!file_exists(dirname(__FILE__).'/runtime/over_wd.txt'))
	{
		file_put_contents(dirname(__FILE__).'/runtime/over_wd.txt', '');
	}
	if(!file_exists(dirname(__FILE__).'/runtime/rule.cache.php'))
	{
		file_put_contents(dirname(__FILE__).'/runtime/rule.cache.php', '');
	}
?>
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Apache/Nginx日志分析</title>
<style type="text/css">
	body{background:#e9e9ed;}
	form{margin:0;}
	.left{float:left;}
	.right{float:right;}
	.clear{clear:both;}
	*{font-size:12px; line-height:1.5;}
	.result div:hover{background:#ffffff;}
	.result div span.red{background:#ff0000; color:#ffffff; padding:0 3px;}
	.result div{padding:3px 0; line-height:1.0;}
	.result u{background:#93a554; text-decoration:none;}
	.result em{background:#000000; color:#ffffff; font-style:normal;}
	textarea{border:1px solid #cecece; margin-right:15px;}
	.redBG_white{background:#ff0000;color:#ffffff;padding:0 5px;}
	.toolBar{border:1px solid #cecece; position:fixed; background:#ffffff; width:95%; padding:5px 10px 10px; min-width:1000px; border-bottom-width:2px;}
	
	.result u{background:#d4d0c8; text-decoration:none;}
	.result i{border:1px solid #666666; border-radius:10px; margin-left:5px; padding:0 4px; background:#ffffff; font-style:normal;}
	textarea{border:1px solid #cecece; margin-right:15px; line-height:1.2;}
</style>
</head>
<body>
<?php
	include(dirname(__FILE__).'/tool_bar.inc.php');
?>
<div style="height:190px;">&nbsp; </div>
<div class="result">
<?php
	if(empty($get_filename))
	{
		echo('<p>未选择文件！</p>');
	}
	else
	{
		if(!file_exists(dirname(__FILE__).'/log/'.$get_filename))
		{
			echo('<p style="color:#ff0000;">日志文件'.$get_filename.'未找到，请检查！</p>');
		}
		else
		{
			echo '<a href="./ip_count.php?filename='.(isset($_GET['filename']) ? $_GET['filename'] : '').'&type=ip" target="_blank">按ip统计访问次数</a> &nbsp; ';
			
			echo '<a href="./ip_count.php?filename='.(isset($_GET['filename']) ? $_GET['filename'] : '').'&type=c_net" target="_blank">按C段统计访问次数</a> &nbsp; ';
			
			echo '<a href="./ip_count.php?filename='.(isset($_GET['filename']) ? $_GET['filename'] : '').'&type=b_net" target="_blank">按B段统计访问次数</a>';
			
			$fp 	= fopen(dirname(__FILE__).'/runtime/in_wd.txt', 'r');//匹配词
			$in_wd	= [];
			while(!feof($fp))
			{
				$s1 = fgets($fp);
				$s1 = trim($s1);
				if(empty($s1) || substr($s1, 0, 1)=='#'){
					continue;
				}
				$in_wd[] = $s1;
			}
			$in_wd_count = count($in_wd);
			fclose($fp);
			
			$fp 		= fopen(dirname(__FILE__).'/runtime/over_wd.txt', 'r');//排除词
			$over_wd	= [];
			while(!feof($fp))
			{
				$s2	= fgets($fp);
				$s2	= trim($s2);
				if(empty($s2) || substr($s2, 0, 1)=='#'){
					continue;
				}
				$over_wd[] = $s2;
			}
			$over_wd_count = count($over_wd);
			fclose($fp);
			
			$fp	= fopen(dirname(__FILE__).'/log/'.$get_filename, 'r');
			$c	= 0;             //被匹配到的行数
			$all_line = 0;      //总行数
			while(!FEOF($fp))
			{
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
				
				if($all_line % 20000 == 0)
				{
					flush();
					ob_flush();
					sleep(1);
				}
				
				$sl_pass = 1; //当前行的记录是否符合筛选规则，1：是，0否
				
				if($in_wd_count && $cfg_rule_cache['wd_priority']=='in_wd') //查找优先
				{
					$sl_pass = 0;
					foreach($in_wd AS $key){
						$key = str_ireplace('<', '&lt;', $key);
						$key = str_ireplace('>', '&gt;', $key);
						//if(substr_count($sl, $key)){
						if(stripos($sl, $key) !== false){
							$sl_pass = 1;
							break;
						}
					}
				}
				else if($over_wd_count && $cfg_rule_cache['wd_priority']=='over_wd') //排除优先
				{
					foreach($over_wd AS $key){
						$key = str_ireplace('<', '&lt;', $key);
						$key = str_ireplace('>', '&gt;', $key);
						//if(substr_count($sl, $key)){
						if(stripos($sl, $key) !== false){
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
						$sl = strtr($sl, ['HTTP/1.'=>'<b>HTTP/1.</b>', 'HTTP/2.'=>'<b>HTTP/2.</b>']);
					}
					else if(preg_match('/\sHTTP\/2\.\d/', $sl))
					{
						list($left_str, $right_str) = explode(' HTTP/2.', $sl);
						$sl = strtr($sl, ['HTTP/1.'=>'<b>HTTP/1.</b>', 'HTTP/2.'=>'<b>HTTP/2.</b>']);
					}
					else{
						$sl = '<span class="redBG_white">(未找到HTTP/x.x，已进入贪婪匹配)</span> '.$sl;
					}
				}
				
				
				if('and' == $cfg_rule_cache['in_wd_relation']){//查找关键词and关系
					if($in_wd_count && $sl_pass){//再匹配
						$sl_pass = 1;
						for($i=0; $i<$in_wd_count; $i++){
							//if(!substr_count($left_str, $in_wd[$i])){ //大小写敏感
							if(stripos($left_str, $in_wd[$i]) === false){ //不区分大小写
								$sl_pass = 0;
								break;
							}else{
								$sl = str_ireplace($in_wd[$i], '<em>'.$in_wd[$i].'</em>', $sl);
							}
						}
					}
				}else{//查找关键词or关系匹配，默认
					if($in_wd_count && $sl_pass){//再匹配
						$sl_pass = 0;
						for($i=0; $i<$in_wd_count; $i++){
							//if(substr_count($left_str, $in_wd[$i])){
							if(stripos($left_str, $in_wd[$i]) !== false){
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
				
				//最多输出10000行
				if($c < 10000)
				{
					$rp_size_str	= '';
					preg_match_all('/(?<=\s)\d{2,}(?=\s)/', $sl, $rp_size);	//提取响应字节大小
					if(isset($rp_size[0]))
					{
						foreach($rp_size[0] AS $_v)
						{
							if(in_array($_v, [200, 301, 302, 400, 403, 404, 429, 500, 502, 503]))
							{
								continue;
							}
							$rp_size_str	.= "<i title=\"响应大小\">".ceil($_v/1024)."KB</i>";
						}
					}
					
					echo "<div><u>[".($c+1)."]</u> {$sl} {$rp_size_str} </div>\n";
				}
				
				$c++;
			}
			fclose($fp);
		}
		
		list($mico_second, $second) = explode(' ', microtime());
		$end_time = $mico_second + $second;
		
		echo ($c>10000 ? "<p>省略输出 ".($c-10000)." 行</p>" : "");
		
		echo '<script type="text/javascript"> document.getElementById("count").innerHTML = "（共匹配 '.$c.' <b>/</b> '.$all_line.' 行，'.round($end_time-$start_time, 6).'秒）"; </script>';
	}
?>
</div>
<p>&nbsp; </p><p>&nbsp; </p>

</body>
</html>