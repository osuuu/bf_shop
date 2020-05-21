<?php
include("../api.inc.php");

$out_trade_no = isset($_GET['out_trade_no']) ? daddslashes($_GET['out_trade_no']) : exit('No out_trade_no!');

@header('Content-Type: text/html; charset=UTF-8');

$res = $dbh->prepare("select * from bf_order where out_trade_no = $out_trade_no");
$res->execute();

$row = $res->fetch(PDO::FETCH_ASSOC);
$return=curPageURL().'/includes/pay/return.php?out_trade_no='.$out_trade_no;
if ($row['status'] >= 1) {
    exit('{"code":1,"msg":"付款成功","backurl":"' . $return . '"}');
} else {
    exit('{"code":-1,"msg":"未付款"}');
}
?>