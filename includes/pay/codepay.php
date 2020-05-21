<?php

include("../api.inc.php");
@header('Content-Type: text/html; charset=UTF-8');
$type = isset($_GET['type']) ? $_GET['type'] : exit('No type!');
if ($type == 'alipay') {
    $type = 1;
} elseif ($type == 'qqpay') {
    $type = 2;
} else {
    $type = 3;
}

$or = daddslashes($_GET['out_trade_no']);

$res1 = $dbh->prepare("select * from bf_order where out_trade_no = $or");
$res1->execute();

$row1 = $res1->rowCount();

$row2 = $res1->fetch(PDO::FETCH_ASSOC);

if ($row1==0 || $row2['money'] != $_GET['money'] || $row2['status']==1) {
   	sysmsg('验证失败');
	exit();
}
//error_reporting(E_ALL & ~E_NOTICE); //过滤脚本提醒
date_default_timezone_set('PRC'); //时区设置 解决某些机器报错
#支付宝接口配置
$codepay_config['id'] = $conf['codeid'];
$codepay_config['key'] = $conf['codekey'];
//字符编码格式 目前支持 gbk GB2312 或 utf-8 保证跟文档编码一致 建议使用utf-8
$codepay_config['chart'] = strtolower('utf-8');

//是否启用免挂机模式 1为启用. 未开通请勿更改否则资金无法及时到账
$codepay_config['act'] = "0"; //认证版则开启 一般情况都为0


// if ((int)$codepay_config['id'] < 1){
//   	sysmsg('请先到后台配置ID跟密钥');
// 	exit();	
// } 

/**订单支付页面显示方式
 * 1: GET框架云端支付 (简单 兼容性强 自动升级 1分钟可集成)
 * 2: POST表单到云端支付 (简单 兼容性强 自动升级)
 * 3：自定义开发模式 (默认 复杂 需要一定开发能力 手动升级 html/codepay_diy_order.php修改收银台代码)
 * 4：高级模式(复杂 需要较强的开发能力 手动升级 html/codepay_supper_order.php修改收银台代码)
 */
$codepay_config['page'] = 4; //支付页面展示方式

//支付页面风格样式 仅针对$codepay_config['page'] 参数为 1或2 才会有用。
$codepay_config['style'] = 1; //暂时保留的功能 后期会生效 留意官网发布的风格编号


//二维码超时设置  单位：秒
$codepay_config['outTime'] = 360;//360秒=6分钟 最小值60  不建议太长 否则会影响其他人支付

//最低金额限制
$codepay_config['min'] = 0.01;


$codepay_config['return_url'] = curPageURL().'/includes/pay/codepay_return.php';
$codepay_config['notify_url'] = curPageURL().'/includes/pay/codepay_notify.php';

//获取客户端IP地址
function getIp(){ //取IP函数
    static $realip;
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } else {
            if (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
    }
    return $realip;
}

$pay_id = getIp();
$param = $or;
if ($type <= 0) $type = 3;

$price = $row2['money'];

if ($conf['qrcode'] == '1') $codepay_config["qrcode_url"] = "./codepay/qrcode.php";

$codepay_config['pay_type'] = 1;

if ($codepay_config['pay_type'] == 1 && $type == 1) $codepay_config["qrcode_url"] = '';

$data = array(
    "id" => (int)$codepay_config['id'],//平台ID号
    "type" => $type,//支付方式
    "price" => (float)$price,//原价
    "pay_id" => $pay_id, //可以是用户ID,站内商户订单号,用户名
    "param" => $param,//自定义参数
//            "https" => 1,//启用HTTPS
    "act" => (int)$codepay_config['act'],
    "outTime" => (int)$codepay_config['outTime'],//二维码超时设置
    "page" => (int)$codepay_config['page'],//付款页面展示方式
    "return_url" => $codepay_config["return_url"],//付款后附带加密参数跳转到该页面
    "notify_url" => $codepay_config["notify_url"],//付款后通知该页面处理业务
    "style" => (int)$codepay_config['style'],//付款页面风格
    "user_ip"=>getIp(),
    "pay_type" => $codepay_config['pay_type'],//支付宝使用官方接口
    "qrcode_url" => $codepay_config['qrcode_url'],//本地化二维码
    "chart" => trim(strtolower($codepay_config['chart']))//字符编码方式
    //其他业务参数根据在线开发文档，添加参数.文档地址:https://codepay.fateqq.com/apiword/
    //如"参数名"=>"参数值"
);

function create_link($params,$codepay_key,$host=""){
    ksort($params); //重新排序$data数组
    reset($params); //内部指针指向数组中的第一个元素
    $sign = '';
    $urls = '';
    foreach ($params AS $key => $val) {
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

    $key = md5($sign . $codepay_key);//替换为自己的密钥
    $query = $urls . '&sign=' . $key; //创建订单所需的参数
    $apiHost=$host?$host:"http://api2.fateqq.com:52888/creat_order/?";
    $url = $apiHost.$query; //支付页面
    return array("url"=>$url,"query"=>$query,"sign"=>$sign,"param"=>$urls);
}
$back=create_link($data,$codepay_config['key']);


switch ((int)$type) {
    case 1:
        $typeName = '支付宝';
        break;
    case 2:
        $typeName = 'QQ';
        break;
    default:
        $typeName = '微信';
}
$user_data = array("return_url" => $codepay_config["return_url"],
    "type" => $type, "outTime" => $codepay_config["outTime"], "codePay_id" => $codepay_config["id"]);


$user_data["qrcode_url"] = $codepay_config["qrcode_url"];

//中间那log 默认为10秒后隐藏
//改为自己的替换img目录下的use_开头的图片 你要保证你的二维码遮挡不会影响扫码
//二维码容错率决定你能遮挡多少部分
$user_data["logShowTime"] = 1;
/**
 * 高级模式 云端创建订单。(注意不要外泄密钥key)
 * 可自行根据订单返回的参数做一些高级功能。 以下demo只是简单的功能 其他需要自行开发
 * 比如根据money type 参数调用本地的二维码图片。
 * 比如根据云端订单状态创建失败 展示自定义转账的二维码。
 * 比如可自行开发付款后的同步通知实现。
 * 比如可自行开发软件端某个支付方式掉线。 自动停用该付款方式。
 * 如使用云端同步通知  请附带必要的参数 码支付的用户id,pay_id,type,money,order_id,tag,notiry_key
 * 必须将notiry_key参数返回 因为该参数为服务解密参数(会随时变化)。否则影响云端同步通知
 */


if (function_exists('file_get_contents')) {
    $codepay_json = file_get_contents($back['url']);
} else {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $back['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $codepay_json = curl_exec($ch);
    curl_close($ch);
}

if(empty($codepay_json)){
    $data['call']="callback";
    $data['page']="3";
    $back=create_link($data,$codepay_config['key']);
    $codepay_html='<script src="'.$back['url'].'"></script>';
    
}else{
    $codepay_data = json_decode($codepay_json);
    $qr = $codepay_data ? $codepay_data->qrcode : '';
    $codepay_html="<script>callback({$codepay_json})</script>";
}

if(!is_dir('./codepay')){ //如果存在这个文件 表示codepay目录上传 使用本地资源否则用远程资源
    $codepay_path="https://codepay.fateqq.com";
}else{
    $codepay_path="../codepay";
}

?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $codepay_config['chart'] ?>">
    <meta http-equiv="Content-Language" content="zh-cn">
    <meta name="apple-mobile-web-app-capable" content="no"/>
    <meta name="apple-touch-fullscreen" content="yes"/>
    <meta name="format-detection" content="telephone=no,email=no"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="white">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title><?php echo $title['name'] ?>扫码支付 - 码支付</title>
    <link href="./codepay/css/wechat_pay.css" rel="stylesheet" media="screen">

</head>

<body>
<div class="body">
    <h1 class="mod-title">
        <span class="ico_log ico-<?php echo $type ?>"></span>
    </h1>

    <div class="mod-ct">
        <div class="order">
        </div>
        <div class="amount" id="money">￥<?php echo $price ?></div>
        <div class="qrcode-img-wrapper" data-role="qrPayImgWrapper">
            <div data-role="qrPayImg" class="qrcode-img-area">
                <div class="ui-loading qrcode-loading" data-role="qrPayImgLoading" style="display: none;">点击重新加载</div>
                <div style="position: relative;display: inline-block;">
                    <img id='show_qrcode' alt="加载中..." src="<?php echo $qr ?>" width="210" height="210" style="display: block;">
                   
                    <img onclick="$('#use').hide()" id="use"
                         src="./codepay/img/use_<?php echo $type ?>.png"
                         style="position: absolute;top: 50%;left: 50%;width:32px;height:32px;margin-left: -21px;margin-top: -21px">
                </div>
            </div>


        </div>
        <div class="time-item" id="msg">
            <h1>二维码过期时间</h1>
            <strong id="hour_show">0时</strong>
            <strong id="minute_show">0分</strong>
            <strong id="second_show">0秒</strong>
            <br><br><font color="red">请务必付款与提示金额相同的金额 否则无效</font>
        </div>

        <div class="tip">
            <div class="ico-scan"></div>
            <div class="tip-text">
                <p>请使用<?php echo $typeName ?>扫一扫</p>
                <p>扫描二维码完成支付</p>
            </div>
           
        </div>

        <div class="detail" id="orderDetail">
            <dl class="detail-ct" id="desc" style="display: none;">

                <dt>状态</dt>
                <dd id="createTime">订单创建</dd>

            </dl>
            <a href="javascript:void(0)" class="arrow"><i class="ico-arrow"></i></a>
        </div>

        <div class="tip-text">
        </div>


    </div>
    <div class="foot">
        <div class="inner">
            <p>手机用户可保存上方二维码到手机中</p>
            <p>在<?php echo $typeName ?>扫一扫中选择“相册”即可</p>
        </div>
    </div>

</div>
<div class="copyRight">
    <p>支付合作：<a href="http://codepay.fateqq.com/" target="_blank">码支付</a></p>
</div>

<!--注意下面加载顺序 顺序错乱会影响业务-->
<script src="./codepay/js/jquery-1.10.2.min.js"></script>

<script>
    var user_data =<?php echo json_encode($user_data);?>
</script>
<script src="./codepay/js/notify.js"></script>
<script src="./codepay/js/codepay_util.js"></script>
<?php echo $codepay_html;?>
<script>
    setTimeout(function () {
        $('#use').hide()
    }, user_data.logShowTime || 10000)
</script>
</body>
</html>
