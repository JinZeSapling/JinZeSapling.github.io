<?php
/*
*Author:FancyPig Team
*https://www.iculture.cc
*/

$time_start = microtime(true);
define('ROOT', dirname(__FILE__).'/');
define('MATCH_LENGTH', 0.1*1024*1024);	//字符串长度 0.1M 自己设置，一般够了。
define('RESULT_LIMIT',100);


function my_scandir($path){//获取数据文件地址
	$filelist=array();
	if($handle=opendir($path)){
		while (($file=readdir($handle))!==false){
			if($file!="." && $file !=".."){
				if(is_dir($path."/".$file)){
					$filelist=array_merge($filelist,my_scandir($path."/".$file));
				}else{
					$filelist[]=$path."/".$file;
				}
			}
		}
	}
	closedir($handle);
	return $filelist;
}

function get_results($keyword){//查询
	$return=array();
	$count=0;
	$datas=my_scandir(ROOT."FancyPig"); //数据库文档目录
	if(!empty($datas))foreach($datas as $filepath){
		$filename = basename($filepath);
		$start = 0;
		$fp = fopen($filepath, 'r');
			while(!feof($fp)){
				fseek($fp, $start);
				$content = fread($fp, MATCH_LENGTH);
				$content.=(feof($fp))?"\n":'';
				$content_length = strrpos($content, "\n");
				$content = substr($content, 0, $content_length);
				$start += $content_length;
				$end_pos = 0;
				while (($end_pos = strpos($content, $keyword, $end_pos)) !== false){
					$start_pos = strrpos($content, "\n", -$content_length + $end_pos);
					$start_pos = ($start_pos === false)?0:$start_pos;
					$end_pos = strpos($content, "\n", $end_pos);
					$end_pos=($end_pos===false)?$content_length:$end_pos;
					$return[]=array(
									'f'=>$filename,
									't'=>trim(substr($content, $start_pos, $end_pos-$start_pos))
								);
					$count++;
					if ($count >= RESULT_LIMIT) break;
				}
				unset($content,$content_length,$start_pos,$end_pos);
				if ($count >= RESULT_LIMIT) break;
			}
		fclose($fp);
		if ($count >= RESULT_LIMIT) break;
	}
	return $return;
}


if(!empty($_POST)&&!empty($_POST['q'])){
	set_time_limit(0);				//不限定脚本执行时间
	$q=strip_tags(trim($_POST['q']));
	$results=get_results($q);
	$count=count($results);
}
 
?>
<!DOCTYPE HTML>
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>社交信息泄露查询平台- Powered By FancyPig's blog </title>
<meta name="copyright" content="www.iculture.cc" />
<meta name="keywords" content="SED,社工库,社工数据,社工密码,渗透,黑客数据查询,Social Engineering Data" />
<meta name="description" content="一款由FancyPig Team提供的Social Engineering Data 社工库/社工数据库查询工具。帮助您判断您的密码或个人信息是否已经被公开或泄漏。FancyPig’s blog,关注互联网安全技术,提供互联网共享服务。" />
<link rel="stylesheet" type="text/css" href="html/default.css" />
	<style type="text/css">
	body,td,th {
	color: #FFF;
}
    a:link {
	color: #0C0;
	text-decoration: none;
}
    body {
	background-color: #000;
}
    a:visited {
	text-decoration: none;
	color: #999;
}
a:hover {
	text-decoration: none;
}
a:active {
	text-decoration: none;
	color: #F00;
}
    </style>
<script>
<!--
    function check(form){
if(form.q.value==""){
  alert("Not null！");
  form.q.focus();
  return false;
 }
}
-->
</script>
	</head>
	<body>
	<div id="container"><div id="header"><a href="https://www.iculture.cc" ><h1>社交信息泄露查询平台</h1></a></div><br /><br />

<form name="from"action="index.php" method="post">
			<div id="content"><div id="create_form"><label>请输入您要查询的关键词：<input class="inurl" size="26" id="unurl" name="q" value="<?php echo !empty($q)?$q:''; ?>"/></label>
	<p class="ali"><label for="alias">关键词搜索:</label><span>姓名,电话,用户名,邮箱,QQ,论坛账户...</span></p><p class="but"><input onclick="check(form)" type="submit" value="Search" class="submit" /></p>
		</form></div>﻿
		<?php
		if(isset($count)){
			echo 'Get ' . $count . ' results, cost ' . (microtime(true) - $time_start) . " seconds"; 
			if(!empty($results)){
				echo '<ul>';
				foreach($results as $v){
					echo '<li>来自['.$v['f'].']数据 <br />详细信息:　'.$v['t'].'</li>';
				}
				echo '<br /><br /><font color=#ffff00><li>数据完全来自网络<br />所有展现的信息不代表本站观点</li></font>';
				echo '</ul>';
			}
			        echo '<hr align="center" width="550" color="#2F2F2F" size="1"><font color=#ff0000>我们无法保证信息的完全准确性';
				echo '<br />信息如果不完整或者存在缺失，您可以联系我们添加或修改';
				echo '<br />联系我们:fancypig@iculture.cc</font>';
				echo '</ul>';
		}
		?>
		<div id="nav">
<ul><li class="current"><a href="https://jq.qq.com/?_wv=1027&k=ELC16CIR">加入Q群</a></li><li><a href="https://www.iculture.cc/sg/pig=2984" target="_blank">下载源码</a></li></ul>
</div>
<div id="footer">
<p>社交信息泄露查询平台 ©2021-2099 Powered By <a href="https://www.iculture.cc/" target="_blank">FancyPig<a></p><div style="display:none">
</div>
</div>
</body>
</html>