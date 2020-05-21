<?php
include("../api.inc.php");
include("../class/class.phpmailer.php");
include("../class/class.smtp.php");
$key=$_GET['key'];
$rece=$_GET['rece'];
$flag=$_GET['flag'];
$out_trade_no=$_GET['out_trade_no'];
$site=$title['name'];
$goodsid=$_GET['gid'];
if($key != $title['monit']){
	$result = array("status" => 100, "message" => "发送失败,key错误", "data" =>null);
}else{

	
	if($flag==1){
		$title= $site.'发货提醒';
		$res = $dbh->prepare("select * from bf_km where out_trade_no=$out_trade_no limit  1");
		$res->execute();
		$row = $res->fetch(PDO::FETCH_ASSOC);
		$content='<p style="font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 0px;">尊敬的'.$row['user'].'用户，您好！</p>
                    <p class="cTMail-content"
                       style="font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 0px 0px 36px; word-wrap: break-word; word-break: break-all;">
                        您购买的'.$row['gName'].'商品已发货成功，如未显示完全请点击下方订单详细自行查询
                       <br><strong>商户单号：</strong>'.$out_trade_no.'
                        <br><strong>购买时间：</strong>'.date("Y-m-d H:i:s", $row['endtime']).'
                        <br><strong>卡密内容：</strong>'.$row['km'].'
                    </p>
                   <a id="cTMail-btn" href="'.curPageURL().'/query?on='.$out_trade_no.'" style="font-size: 16px; line-height: 45px; display: block; background-color: rgb(0, 164, 255); color: rgb(255, 255, 255); text-align: center; text-decoration: none; margin-top: 28px; margin-bottom: 36px; border-radius: 3px;">查看订单详细</a>';
		
	}else if($flag==2){
		$title= $site.'库存不足提醒';
		$res = $dbh->prepare("select * from bf_km where goodsid =$goodsid AND status=0");
		$res->execute();
		$row = $res->rowCount();
		$res1 = $dbh->prepare("select * from bf_goods where id =$goodsid");
		$res1->execute();
		$row1 = $res->fetch(PDO::FETCH_ASSOC);
		$content='<p style="font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 0px;">尊敬的管理员，您好！</p>
                    <p class="cTMail-content"
                       style="font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 0px 0px 36px; word-wrap: break-word; word-break: break-all;">
                        您的商品'.$row1['name'].'库存卡密仅剩'.$row.'件，请及时补货。</p>
                   <a id="cTMail-btn" href="'.curPageURL().'/query?on='.$out_trade_no.'" style="font-size: 16px; line-height: 45px; display: block; background-color: rgb(0, 164, 255); color: rgb(255, 255, 255); text-align: center; text-decoration: none; margin-top: 28px; margin-bottom: 36px; border-radius: 3px;">查看订单状态</a>';
		
	}else if($flag==3){
		$title= $site.'客户下单提醒';
		$res = $dbh->prepare("select * from bf_order where out_trade_no=$out_trade_no");
		$res->execute();
		$row = $res->fetch(PDO::FETCH_ASSOC);
			$content='<p style="font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 0px;">尊敬的管理员，您好！</p>
                    <p class="cTMail-content"
                       style="font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 0px 0px 36px; word-wrap: break-word; word-break: break-all;">
                        用户'.$row['user'].'购买了'.$row['gName'].'商品'.$row['number'].'个。
                       <br><strong>商户单号：</strong>'.$out_trade_no.'
                        <br><strong>购买时间：</strong>'.date("Y-m-d H:i:s", $row['endTime']).'
                    </p>';
	}else{
		$title= $site.'邮箱对接成功';
		$content='<p style="font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 0px;">尊敬的管理员，您好！</p>
                    <p class="cTMail-content"
                       style="font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 0px 0px 36px; word-wrap: break-word; word-break: break-all;">
                       当您看到这条邮件的时候说明您的邮箱已经对接好了。
                       </p>';
	}

$html='<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <style>
        .open_email {
            background: url(http://imgcache.qq.com/bossweb/pay/images/mailmsg/email_bg.png) no-repeat 0 -35px;
            width: 760px;
            padding: 10px;
            font-family: Tahoma, "宋体";
            margin: 0 auto;
            margin-bottom: 20px;
            text-align: left;
        }

        .open_email a:link, .open_email a:visited {
            color: #295394;
            text-decoration: none !important;
        }

        .open_email a:active, .open_email a:hover {
            color: #000;
            text-decoration: underline !important;
        }

        .open_email h5, .open_email h6 {
            font-size: 14px;
            margin: 0;
            padding-top: 2px;
            line-height: 21px;
        }

        .open_email h5 {
            color: #df0202;
            padding-bottom: 10px;
        }

        .open_email h6 {
            padding-bottom: 2px;
        }

        .open_email h5 span, .open_email p {
            font-size: 12px;
            color: #808080;
            font-weight: normal;
            margin: 0;
            padding: 0;
            line-height: 21px;
        }
    </style>
    <title></title>
</head>
<body>
<div align="center">
    <div class="open_email" style="margin-left: 8px; margin-top: 8px; margin-bottom: 8px; margin-right: 8px;">
        <div>
            <span class="genEmailNicker"> </span>
            <br/>
            <span class="genEmailContent">
      <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
      <div style="box-sizing:border-box;text-align:center;min-width:320px; max-width:660px; border:1px solid #f6f6f6; background-color:#f7f8fa; margin:auto; padding:20px 0 30px;">
       <div class="main-content" style="">
        <table style="width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse">
         <tbody>
          <tr style="font-weight:300">
           <td style="width:3%;max-width:30px;"></td>
           <td style="max-width:600px;">
             <p
                     style="height:2px;background-color: #00a4ff;border: 0;font-size:0;padding:0;width:100%;margin-top:20px;"></p>
            <div id="cTMail-inner"
                 style="background-color:#fff; padding:23px 0 20px;box-shadow: 0px 1px 1px 0px rgba(122, 55, 55, 0.2);text-align:left;">
             <table style="width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse;text-align:left;">
              <tbody>
               <tr style="font-weight:300">
                <td style="width:3.2%;max-width:30px;"></td>
                <td style="max-width:480px;text-align:left;">
                    <h1 style="font-weight:bold;font-size:20px; line-height:36px; margin:0 0 16px;">'.$title.'</h1>
                    '.$content.'
                    <dl style="font-size:14px;color:#333; line-height:18px;"></dl>
                    <p id="cTMail-sender"
                       style="color:#333;font-size:14px; line-height:26px; word-wrap:break-word; word-break:break-all;margin-top:32px;">
                        此致
                        <br/>
                        <strong>'.$site.'团队</strong>
                    </p>
                </td>
                <td style="width:3.2%;max-width:30px;"></td>
               </tr>
              </tbody>
             </table>
            </div>
            <div id="cTMail-copy" style="text-align:center; font-size:12px; line-height:18px; color:#999">
             <table style="width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse">
              <tbody>
               <tr style="font-weight:300">
                <td style="width:3.2%;max-width:30px;"></td>
                <td style="max-width:540px;"> <p
                        style="text-align:center; margin:20px auto 14px auto;font-size:12px;color:#999;">此为系统邮件，请勿回复。</p> </td>
                <td style="width:3.2%;max-width:30px;"></td>
               </tr>
              </tbody>
             </table>
            </div> </td>
           <td style="width:3%;max-width:30px;"></td>
          </tr>
         </tbody>
        </table>
       </div>
      </div>
            </span>
            <br/>
            <span class="genEmailTail"> </span>
        </div>
    </div>
</div>
</body>
</html>';

	$ress = $dbh->prepare("select * from bf_email where id=1");
	$ress->execute();
	$rows = $ress->fetch(PDO::FETCH_ASSOC);

	$smtp = $rows['mail_stmp']; //必填，设置SMTP服务器 QQ邮箱是smtp.qq.com ，QQ邮箱默认未开启，请在邮箱里设置开通。网易的是 smtp.163.com 或 smtp.126.com
	$mail_usr =  $rows['mail_usr']; // 必填，开通SMTP服务的邮箱；也就是发件人Email。
	$mail_pwd = $rows['mail_pwd']; //必填， 以上邮箱对应的密码
	$ymail = $rece; //收信人的邮箱地址，也就是你自己收邮件的邮箱
	$yname = "BF发卡网"; //收件人称呼
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->Host = $smtp;
	$mail->Username = $mail_usr;
	$mail->Password = $mail_pwd; //必填， 以上邮箱对应的密码
	$mail->From = $mail_usr;
	$mail->FromName = $site;
	$mail->AddAddress($ymail, $yname);
	$mail->Subject = $title;
	date_default_timezone_set('Asia/Shanghai');
	$time = date("Y-m-d H:i:s", time());
	$mail->MsgHTML($html);
	$mail->IsHTML(true);
	if (!$mail->Send()) {
		//header("Content-Type: text/html; charset=utf-8");
		$result = array("status" => 100, "message" => "发送失败", "data" => null); 
	} else {
		//header("Content-Type: text/html; charset=utf-8");
		$result = array("status" => 200, "message" => "发送成功", "data" => null); 
	}

	
}
	



echo(json_encode($result));
?>
