<?php
include("../api.inc.php");


$_POST = $_GET;
ksort($_POST); //排序post参数
reset($_POST); //内部指针指向数组中的第一个元素
$sign = '';
foreach ($_POST AS $key => $val) {
    if ($val == '') continue;
    if ($key != 'sign') {
        if ($sign != '') {
            $sign .= "&";
            $urls .= "&";
        }
        $sign .= "$key=$val"; //拼接为url参数形式
        $urls .= "$key=" . urlencode($val); //拼接为url参数形式
    }
}
if (!$_POST['pay_no'] || md5($sign . $conf['codekey']) != $_POST['sign']) { //不合法的数据 KEY密钥为你的密钥
   	sysmsg('验证失败');
} else { //合法的数据
    $trade_no = $_POST['pay_no'];
    $out_trade_no = $_POST['param'];
    
	$res = $dbh->prepare("select * from bf_order where out_trade_no = $out_trade_no");
	$res->execute();
	$row = $res->fetch(PDO::FETCH_ASSOC);
	
	$res1 = $dbh->prepare("select * from bf_email where id=1");
	$res1->execute();
	$row1 = $res1->fetch(PDO::FETCH_ASSOC);
 
//以防码支付掉单，这里做双重验证
     if($row['status']==0){
			for($i=1;$i<=$row['number'];$i++){
				$sql1="update bf_km set out_trade_no=:out_trade_no,trade_no=:trade_no,endtime=:endtime,user=:user,status=:status,gName=:gName where goodsid = :goodsid and status=0 limit  1";
				$res1 = $dbh->prepare($sql1);
				$res1->bindValue(":goodsid",$row['goodsid']);
				$res1->bindValue(":out_trade_no",$row['out_trade_no']);
				$res1->bindValue(":trade_no",$trade_no);
				$res1->bindValue(":endtime",time());
				$res1->bindValue(":user",$row['user']);
				$res1->bindValue(":gName",$row['gName']);
				$res1->bindValue(":status",1);
				$res1->execute();
			}
			$sql2="update bf_order set trade_no=:trade_no,endTime=:endTime,status=:status where out_trade_no = :out_trade_no";
			$res2 = $dbh->prepare($sql2);
			$res2->bindValue(":out_trade_no",$row['out_trade_no']);
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

	echo "<script>window.location.href='".curPageURL()."/query?on=".$out_trade_no."';</script>";
   
}
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
            <p>正在为您提取卡密，请耐心等候</p>
            <p>将为在2~10秒内完成</p>
        </div>
    </div>
    <script src="https://cdn.staticfile.org/jquery/1.12.4/jquery.min.js"></script>
      </div>
</body>
</html>