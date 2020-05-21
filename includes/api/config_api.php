<?php
/**
 * 设置操作接口
 * @author 捕风阁 www.osuu.net
 * @program  BF发卡网 https://github.com/osuuu/BF_SHOP
 * @time 2020.4.20
 */
include("../api.inc.php");
$username=daddslashes($_POST['username']);
$password=daddslashes($_POST['password']);
$all=$_POST['all'];//查询
$edit=$_POST['edit'];//编辑
$monit=$_POST['monit'];
$upkey=$_POST['upkey'];
$gg=$_POST['gg'];
$pcgg=$_POST['pcgg'];
$querygg=$_POST['querygg'];
$kf=$_POST['kf'];
$rec=$_POST['rec'];
$copy=$_POST['copy'];
$link=$_POST['link'];
$stockno=$_POST['stockno'];
$emailfk=$_POST['emailfk'];
$early=$_POST['early'];
$earlylim=$_POST['earlylim'];
$sold=$_POST['sold'];
$home=$_POST['home'];//首页
$reset=$_POST['reset'];//重置密码
$checkp = '/^[a-zA-Z0-9]{6,10}$/';
$template=$_POST['template'];
$logo=$_POST['logo'];

$data = file_get_contents(curPageURL().'/includes/api/login_api.php?username='.$username.'&password='.$password);
$data = json_decode($data,true);

if($data['status'] == 200){
	if($all){//查询全部
		$res = $dbh->prepare("select * from bf_config where id = 1");
		$res->execute();
		$row = $res->fetch(PDO::FETCH_ASSOC);
		$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	}else if($edit){//编辑
		$sql="update bf_config set name=:name,monit=:monit,upkey=:upkey,gg=:gg,pcgg=:pcgg,querygg=:querygg,kf=:kf,rec=:rec,copy=:copy,link=:link,stockno=:stockno,emailfk=:emailfk,early=:early,earlylim=:earlylim,sold=:sold where id = 1";
		$res = $dbh->prepare($sql);
		$res->bindValue(":name",$edit);
		$res->bindValue(":monit",$monit);
		$res->bindValue(":upkey",$upkey);
		$res->bindValue(":gg",$gg);
		$res->bindValue(":pcgg",$pcgg);
		$res->bindValue(":querygg",$querygg);
		$res->bindValue(":kf",$kf);
		$res->bindValue(":rec",$rec);
		$res->bindValue(":copy",$copy);
		$res->bindValue(":link",$link);
		$res->bindValue(":stockno",$stockno);
		$res->bindValue(":emailfk",$emailfk);
		$res->bindValue(":early",$early);
		$res->bindValue(":earlylim",$earlylim);
		$res->bindValue(":sold",$sold);
		$res->execute();
		$row = $res->rowCount();
		if($row == 1){
			$result = array("status" => 200, "message" => "修改成功", "data" => null);
		}else{
			$result = array("status" => 100, "message" => "修改失败", "data" => null);
		}
	}else if($home){
		$yesterday =strtotime(date('Y-m-d'.'00:00:00',time()-3600*24));
		$today= strtotime(date('Y-m-d'.'00:00:00',time()));
		$now=time();
		//今日订单
		$res1 = $dbh->prepare("select money from bf_order where endTime>=$today and status=1");
		$res1->execute();
		$row1 = $res1->fetchAll(PDO::FETCH_ASSOC);
		$o1 = $res1->rowCount();
		//交易完成
		$res2 = $dbh->prepare("select money from bf_order");
		$res2->execute();
		$o2 = $res2->rowCount();
		//昨日交易
		$res3 = $dbh->prepare("select money from bf_order where endTime>=$yesterday and endTime<=$today and status=1");
		$res3->execute();
		$row3 = $res3->fetchAll(PDO::FETCH_ASSOC);
		
		//最近3天订单
		$orday =strtotime(date('Y-m-d'.'00:00:00',time()-3600*(24*3)));
		$res4 = $dbh->prepare("select * from bf_order where endTime>=$orday and status=1");
		$res4->execute();
		$row4 = $res4->fetchAll(PDO::FETCH_ASSOC);
	
		$result = array("status" => 200, "message" => "验证成功",
		"todayorder" => $o1,
		"todaymoney"=>$row1,
		"orderover"=>$o2,
		"yesterday"=>$row3,
		"late"=>$row4,
		"version"=>VERSION,
		);
	}else if($reset){ //重置密码
		if(!preg_match($checkp, $reset)){
			$result = array("status" => 100, "message" => "密码需6-10位字母或数字", "data" => null);
		}else{
			$sql="update bf_admin set password=:pass where username = :username";
			$res = $dbh->prepare($sql);
			$res->bindValue(":username",$username);
			$res->bindValue(":pass",md5($reset.$password_hash));
			$res->execute();
			$row = $res->rowCount();
			if($row==1){
				$result = array("status" => 200, "message" => "修改成功", "data" => null);
			}else{
				$result = array("status" => 100, "message" => "修改失败", "data" => null); 
			}
		}
	}else if($template){
		$sql="update bf_config set template=:template where id = 1";
		$res = $dbh->prepare($sql);
		$res->bindValue(":template",$template);
		$res->execute();
		$row = $res->rowCount();
		if($row == 1){
			$result = array("status" => 200, "message" => "修改成功", "data" => null);
		}else{
			$result = array("status" => 100, "message" => "修改失败", "data" => null);
		}
	}else if($logo){
		$sql="update bf_config set logo=:logo where id = 1";
		$res = $dbh->prepare($sql);
		$res->bindValue(":logo",$logo);
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