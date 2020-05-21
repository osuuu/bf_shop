<?php
include("../api.inc.php");
$key=$_POST['key'];
$res = $dbh->prepare("select upkey from bf_config where id = 1");
$res->execute();
$row = $res->fetch(PDO::FETCH_ASSOC);
if($key != $row['upkey']){
	$result = array("status" => 100, "message" => "上传失败,上传key错误", "data" =>null);
}else{

// 允许上传的图片后缀
$allowedExts = array("gif", "jpeg", "jpg", "png");
$temp = explode(".", $_FILES["file"]["name"]);
// echo $_FILES["file"]["size"];
$extension = end($temp);     // 获取文件后缀名
if($_FILES["file"]["size"] > 512000){// 大于 500 kb
	$result = array("status" => 100, "message" => "图片不能大于500kb", "data" =>null);
}else if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/jpg")
|| ($_FILES["file"]["type"] == "image/pjpeg")
|| ($_FILES["file"]["type"] == "image/x-png")
|| ($_FILES["file"]["type"] == "image/png"))
&& in_array($extension, $allowedExts)){
	if ($_FILES["file"]["error"] > 0){
		$result = array("status" => 100, "message" => "上传失败", "data" =>null);
	}else{
		move_uploaded_file($_FILES["file"]["tmp_name"], "../../upload/" . time().".png");
		// echo(time().".png");
		$result = array("status" => 200, "message" => "上传成功", "data" => time().".png");
	}
}else{
	$result = array("status" => 100, "message" => "图片格式不支持", "data" =>null);
}
}
echo(json_encode($result)); 
?>
