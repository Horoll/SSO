<?php
//判断server2域下的cookie是否存在，或者是否从sso服务发来了token
if (isset($_COOKIE['token2']) || isset($_GET['token']) )   
{
	//如果token已经存在，则用token向redis中请求用户数据
    $token = $_COOKIE['token2']??$_GET['token'];
    //连接redis
	$redis = new redis();
	$redis->connect('127.0.0.1', 6379);  
	$username = $redis->hGet($token,'username');

	if(!isset($_COOKIE['token2']))
		//生成server2域下的cookie
		setcookie('token2',$token,time()+3600);
}
else
	//如果没有登录，则跳转sso服务
	header("Location:http://localhost/sso/sso.php?from=server2"); 
?>
<html>
<head>
<title>server2</title>
</head>
<p>hello！<?=$username?></p>
</html>