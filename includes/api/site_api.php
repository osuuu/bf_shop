<?php
/**
 * 获取网站信息
 * @author 捕风阁 www.osuu.net
 * @program  BF发卡网 https://github.com/osuuu/BF_SHOP
 * @time 2020.4.13
 */
include("../api.inc.php");
 
$goods=$_POST['goods'];
$type=$_POST['type'];
$id=$_POST['id'];
$swiper=$_POST['swiper'];
$goodsid=$_POST['goodsid'];
$stock=$_POST['stock'];
$pro=$_POST['pro'];
$pay=$_POST['pay'];
$query=$_POST['query'];
$goodsquery=$_POST['goodsquery'];

if($goods){//全部商品
	$res = $dbh->prepare("select * from bf_goods where state =1");
	$res->execute();
	$row = $res->fetchAll(PDO::FETCH_ASSOC);
	$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	
}else if($type){//全部分类
	$res = $dbh->prepare("select * from bf_type where status =1");
	$res->execute();
	$row = $res->fetchAll(PDO::FETCH_ASSOC);
	$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	
}else if($id){//指定分类商品
	$res = $dbh->prepare("select * from bf_goods where type_id =$id AND state=1");
	$res->execute();
	$row = $res->fetchAll(PDO::FETCH_ASSOC);
	$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	
}else if($goodsid){//指定商品
	$res = $dbh->prepare("select * from bf_goods where id =$goodsid");
	$res->execute();
	$row = $res->fetch(PDO::FETCH_ASSOC);
	$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	
}else if($pro){//促销商品
	$res = $dbh->prepare("select * from bf_promotion where goodsid =$pro");
	$res->execute();
	$row = $res->fetch(PDO::FETCH_ASSOC);
	$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	
}else if($stock){//商品库存
	$res = $dbh->prepare("select * from bf_km where goodsid =$stock AND status=0");
	$res->execute();
	$row = $res->rowCount();
	$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	
}else if($swiper){//全部轮播
	$res = $dbh->prepare("select * from bf_swiper");
	$res->execute();
	$row = $res->fetchAll(PDO::FETCH_ASSOC);
	$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	
}else if($pay){//支付方式
	$res = $dbh->prepare("select alipay,wxpay,qqpay from bf_pay where id=1");
	$res->execute();
	$row = $res->fetch(PDO::FETCH_ASSOC);
	$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	
}else if($goodsquery){ //商品搜索
	$sql = "select * from bf_goods where name LIKE :query";
	$res = $dbh->prepare($sql);
	$res->bindValue(":query",'%'.$goodsquery.'%');
	$res->execute();
	$row = $res->fetchAll(PDO::FETCH_ASSOC);
	$result = array("status" => 200, "message" => "获取成功", "data" => $row);
}else if($query){//订单查询
	$res = $dbh->prepare("select km,endtime,out_trade_no,gName from bf_km where user=$query");
	$res->execute();
	$row = $res->fetchAll(PDO::FETCH_ASSOC);
	if(!$row){
		$ress = $dbh->prepare("select km,endtime,out_trade_no,gName from bf_km where out_trade_no=$query");
		$ress->execute();
		$rows = $ress->fetchAll(PDO::FETCH_ASSOC);
		if(!$rows){
			$result = array("status" => 100, "message" => "订单不存在", "data" => $row);
		}else{
			$result = array("status" => 200, "message" => "获取成功", "data" => $rows);
		}
	}else{
		$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	}
	
}else{
	$res = $dbh->prepare("select name,gg,pcgg,querygg,rec,kf,copy,link,stockno,template,logo from bf_config where id =1");
	$res->execute();
	$row = $res->fetch(PDO::FETCH_ASSOC);
	$result = array("status" => 200, "message" => "获取成功", "data" => $row);
}

        
echo(json_encode($result));   
        
?>