<?php
/* *
 * 支付宝当面付异步通知页面
 */

include("../api.inc.php");
require_once(ROOT."pay/f2fpay/config.php");
require_once(ROOT . "pay/f2fpay/AlipayTradeService.php");

//计算得出通知验证结果
$alipaySevice = new AlipayTradeService($config); 
//$alipaySevice->writeLog(var_export($_POST,true));
$verify_result = $alipaySevice->check($_POST);

if($verify_result) {//验证成功
	//商户订单号

	$out_trade_no = daddslashes($_POST['out_trade_no']);

	//支付宝交易号

	$trade_no = $_POST['trade_no'];
	//交易状态
	$trade_status = $_POST['trade_status'];

	//买家支付宝
	$buyer_id = daddslashes($_POST['buyer_id']);

		$res = $dbh->prepare("select * from bf_order where out_trade_no = $out_trade_no");
		$res->execute();
		$row = $res->fetch(PDO::FETCH_ASSOC);
		if($row['status']==0){
			for($i=1;$i<=$row['number'];$i++){
				$sql1="update bf_km set out_trade_no=:out_trade_no,trade_no=:trade_no,endtime=:endtime,user=:user,status=:status,gName=:gName where goodsid = :goodsid and status=0 limit  1";
				$res1 = $dbh->prepare($sql1);
				$res1->bindValue(":goodsid",$row['goodsid']);
				$res1->bindValue(":out_trade_no",$out_trade_no);
				$res1->bindValue(":trade_no",$trade_no);
				$res1->bindValue(":endtime",time());
				$res1->bindValue(":user",$row['user']);
				$res1->bindValue(":gName",$row['gName']);
				$res1->bindValue(":status",1);
				$res1->execute();
			}
			$sql2="update bf_order set trade_no=:trade_no,endTime=:endTime,status=:status where out_trade_no = :out_trade_no";
			$res2 = $dbh->prepare($sql2);
			$res2->bindValue(":out_trade_no",$out_trade_no);
			$res2->bindValue(":trade_no",$trade_no);
			$res2->bindValue(":endTime",time());
			$res2->bindValue(":status",1);
			$res2->execute();
			
			$gid=$row['goodsid'];
			//修改销量
			$res3 = $dbh->prepare("select sales from bf_goods where id =$gid ");
			$res3->execute();
			$row3 = $res3->fetch(PDO::FETCH_ASSOC);
			$sales=$row3['sales']+$row['number'];
			$res4 = $dbh->prepare("update bf_goods set sales=$sales where id = $gid");
			$res4->execute();
			
		}

	echo "success";
}
else {
    //验证失败
    echo "fail";
}
?>