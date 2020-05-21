<?php
/* * 
 * 功能：彩虹易支付页面跳转同步通知页面
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见epay_notify_class.php中的函数verifyReturn
 */
include("../api.inc.php");
require_once("epay/epay.config.php");
require_once("epay/epay_notify.class.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>订单提取中，请耐心等候</title>
</head>
<body>
<div id="root">
    <style>
        .page-loading-warp {
            padding: 70px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .ant-spin {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            color: rgba(0, 0, 0, 0.65);
            font-size: 14px;
            font-variant: tabular-nums;
            line-height: 1.5;
            list-style: none;
            -webkit-font-feature-settings: 'tnum';
            font-feature-settings: 'tnum';
            position: absolute;
            display: none;
            color: #1890ff;
            text-align: center;
            vertical-align: middle;
            opacity: 0;
            -webkit-transition: -webkit-transform 0.3s cubic-bezier(0.78, 0.14, 0.15, 0.86);
            transition: -webkit-transform 0.3s cubic-bezier(0.78, 0.14, 0.15, 0.86);
            transition: transform 0.3s cubic-bezier(0.78, 0.14, 0.15, 0.86),
            -webkit-transform 0.3s cubic-bezier(0.78, 0.14, 0.15, 0.86);
        }

        .ant-spin-spinning {
            position: static;
            display: inline-block;
            opacity: 1;
        }

        .ant-spin-dot {
            position: relative;
            display: inline-block;
            font-size: 20px;
            width: 20px;
            height: 20px;
        }

        .ant-spin-dot-item {
            position: absolute;
            display: block;
            width: 9px;
            height: 9px;
            background-color: #1890ff;
            border-radius: 100%;
            -webkit-transform: scale(0.75);
            -ms-transform: scale(0.75);
            transform: scale(0.75);
            -webkit-transform-origin: 50% 50%;
            -ms-transform-origin: 50% 50%;
            transform-origin: 50% 50%;
            opacity: 0.3;
            -webkit-animation: antSpinMove 1s infinite linear alternate;
            animation: antSpinMove 1s infinite linear alternate;
        }

        .ant-spin-dot-item:nth-child(1) {
            top: 0;
            left: 0;
        }

        .ant-spin-dot-item:nth-child(2) {
            top: 0;
            right: 0;
            -webkit-animation-delay: 0.4s;
            animation-delay: 0.4s;
        }

        .ant-spin-dot-item:nth-child(3) {
            right: 0;
            bottom: 0;
            -webkit-animation-delay: 0.8s;
            animation-delay: 0.8s;
        }

        .ant-spin-dot-item:nth-child(4) {
            bottom: 0;
            left: 0;
            -webkit-animation-delay: 1.2s;
            animation-delay: 1.2s;
        }

        .ant-spin-dot-spin {
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
            -webkit-animation: antRotate 1.2s infinite linear;
            animation: antRotate 1.2s infinite linear;
        }

        .ant-spin-lg .ant-spin-dot {
            font-size: 32px;
            width: 32px;
            height: 32px;
        }

        .ant-spin-lg .ant-spin-dot i {
            width: 14px;
            height: 14px;
        }

        @media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
            .ant-spin-blur {
                background: #fff;
                opacity: 0.5;
            }
        }

        @-webkit-keyframes antSpinMove {
            to {
                opacity: 1;
            }
        }

        @keyframes antSpinMove {
            to {
                opacity: 1;
            }
        }

        @-webkit-keyframes antRotate {
            to {
                -webkit-transform: rotate(405deg);
                transform: rotate(405deg);
            }
        }

        @keyframes antRotate {
            to {
                -webkit-transform: rotate(405deg);
                transform: rotate(405deg);
            }
        }
    </style>
    <div class="page-loading-warp">
        <div class="ant-spin ant-spin-lg ant-spin-spinning">
          <span class="ant-spin-dot ant-spin-dot-spin"
          ><i class="ant-spin-dot-item"></i><i class="ant-spin-dot-item"></i
              ><i class="ant-spin-dot-item"></i><i class="ant-spin-dot-item"></i
              ></span>
            <p>购买成功，请耐心等候</p>
            <p>即将跳转订单查询页面</p>
        </div>
    </div>
    <script src="https://cdn.staticfile.org/jquery/1.12.4/jquery.min.js"></script>
<?php
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	//商户订单号

	$out_trade_no = $_GET['out_trade_no'];

	//支付宝交易号

	$trade_no = $_GET['trade_no'];

	//交易状态
	$trade_status = $_GET['trade_status'];

	//支付方式
	$type = $_GET['type'];

	$res = $dbh->prepare("select * from bf_order where out_trade_no = $out_trade_no");
	$res->execute();
	$row = $res->fetch(PDO::FETCH_ASSOC);
	
	$res1 = $dbh->prepare("select * from bf_email where id=1");
	$res1->execute();
	$row1 = $res1->fetch(PDO::FETCH_ASSOC);
	
    if($_GET['trade_status'] == 'TRADE_SUCCESS') {
		if($row['flag']==0 && $row['status']==1){
		if($title['emailfk']==1){
			$data = file_get_contents(curPageURL().'/includes/api/send_api.php?key='.$title['monit'].'&flag=1&out_trade_no='.$out_trade_no.'&rece='.$row['user'].'@qq.com');
			$data = json_decode($data,true);
		}
		if($title['sold']==1){
			$data = file_get_contents(curPageURL().'/includes/api/send_api.php?key='.$title['monit'].'&flag=3&out_trade_no='.$out_trade_no.'&rece='.$row1['mail_rece']);
			$data = json_decode($data,true);
		}
		if($title['early']==1){
			$gid=$row['goodsid'];
			$n=$title['earlylim'];
			$res2 = $dbh->prepare("select * from bf_km where goodsid =$gid AND status=0");
			$res2->execute();
			$row2 = $res2->rowCount();
			if($row2<$n){
				$data = file_get_contents(curPageURL().'/includes/api/send_api.php?key='.$title['monit'].'&flag=2&gid='.$gid.'&rece='.$row1['mail_rece']);
				$data = json_decode($data,true);
			}
			
		}
		$sql="update bf_order set flag=1 where out_trade_no = $out_trade_no";
		$res = $dbh->prepare($sql);
		$res->execute();
	}


    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }

	echo "<script>window.location.href='".curPageURL()."/query?on=".$out_trade_no."';</script>";

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    echo "验证失败";
}
?>
   </div>
</body>
</html>