<?php
$config = array (
	//签名方式,默认为RSA2(RSA2048)
	'sign_type' => "RSA2",

	//支付宝公钥
	'alipay_public_key' => $conf['f2f_public'],

	//商户私钥
	'merchant_private_key' => $conf['f2f_private'],

	//编码格式
	'charset' => "UTF-8",

	//支付宝网关
	'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

	//应用ID
	'app_id' => $conf['f2f_appid'],

	//异步通知地址,只有扫码支付预下单可用
	'notify_url' => curPageURL().'/includes/pay/f2fpay_notify.php',

	//最大查询重试次数
	'MaxQueryRetry' => "10",

	//查询间隔
	'QueryDuration' => "3"
);