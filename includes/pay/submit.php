<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>正在为您跳转到支付页面，请稍候...</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        p {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 330px;
            height: 30px;
            margin: -35px 0 0 -160px;
            padding: 20px;
            font: bold 14px/30px "宋体", Arial;
            background: #f9fafc url(./assets/load.gif) no-repeat 20px 26px;
            text-indent: 22px;
            border: 1px solid #c5d0dc;
        }

        #waiting {
            font-family: Arial;
        }
    </style>
<?php
/**
 * @author 捕风阁 www.osuu.net
 * @program  BF发卡网 https://github.com/osuuu/BF_SHOP
 * @time 2020.4.13
 */
include("../api.inc.php");
require_once("../class/Mobile.class.php");
$type=$_GET['type'];
$out_trade_no=$_GET['out_trade_no'];
$name=$_GET['name'];
$money=$_GET['money'];
$goodsid=$_GET['goodsid'];
$num=$_GET['num'];
$qq=$_GET['qq'];

if($type != 'alipay' and $type != 'wxpay' and $type != 'qqpay'){
	sysmsg('支付方式不正确');
	exit();
}else if(!$out_trade_no){
	sysmsg('订单号不能为空');
	exit();
}else if(!$name){
	sysmsg('商品名称不能为空');
	exit();
}else if(!$money){
	sysmsg('商品金额不能为空');
	exit();
}else if(!$goodsid){
	sysmsg('商品ID不能为空');
	exit();
}else if(!$qq){
	sysmsg('联系方式不能为空');
	exit();
}else if(!$num){
	sysmsg('购买数量不能为空');
	exit();
}
//效验订单是否存在
$res4 = $dbh->prepare("select * from bf_order where out_trade_no = $out_trade_no");
$res4->execute();
$row4 = $res4->rowCount();
if($row4>=1){
	sysmsg('此订单已经失效,请返回重新创建');
	exit();
}else{
	//效验支付方式
	$res = $dbh->prepare("select $type from bf_pay where id = 1");
	$res->execute();
	$row = $res->fetch(PDO::FETCH_ASSOC);
	if($row[$type] ==0){
		sysmsg('当前支付方式已关闭');
		exit();
	}
	//效验商品
	$res1 = $dbh->prepare("select name,price,minbuy,maxbuy,state from bf_goods where id = $goodsid");
	$res1->execute();
	$row1 = $res1->fetch(PDO::FETCH_ASSOC);
	if($row1['state']==0){
		sysmsg('此商品已下架或不存在');
		exit();
	}else if($num < $row1['minbuy']){
		sysmsg("此商品".$row1['minbuy']."件起购，您当前购买的是".$num."件");
		exit();
	}else if($num > $row1['maxbuy']){
		sysmsg("此商品限购".$row1['maxbuy']."件，您当前购买的是".$num."件");
		exit();
	}

	// 效验库存
	$res6 = $dbh->prepare("select * from bf_km where goodsid = $goodsid");
	$res6->execute();
	$row6 = $res6->rowCount();
	if($row6<$num){
		sysmsg('商品库存不足');
		exit();
	}
	//效验商品价格
	if($row1['price'] != $money){
		//价格不一致，判断是否促销
		$res2 = $dbh->prepare("select * from bf_promotion where goodsid = $goodsid");
		$res2->execute();
		$row2 = $res2->fetch(PDO::FETCH_ASSOC);
		if(!$res2){
			sysmsg('订单价格与商品售价不一致');
			exit();
		}else if($row2['endtime'] < time()){
			//活动无效
			sysmsg('商品促销已结束，请返回重新购买');
			exit();
		}else if($money != round($row2['discount']/10*$row1['price']*$num,2)){
			sysmsg('订单价格效验失败');
			exit();
		}
	}

	//添加订单记录
	$sql3="insert into bf_order (out_trade_no,goodsid,money,user,type,startTime,number,gName) values (:out_trade_no,:goodsid,:money,:user,:type,:startTime,:number,:gName)";
	$res3 = $dbh->prepare($sql3);
	$res3->bindValue(":out_trade_no",$out_trade_no);
	$res3->bindValue(":goodsid",$goodsid);
	$res3->bindValue(":money",$money);
	$res3->bindValue(":user",$qq);
	$res3->bindValue(":type",$type);
	$res3->bindValue(":startTime",time());
	$res3->bindValue(":number",$num);
	$res3->bindValue(":gName",$row1['name']);
	$res3->execute();
	$row3 = $res3->rowCount();
	if($row3 == 0){
		sysmsg('订单添加失败，系统出错');
		exit();
	}
}

$res5 = $dbh->prepare("select alipay,wxpay,qqpay from bf_pay where id = 1");
$res5->execute();
$row5 = $res5->fetch(PDO::FETCH_ASSOC);
// 发起支付

if($type == 'alipay' && $row5['alipay'] == 2 || $type == 'wxpay' && $row5['wxpay'] == 2 || $type == 'qqpay' && $row5['qqpay'] == 2){
	//易支付
	require_once(ROOT."pay/epay/epay.config.php");
	require_once(ROOT."pay/epay/epay_submit.class.php");
	
	$parameter = array(
		"pid" => $alipay_config['partner'],//商户ID
		"type" => $type,//支付类型
		"notify_url"	=> curPageURL().'/includes/pay/epay_notify.php',//异步通知
		"return_url"	=> curPageURL().'/includes/pay/epay_return.php',//回调
		"out_trade_no"	=> $out_trade_no,//订单编号
		"name"	=> $row1['name'],//商品名称
		"money"	=> $money,//金额
		"sitename"	=> $title['name']//网站名称
	);
	//建立请求
	$alipaySubmit = new AlipaySubmit($alipay_config);
	$html_text = $alipaySubmit->buildRequestForm($parameter,"POST", "正在验证订单并跳转...");
	echo $html_text;

}else if($type == 'alipay' && $row5['alipay'] == 3 || $type == 'wxpay' && $row5['wxpay'] == 3 || $type == 'qqpay' && $row5['qqpay'] == 3){
	//码支付
	echo "<script>window.location.href='".curPageURL()."/includes/pay/codepay.php?out_trade_no=".$out_trade_no."&money=".$money."&type=".$type."';</script>";

	
}else if($type == 'alipay'){
	if($row5['alipay'] == 1){
		//支付宝官方支付
	require_once(ROOT."pay/alipay/alipay.config.php");
	require_once(ROOT."pay/alipay/alipay_submit.class.php");
	if (isMobile() == true) {
    	$alipay_service = "alipay.wap.create.direct.pay.by.user";
	} else {
    	$alipay_service = "create_direct_pay_by_user";
	}
	//构造要请求的参数数组，无需改动
	$parameter = array(
		"service"       => $alipay_service,
		"partner"       => $alipay_config['partner'],
		"seller_id"  => $alipay_config['seller_id'],
		"payment_type"	=> $alipay_config['payment_type'],
		"notify_url"	=> curPageURL().'/includes/pay/alipay_notify.php',
		"return_url"	=> curPageURL().'/includes/pay/alipay_return.php',
		
		"anti_phishing_key"=>$alipay_config['anti_phishing_key'],
		"exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $row1['name'],
		"total_fee"	=> $money,
		"body"	=> $body,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
	);

	//建立请求
	$alipaySubmit = new AlipaySubmit($alipay_config);
	$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
	echo $html_text;
	}else if($row5['alipay'] == 4){
		//当面付
	echo "<script>window.location.href='".curPageURL()."/includes/pay/alipay.php?out_trade_no=".$out_trade_no."';</script>";
	}
	
}else  {
	sysmsg('当前支付方式未开放');
	exit();
}



?>

<p>正在为您跳转到支付页面，请稍候...</p>
</body>
</html>