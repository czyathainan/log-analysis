<div class="toolBar">
	<div class="left" style="padding-bottom:8px;">
		<a href="javascript:void(0);" onclick="document.getElementById('bottom_table').style.display='none'; this.style.display='none'; document.getElementById('show1').style.display='';" id="close1">收起</a>
		<a href="javascript:void(0);" onclick="document.getElementById('bottom_table').style.display=''; this.style.display='none'; document.getElementById('close1').style.display='';" id="show1" style="display:none;">展开</a> &nbsp; &nbsp; 
		<a href="./">返回</a>
	</div>
	<div class="clear"></div>
	
	<div id="bottom_table">
		<div class="left">
			<div style="border:1px solid #999999; min-width:320px; height:105px; margin-right:20px; overflow-y:scroll;">
			<?php
				$log_dir = scandir(dirname(__FILE__).'/log/');
				foreach($log_dir AS $v){
					if(!substr_count($v, '.txt') && !substr_count($v, '.log')){
						continue;
					}
					$fsize = filesize(dirname(__FILE__).'/log/'.$v);
					if($fsize < 512){
						$fsize = $fsize.' B';
					}else if($fsize < 1024 * 100){
						$fsize = round($fsize/1024, 2).' KB';
					}else{
						$fsize = round($fsize/(1024*1024), 2).' MB';
					}
					$fsize = "<span style='color:#666666; font-size:10px;'>（{$fsize}）</span>";
					
					if($get_filename == $v){
						echo("<b>$v</b> {$fsize} &nbsp; <br />");
					}else{
						echo('<a href="?filename='.$v.'">'.$v.'</a> '.$fsize.'<br /> ');
					}
				}
			?>
			</div>
			<div>分析结果：<span id="count"><?php echo $get_filename ? '<img src="loading1.gif" /> 分析中...' : '';?></span></div>
		</div>
		
		
		<form method="POST" action="rule.php?update=1" class="left">
			<div style="float:left;">
				请输入你要查找的关键词 (一行一个关键词,不区分大小写)<br />
				<textarea name="in_wd" style="width:350px; height:80px;"><?php echo file_get_contents(dirname(__FILE__).'/runtime/in_wd.txt');?></textarea>
			</div>

			<div class="left">
				请输入你要排除的关键词 (一行一个关键词,不区分大小写)<br />
				<textarea name="over_wd" style="width:350px; height:80px;"><?php echo file_get_contents(dirname(__FILE__).'/runtime/over_wd.txt');?></textarea>
			</div>
			<div class="clear"></div>
			
			<div style="padding-top:5px;">
				查找词关系：<select name="in_wd_relaction">
				<?php
					$s = '<option value="or">OR</option>
					<option value="and">AND</option>';
					echo str_ireplace('value="'.$cfg_rule_cache['in_wd_relation'].'"', 'value="'.$cfg_rule_cache['in_wd_relation'].'" selected', $s);
				?>
				</select>&nbsp; 
				
				匹配范围：<select name="in_wd_range">
				<?php
					$s = '<option value="all">整行</option>
					<option value="location">受访URL</option>';
					echo str_ireplace('value="'.$cfg_rule_cache['in_wd_range'].'"', 'value="'.$cfg_rule_cache['in_wd_range'].'" selected', $s);
				?>
				</select>&nbsp; 
				
				优先级：
				<select name="wd_priority">
				<?php
					$s = '<option value="in_wd">查找优先</option>
					<option value="over_wd">排除优先</option>';
					echo str_ireplace('value="'.$cfg_rule_cache['wd_priority'].'"', 'value="'.$cfg_rule_cache['wd_priority'].'" selected', $s);
				?>
				</select>&nbsp; 
				
				<input type="hidden" name="filename" value="<?php echo $get_filename;?>" />
				&nbsp; &nbsp; <input type="submit" value="保存规则" />
			</div>
		</form>
	</div>
</div>