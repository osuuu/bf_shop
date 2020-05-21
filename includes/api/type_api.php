<?php
/**
 * 分类操作接口
 * @author 捕风阁 www.osuu.net
 * @program  BF发卡网 https://github.com/osuuu/BF_SHOP
 * @time 2020.4.13
 */
include("../api.inc.php");
$username=daddslashes($_POST['username']);
$password=daddslashes($_POST['password']);
$all=$_POST['all'];//获取全部
$add=$_POST['add'];//添加
$id=$_POST['id'];//详细查询
$del=$_POST['del'];//删除
$edit=$_POST['edit'];//编辑
$name=$_POST['name'];
$weight=$_POST['weight'];
$status=$_POST['status'];

$data = file_get_contents(curPageURL().'/includes/api/login_api.php?username='.$username.'&password='.$password);
$data = json_decode($data,true);

if($data['status'] == 200){
	if($all){//查询全部
		$res = $dbh->prepare("select * from bf_type");
		$res->execute();
		$row = $res->fetchAll(PDO::FETCH_ASSOC);
		$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	}else if($id){//详细查询
		$res = $dbh->prepare("select * from bf_type where id = $id");
		$res->execute();
		$row = $res->fetch(PDO::FETCH_ASSOC);
		$result = array("status" => 200, "message" => "获取成功", "data" => $row);
	}else if($add){//添加
		$sql="insert into bf_type (name,weight,starttime,status) values (:name,:weight,:starttime,:status)";
		$res = $dbh->prepare($sql);
		$res->bindValue(":name",$add);
		$res->bindValue(":weight",$weight);
		$res->bindValue(":starttime",time());
		$res->bindValue(":status",$status);
		$res->execute();
		$row = $res->rowCount();
		if($row == 1){
			$result = array("status" => 200, "message" => "添加成功", "data" => null);
		}else{
			$result = array("status" => 100, "message" => "添加失败", "data" => null);
		}
	}else if($del){
		//删除分类
		$res = $dbh->prepare("delete from bf_type where id=$del");
		$res->execute();
		$row = $res->rowCount();
		//删除商品
		$res2 = $dbh->prepare("delete from bf_goods where type_id=$del");
		$res2->execute();
		$rows = $res2->rowCount();
		if($row == 1){
			$result = array("status" => 200, "message" => "删除成功", "data" => null);
		}else{
			$result = array("status" => 100, "message" => "删除失败", "data" => null); 
		}
	}else if($edit){//编辑
		$sql="update bf_type set name=:name,weight=:weight,status=:status where id = :id";
		$res = $dbh->prepare($sql);
		$res->bindValue(":id",$edit);
		$res->bindValue(":name",$name);
		$res->bindValue(":weight",$weight);
		$res->bindValue(":status",$status);
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