<?php
include("../api.inc.php");


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
    exit('fail');
} else { //合法的数据
    $trade_no = $_POST['pay_no'];
    $out_trade_no = $_POST['param'];
    
	$res = $dbh->prepare("select * from bf_order where out_trade_no = $out_trade_no limit  1");
	$res->execute();
	$row = $res->fetch(PDO::FETCH_ASSOC);

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
    exit('success');
}
?>