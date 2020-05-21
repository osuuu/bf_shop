<?php
/**
 * 邮箱操作接口
 * @author 捕风阁 www.osuu.net
 * @program  BF发卡网 https://github.com/osuuu/BF_SHOP
 * @time 2020.4.20
 */
include("../api.inc.php");
$username=daddslashes($_POST['username']);
$password=daddslashes($_POST['password']);
$all=$_POST['all'];//获取全部
$edit=$_POST['edit'];//编辑
$mail_stmp=$_POST['mail_stmp'];
$mail_port=$_POST['mail_port'];
$mail_usr=$_POST['mail_usr'];
$mail_pwd=$_POST['mail_pwd'];
$mail_rece=$_POST['mail_rece'];


$data = file_get_contents(curPageURL().'/includes/api/login_api.php?username='.$username.'&password='.$password);
$data = json_decode($data,true);

if($data['status'] == 200){
	if($all){//查询全部
		$res = $dbh->prepare("select * from bf_email where id=1");
		$res->execute();
		$row = $res->fetch(PDO::FETCH_ASSOC);
		$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	}else if($edit){//编辑
		$sql="update bf_email set mail_stmp=:mail_stmp,mail_port=:mail_port,mail_usr=:mail_usr,mail_pwd=:mail_pwd,mail_rece=:mail_rece where id=1";
		$res = $dbh->prepare($sql);
		$res->bindValue(":mail_stmp",$mail_stmp);
		$res->bindValue(":mail_port",$mail_port);
		$res->bindValue(":mail_usr",$mail_usr);
		$res->bindValue(":mail_pwd",$mail_pwd);
		$res->bindValue(":mail_rece",$mail_rece);
	
		$res->execute();
		$row = $res->rowCount();
		if($row == 1){
			$result = array("status" => 200, "message" => "修改成功", "data" => null);
		}else{
			$result = array("status" => 100, "message" => "修改失败", "data" => null);
		}
	}
	
}else{
	$result = array("status" => -1, "message" => "验证失败", "data" => null);
}


echo(json_encode($result));   


        
?>