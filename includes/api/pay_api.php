<?php
/**
 * 支付操作接口
 * @author 捕风阁 www.osuu.net
 * @program  BF发卡网 https://github.com/osuuu/BF_SHOP
 * @time 2020.4.13
 */
include("../api.inc.php");
$username=daddslashes($_POST['username']);
$password=daddslashes($_POST['password']);
$all=$_POST['all'];//获取全部
$edit=$_POST['edit'];//编辑
$alipay=$_POST['alipay'];
$wxpay=$_POST['wxpay'];
$qqpay=$_POST['qqpay'];
$epayurl=$_POST['epayurl'];
$epayid=$_POST['epayid'];
$epaykey=$_POST['epaykey'];
$ali_partner=$_POST['ali_partner'];
$ali_seller=$_POST['ali_seller'];
$ali_key=$_POST['ali_key'];
$codeid=$_POST['codeid'];
$codekey=$_POST['codekey'];
$f2f_appid=$_POST['f2f_appid'];
$f2f_public=$_POST['f2f_public'];
$f2f_private=$_POST['f2f_private'];
$qq_mchid=$_POST['qq_mchid'];
$qq_mchkey=$_POST['qq_mchkey'];
$wx_appid=$_POST['wx_appid'];
$wx_mchid=$_POST['wx_mchid'];
$wx_key=$_POST['wx_key'];
$wx_secret=$_POST['wx_secret'];

$data = file_get_contents(curPageURL().'/includes/api/login_api.php?username='.$username.'&password='.$password);
$data = json_decode($data,true);

if($data['status'] == 200){
	if($all){//查询全部
		$res = $dbh->prepare("select * from bf_pay where id=1");
		$res->execute();
		$row = $res->fetch(PDO::FETCH_ASSOC);
		$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	}else if($edit){//编辑
		$sql="update bf_pay set alipay=:alipay,wxpay=:wxpay,qqpay=:qqpay,epayurl=:epayurl,epayid=:epayid,epaykey=:epaykey,codeid=:codeid,codekey=:codekey,ali_partner=:ali_partner,ali_seller=:ali_seller,ali_key=:ali_key,f2f_appid=:f2f_appid,f2f_public=:f2f_public,f2f_private=:f2f_private,qq_mchid=:qq_mchid,qq_mchkey=:qq_mchkey,wx_appid=:wx_appid,wx_mchid=:wx_mchid,wx_key=:wx_key,wx_secret=:wx_secret where id =1";
		$res = $dbh->prepare($sql);
		$res->bindValue(":alipay",$alipay);
		$res->bindValue(":wxpay",$wxpay);
		$res->bindValue(":qqpay",$qqpay);
		$res->bindValue(":epayurl",$epayurl);
		$res->bindValue(":epayid",$epayid);
		$res->bindValue(":epaykey",$epaykey);
		$res->bindValue(":codeid",$codeid);
		$res->bindValue(":codekey",$codekey);
		$res->bindValue(":ali_partner",$ali_partner);
		$res->bindValue(":ali_seller",$ali_seller);
		$res->bindValue(":ali_key",$ali_key);
		$res->bindValue(":f2f_appid",$f2f_appid);
		$res->bindValue(":f2f_public",$f2f_public);
		$res->bindValue(":f2f_private",$f2f_private);
		$res->bindValue(":qq_mchid",$qq_mchid);
		$res->bindValue(":qq_mchkey",$qq_mchkey);
		$res->bindValue(":wx_appid",$wx_appid);
		$res->bindValue(":wx_mchid",$wx_mchid);
		$res->bindValue(":wx_key",$wx_key);
		$res->bindValue(":wx_secret",$wx_secret);
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