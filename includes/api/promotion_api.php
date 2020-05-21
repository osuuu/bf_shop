<?php
/**
 * 促销操作接口
 * @author 捕风阁 www.osuu.net
 * @program  BF发卡网 https://github.com/osuuu/BF_SHOP
 * @time 2020.4.13
 */
include("../api.inc.php");
$username=daddslashes($_POST['username']);
$password=daddslashes($_POST['password']);
$allgoods=$_POST['allgoods'];//获取全部商品
$all=$_POST['all'];//获取全部
$add=$_POST['add'];//添加
$del=$_POST['del'];//删除
$goodsid=$_POST['goodsid'];
$endtime=$_POST['endtime'];
$discount=$_POST['discount'];

$data = file_get_contents(curPageURL().'/includes/api/login_api.php?username='.$username.'&password='.$password);
$data = json_decode($data,true);

if($data['status'] == 200){
	if($all){//查询全部
		$res = $dbh->prepare("select * from bf_promotion");
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		$result = array("status" => 200, "message" => "获取成功", "data" => $row);
		
	}else if($allgoods){//获取全部商品
		$res = $dbh->prepare("select * from bf_goods where state=1");
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		$result = array("status" => 200, "message" => "获取成功", "data" => $row);
		
	}else if($add){//添加
		$res = $dbh->prepare("select * from bf_promotion where goodsid=$goodsid");
		$res->execute();
		$row = $res->rowCount();
		if($row>=1){
			$result = array("status" => 100, "message" => "此商品已有活动存在请先删除", "data" => null);
		}else{
			$res1 = $dbh->prepare("select * from bf_goods where id=$goodsid");
			$res1->execute();
			$row1 = $res1->fetch(PDO::FETCH_ASSOC);
			
			$sql2="insert into bf_promotion (name,discount,goodsid,endtime,gName) values (:name,:discount,:goodsid,:endtime,:gName)";
			$res2 = $dbh->prepare($sql2);
			$res2->bindValue(":name",$add);
			$res2->bindValue(":goodsid",$goodsid);
			$res2->bindValue(":endtime",$endtime);
			$res2->bindValue(":discount",$discount);
			$res2->bindValue(":gName",$row1['name']);
			$res2->execute();
			$row2 = $res2->rowCount();
			if($row2 == 1){
				$result = array("status" => 200, "message" => "添加成功", "data" => null);
			}else{
				$result = array("status" => 100, "message" => "添加失败", "data" => null);
			}
		}
		
	}else if($del){//删除
		$res = $dbh->prepare("delete from bf_promotion where id=$del");
		$res->execute();
		$row = $res->rowCount();
		if($row == 1){
			$result = array("status" => 200, "message" => "删除成功", "data" => null);
		}else{
			$result = array("status" => 100, "message" => "删除失败", "data" => null); 
		}
	}
	
}else{
	$result = array("status" => -1, "message" => "验证失败", "data" => null);
}


echo(json_encode($result));   


        
?>