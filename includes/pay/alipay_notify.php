<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */
include("../api.inc.php");
require_once("alipay/alipay.config.php");
require_once("alipay/alipay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//商户订单号

	$out_trade_no = $_POST['out_trade_no'];

	//支付宝交易号

	$trade_no = $_POST['trade_no'];

	//交易状态
	$trade_status = $_POST['trade_status'];


    if($_POST['trade_status'] == 'TRADE_FINISHED') {
		//判断该笔订单是否在商户网站中已经做过处理
    }
    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
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
    }

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        
	echo "success";		//请不要修改或删除
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
?>