
<?php 
//判断要跳转哪个页面
$from = $_GET['from']??'server1';

//判断sso域下的cookie是否存在
if (isset($_COOKIE['token']))   
{
	//如果token已经存在，则说明用户已经在其他服务上登录过，将token返回
    $token = $_COOKIE['token'];
    //重定向到服务页面
	header("Location:./".$from.".php?token=".$token); 
}
//登录
if(isset($_GET['act']) && $_GET['act']== 'login'){
	//从数据库中比对帐号密码，在此省略.......
	//........
	//登录成功后获取用户名
	$username = $_GET['username'];
	//将用户名+当前时间戳，加密后存入redis，作为token
	$token = md5($username.time());
	//生成sso域下的cookie和过期时间
	$expireTime = 3600;
	setcookie('token',$token,time()+$expireTime);
	//连接redis
	$redis = new redis();
	$redis->connect('127.0.0.1', 6379);  
	//向redis中写入token以及用户信息
	$s = $redis->hset($token,'username',$username);
	//设置token过期时间
	$redis->expireAt($token, time()+$expireTime);
	//重定向到服务页面
	header("Location:./".$from.".php?token=".$token); 
}
?>
<html>
<head>
<title>统一认证服务</title>
</head>
<form action="sso.php" method="get">
	<input type="hidden" name="from" value="<?php echo $from ?>">
	<input type="hidden" name="act" value="login">
  <p>账户: <input type="text" name="username" /></p>
  <p>密码: <input type="password" name="password" /></p>
  <input type="submit" value="Submit" />
</form>
</html>