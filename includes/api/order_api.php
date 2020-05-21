<?php
/**
 * 订单操作接口
 * @author 捕风阁 www.osuu.net
 * @program  BF发卡网 https://github.com/osuuu/BF_SHOP
 * @time 2020.4.22
 */
include("../api.inc.php");
$username=daddslashes($_POST['username']);
$password=daddslashes($_POST['password']);
$all=$_POST['all'];//获取全部

$del=$_POST['del'];//删除
$alldel=$_POST['alldel'];//删除全部


$data = file_get_contents(curPageURL().'/includes/api/login_api.php?username='.$username.'&password='.$password);
$data = json_decode($data,true);

if($data['status'] == 200){
	if($all){//查询全部
		$res = $dbh->prepare("select * from bf_order");
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	}else if($gid){//商品所属卡密
		$res = $dbh->prepare("select * from bf_km where goodsid = $gid");
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	}else if($del){//删除
		$res = $dbh->prepare("delete from bf_order where id=$del");
		$res->execute();
		$row = $res->rowCount();
		if($row == 1){
			$result = array("status" => 200, "message" => "删除成功", "data" => null);
		}else{
			$result = array("status" => 100, "message" => "删除失败", "data" => null); 
		}
	}else if($alldel){//删除
		$res = $dbh->prepare("delete from bf_order where status=0");
		$res->execute();
		$row = $res->rowCount();
		if($row >= 1){
			$result = array("status" => 200, "message" => "删除成功，已删除 $row 个无效订单", "data" => null);
		}else{
			$result = array("status" => 100, "message" => "删除失败", "data" => null); 
		}
	}
	
}else{
	$result = array("status" => -1, "message" => "验证失败", "data" => null);
}


echo(json_encode($result));   


        
?>