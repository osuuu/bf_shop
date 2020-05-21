<?php
/**
 * 后台登录接口
 * @author 捕风阁 www.osuu.net
 * @program  BF发卡网 https://github.com/osuuu/BF_SHOP
 * @time 2020.4.13
 */
 include("../api.inc.php");
 $username=daddslashes($_GET['username']);
 $password=daddslashes($_GET['password']);
 $password = md5($password.$password_hash);


if(!$username || !$password){
	$result = array("status" => -1, "message" => "验证失败", "data" => null);
}else{
	$res = $dbh->prepare("select * from bf_admin where username = :name");
	$res->bindValue(":name",$username);
	$res->execute();
	$row = $res->fetch(PDO::FETCH_ASSOC);
	
	if(!$row){
		$result = array("status" => -1, "message" => "管理员账号不存在", "data" => null);
	}else if($password != $row['password']){
		$result = array("status" => 100, "message" => "密码错误", "data" => null);
	}else{
		$result = array("status" => 200, "message" => "登录成功", "data" => null); 
	}
}
        
echo(json_encode($result));

?>