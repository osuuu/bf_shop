<?php
//简易安装接口

	
require './db.class.php';
require '../includes/function.php';
require '../includes/config.php';
$db_host=isset($_GET['host'])?$_GET['host']:NULL;
$db_port=isset($_GET['port'])?$_GET['port']:NULL;
$db_user=isset($_GET['user'])?$_GET['user']:NULL;
$db_pwd=isset($_GET['pass'])?$_GET['pass']:NULL;
$db_name=isset($_GET['dbname'])?$_GET['dbname']:NULL;

if(!$db_host || !$db_port || !$db_user || !$db_pwd || !$db_name){
	sysmsg('数据库信息填写不全');
}else if($dbconfig['host'] && $dbconfig['port'] && $dbconfig['user'] && $dbconfig['pwd'] && $dbconfig['dbname']){
	sysmsg('您已经安装过了无需再次安装');
	exit();
}else{
$config="<?php
/*数据库配置*/
\$dbconfig=array(
	'host' => '{$db_host}', //数据库服务器
	'port' => {$db_port}, //数据库端口
	'user' => '{$db_user}', //数据库用户名
	'pwd' => '{$db_pwd}', //数据库密码
	'dbname' => '{$db_name}' //数据库名
);
?>";
if(!$con=DB::connect($db_host,$db_user,$db_pwd,$db_name,$db_port)){
	if(DB::connect_errno()==2002)
		sysmsg('连接数据库失败，数据库地址填写错误');
	elseif(DB::connect_errno()==1045)
		sysmsg('连接数据库失败，数据库用户名或密码填写错误');
	elseif(DB::connect_errno()==1049)
		sysmsg('连接数据库失败，数据库名不存在！');
	else
		sysmsg ('连接数据库失败，['.DB::connect_errno().']'.DB::connect_error().'');
	}elseif(file_put_contents('../includes/config.php',$config)){
		$sql=file_get_contents("install.sql");
		$sql=explode(';',$sql);
		$cn = DB::connect($db_host,$db_user,$db_pwd,$db_name,$db_port);
		if (!$cn) die('err:'.DB::connect_error());
		DB::query("set sql_mode = ''");
		DB::query("set names utf8");
		$t=0; $e=0; $error='';
		for($i=0;$i<count($sql);$i++) {
			if ($sql[$i]=='')continue;
			if(DB::query($sql[$i])) {
				++$t;
			} else {
				$e++;
				$error.=DB::error().'<br/>';
			}
		}
		if($e<=1) {
			sysmsg ('<div>安装成功！SQL成功'.$t.'句<br/>
			<span style="color:red">请务必自行删除网站install目录</span><br/>
			<span style="color:green">默认后台账号admin 密码admin</span></div><br/>
			请牢记后台登录地址 其他地址无法登录<a href="/bf-login">登录后台</a> 
			');
		} else {
			sysmsg ('<div>安装失败,建议参照教程手动安装<br/>SQL成功'.$t.'句/失败'.$e.'句<br/>错误信息：'.$error.'</div>');
		}
	}


	


}

	



?>