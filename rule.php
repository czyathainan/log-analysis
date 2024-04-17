<?php
header('Content-type:text/html; charset=utf-8;');
$in_wd		= isset($_POST['in_wd']) ? trim($_POST['in_wd']) : '';
$in_wd		= str_ireplace('\"', '"', $in_wd);
$over_wd	= isset($_POST['over_wd']) ? trim($_POST['over_wd']) : '';
$over_wd	= str_ireplace('\"', '"', $over_wd);
$wd_priority	= isset($_POST['wd_priority']) ? trim($_POST['wd_priority']) : '';

$in_wd_relaction	= isset($_POST['in_wd_relaction']) ? trim($_POST['in_wd_relaction']) : '';//要查找的关键字眼的关系and或or
$in_wd_range		= isset($_POST['in_wd_range']) ? trim($_POST['in_wd_range']) : ''; //匹配范围，仅服务器本地还是贪婪匹配


$rule_arr = Array();
$rule_arr['in_wd_relation']	= $in_wd_relaction;
$rule_arr['in_wd_range']	= $in_wd_range;
$rule_arr['wd_priority']	= $wd_priority;


$fp = fopen(dirname(__FILE__).'/runtime/rule.cache.php', 'w');
	fwrite($fp, "<?php".PHP_EOL."return ".var_export($rule_arr, true).";");
fclose($fp);

$fp = fopen(dirname(__FILE__).'/runtime/in_wd.txt', 'w');
	fwrite($fp, $in_wd);
fclose($fp);

$fp = fopen(dirname(__FILE__).'/runtime/over_wd.txt', 'w');
	fwrite($fp, $over_wd);
fclose($fp);

echo('<script type="text/javascript"> alert("保存成功！"); location.href="./?"; </script>');
